#!/bin/bash
# KBC아카데미 자동 백업
# - 매일: DB 전체, 업로드 파일(강사 사진), .env 2개
# - 일요일(또는 FULL=1): 코드 전체 (vendor, 캐시 제외)
# - KEEP_DAYS 일이 지난 백업은 삭제
#
# 서버 설치: sudo install -o root -g root -m 755 scripts/backup.sh /usr/local/sbin/kbc-backup
# 실행 예약: /etc/cron.d/kbc (docs/ops/cron.kbc 참고)
set -euo pipefail

KBC_DIR="${KBC_DIR:-/var/www/kbc}"
DEST="${DEST:-/var/www/backups/kbc_auto}"
DB_NAME="${DB_NAME:-kbc_db}"
KEEP_DAYS="${KEEP_DAYS:-14}"
TS="$(date +%Y%m%d_%H%M%S)"

umask 077
mkdir -p "$DEST"
chmod 700 "$DEST"

log() { echo "$(date '+%Y-%m-%d %H:%M:%S') $*"; }

# 1) DB (서비스를 멈추지 않는 방식)
mysqldump --single-transaction --routines --triggers --events --databases "$DB_NAME" | gzip > "$DEST/db_$TS.sql.gz"

# 2) 업로드 파일 + 설정(.env)
tar -czf "$DEST/files_$TS.tar.gz" -C "$KBC_DIR" user/public/uploads user/.env admin/.env

# 3) 코드 전체 (주 1회)
if [ "$(date +%u)" = "7" ] || [ "${FULL:-0}" = "1" ]; then
    tar -czf "$DEST/code_$TS.tar.gz" \
        --exclude='*/vendor' --exclude='*/writable/cache/*' --exclude='*/writable/session/*' --exclude='*/writable/logs/*' \
        -C "$(dirname "$KBC_DIR")" "$(basename "$KBC_DIR")"
fi

# 4) 압축 파일이 온전한지 확인
for f in "$DEST"/*_"$TS".*.gz; do
    gzip -t "$f"
done

# 5) 오래된 백업 삭제
find "$DEST" -maxdepth 1 -type f \( -name 'db_*.sql.gz' -o -name 'files_*.tar.gz' -o -name 'code_*.tar.gz' \) -mtime +"$KEEP_DAYS" -delete

log "백업 완료 $TS ($(du -sh "$DEST" | cut -f1) 사용 중)"
