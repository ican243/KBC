#!/bin/bash
# KBC아카데미 로그 정리
# - 오류 로그(log-날짜.log): LOG_DAYS 일 보관
# - 관리자 열람 기록(audit-날짜.log): AUDIT_DAYS 일 보관 (개인정보 열람 기록)
# - 스팸 방지용 임시 기록(캐시): 1일 지난 것 삭제
#
# 서버 설치: sudo install -o root -g root -m 755 scripts/cleanup-logs.sh /usr/local/sbin/kbc-cleanup-logs
set -euo pipefail

KBC_DIR="${KBC_DIR:-/var/www/kbc}"
LOG_DAYS="${LOG_DAYS:-30}"
AUDIT_DAYS="${AUDIT_DAYS:-365}"

for app in user admin; do
    find "$KBC_DIR/$app/writable/logs" -maxdepth 1 -type f -name 'log-*.log' -mtime +"$LOG_DAYS" -delete
    find "$KBC_DIR/$app/writable/logs" -maxdepth 1 -type f -name 'audit-*.log' -mtime +"$AUDIT_DAYS" -delete
done
find "$KBC_DIR/user/writable/cache" -maxdepth 1 -type f \( -name 'kbc_dup_*' -o -name 'throttler_kbc_rate_*' \) -mtime +1 -delete

echo "$(date '+%Y-%m-%d %H:%M:%S') 로그 정리 완료"
