# KBC아카데미 홈페이지

공개 홈페이지(user)와 관리자(admin)를 별도 CodeIgniter4 프로젝트로 분리해 운영한다.

## 구조

```
kbc/
├─ user/       공개 홈페이지 (CodeIgniter4)
├─ admin/      관리자 (CodeIgniter4)
├─ database/   DB 스키마
└─ docs/       서버 설정 및 인수인계 문서
```

## 개발 환경

| 항목 | 값 |
|---|---|
| PHP | 8.3 |
| CodeIgniter | 4.7 |
| DB | MySQL 8.0 / kbc_db (utf8mb4) |
| 공개 홈페이지 | https://kir1.cafe24.com/kbc/ (개발용 하위경로, Basic Auth) |
| 관리자 | https://kir1.cafe24.com/kbc/admin/ (개발용 하위경로, Basic Auth + 로그인 + 2단계 인증) |

## 설치

```bash
cd user  && composer install && cp env .env
cd admin && composer install && cp env .env
```

각 `.env`에 `app.baseURL`과 DB 접속 정보를 입력한다.
DB 계정은 user/admin이 서로 다른 계정을 사용한다.

- user    : kbc_user    (콘텐츠 테이블 조회 + 문의 테이블 등록만 가능, 문의 조회 불가)
- admin   : kbc_admin   (조회/등록/수정/삭제)
- migrate : kbc_migrate (테이블 생성/변경, 마이그레이션 실행 시에만 사용)

## 공개 홈페이지 페이지 (user)

| 주소 | 내용 |
|---|---|
| / | 홈 (요약 + 자세히 보기 링크, 하단 상담 폼) |
| /about | 아카데미 소개 (교육 목표, 강남 협업 모델, 교육 분야, 실습·평가 흐름) |
| /career | 진로 연계 (추진 중 안내, 연계 절차, 개인정보 보호) |
| /courses, /courses/{slug} | 교육과정 비교표, 과정 상세 (기획안 4장 순서) |
| /instructors | 강사진 (동의한 강사만 실명·사진·프로필·경력) |
| /news, /news/{id} | 설명회 일정, 공지 목록(10건씩)·상세 |
| /faq | 자주 묻는 질문 (분류별) |
| /contact | 상담 신청 (`?course=번호` 로 과정 미리 선택) |
| /partner, /privacy | 협력 제안, 개인정보 처리방침 |

- 상단 메뉴 5개(아카데미·교육과정·진로 연계·소식·상담 문의), 휴대폰은 ☰ 메뉴 (`assets/js/site.js`)
- 상담 폼은 `Views/partials/consult_form.php` 하나를 홈과 /contact 가 같이 사용 (오류 시 보낸 페이지로 복귀)
- 비공개 과정·예약/비공개 공지·없는 주소는 404
- 공개 문구 규칙 (기획안 8장): "취업 보장", "수료 즉시 취업", 소득 금액 표현 사용 안 함. 공개 용어는 "협업형 라이브 방송".
  진로 연계는 "연계·설명회·면접 기회"까지만 표현 (직업소개 신고·등록 요건 검토 전). 소개·진로 문구는 `Views/pages/about.php`, `career.php`

## 검색·공유 설정 (user)

- 모든 페이지: 제목, 설명, 대표 URL(canonical), 카카오톡·SNS 공유 태그(og:*, twitter:*), 기관 정보(JSON-LD) — `Views/layouts/main.php`
- 공유 이미지 `public/og-image.png` (1200×630), 아이콘 `favicon.ico`·`favicon.svg`·`apple-touch-icon.png` (로고 확정 시 교체)
- `/robots.txt`, `/sitemap.xml` (`Controllers/Seo.php`): 개발 모드는 수집 차단, 운영 모드(`CI_ENVIRONMENT = production`)는 허용 + 사이트맵
  → robots.txt 는 도메인 맨 앞에 있어야 효과가 있으므로 도메인 연결 후 적용
- 오류 화면: 404 `error_404.php`, 400 `error_400.php`, 서버 오류 `production.php` (운영 모드, 원인은 서버 로그에만)
- 교습비 안내 문구(`fee_notice`)·반환 기준(`refund_policy`)은 교육과정 목록·상세에 표시 (반환 기준이 비어 있으면 숨김)
- 속도 (nginx snippet): CSS·JS·SVG 압축 전송, 정적 파일 7일 캐시 (`?v=` 로 갱신), PHP 화면은 캐시 안 함

## 공개 홈페이지 화면 구조 (user)

```
user/app/Views/
├─ layouts/main.php     공통 틀 (헤더, 푸터, 모바일 하단 상담 버튼)
└─ home/
   ├─ index.php         섹션 순서
   ├─ hero.php          첫 화면
   ├─ fields.php        방송 분야
   ├─ courses.php       교육과정 (courses)
   ├─ crew.php          강사진 (instructors)
   ├─ steps.php         교육 흐름
   ├─ career.php        진로 연계
   ├─ news.php          설명회·공지 (events, notices)
   └─ contact.php       상담 신청

user/public/assets/
├─ css/tokens.css       색·글꼴·모서리 설정값 (분위기 변경 시 이 파일만 수정)
├─ css/site.css         화면 스타일
├─ js/site.js           헤더 전환, 스크롤 효과
└─ fonts/pretendard/    Pretendard 글꼴 (SIL OFL, LICENSE.txt 포함)
```

화면 표시 규칙
- 과정 `is_confirmed = 0` 이면 "시간표·교습비 확정 전" 표시, 교습비 숨김
- 강사 `consent_at` 이 없으면 실명·사진·경력 숨김
- 사이트 설정 `operator_name` 이 비어 있으면 푸터에 "준비 중" 표시, 연락처는 값이 있을 때만 표시
- 공지는 게시일이 지난 것만, 설명회는 앞으로 열릴 것만 표시

디자인 시안 원본은 `docs/mockup/`.

## 문의 접수 (user)

| 주소 | 내용 |
|---|---|
| POST /consult | 교육 상담 신청 (홈 하단 폼) |
| GET /consult/done | 상담 접수 완료 (접수번호는 접수 직후 한 번만 표시) |
| GET, POST /partner | 기관 협력 제안 (교육장 / 강사 / 채용) |
| GET /partner/done | 협력 제안 접수 완료 |

처리 순서: CSRF → 봇 검사 → 입력값 검사 → IP 제한 → 연락처 중복 → 접수번호 발급·저장 → 완료 화면

- 접수번호: `C`(상담) / `P`(협력) + 날짜 + 5자리 (`app/Libraries/ReceiptNumber.php`)
- 스팸 방지 (`app/Libraries/SpamGuard.php`)
  - 숨김 칸(website)이 채워지거나 폼을 연 지 3초 안에 제출하면 봇으로 보고 저장하지 않음 (완료 화면은 동일하게 표시)
  - 같은 IP 10분에 3회, 같은 연락처 10분 내 재접수 제한 (writable/cache 에 기록, 연락처는 해시로 저장)
- 유입 경로: utm 값 또는 외부 유입 사이트 주소를 `source` 에 기록 (`assets/js/site.js`)
- 연락처는 `010-1234-5678` 형식으로 통일해 저장
- 신청한 화면(`entry_page`: home / contact / courses/주소이름)을 함께 저장 → 통계의 과정 페이지 전환
- 조회수: `app/Filters/PageView.php` 가 정상 표시된 공개 HTML 페이지(GET)만 +1. 검색엔진·미리보기 로봇, 404, POST 제외. 쿠키 미사용이라 방문자 수가 아닌 조회수

## 운영자 메일 알림 (user)

- 접수 완료 화면을 먼저 보낸 뒤 발송 (`app/Libraries/InquiryNotifier.php`), 실패해도 접수에는 영향 없음
- 받는 주소: DB `site_settings.notify_email`
- 보내는 계정: `user/.env` 의 `email.*` (네이버 SMTP 465/SSL, `email.SMTPPass` 는 애플리케이션 비밀번호)
- 메일에는 접수번호, 가린 이름(홍*동), 과정/기관·유형, 접수 시각만 포함. 연락처·문의내용은 관리자 화면에서 확인
- 발송 결과(sent/failed, 원인)는 `notification_logs` 에 기록

## 개인정보 안내 (user)

- 처리방침: `/privacy` (`app/Views/pages/privacy.php`) — 공개 전 법무 검토 필요
- 폼 동의 안내: `app/Views/partials/consent_notice.php` (내용 보기)
- 운영 주체, 보유 기간(`inquiry_retention`), 위탁(`privacy_entrusted`), 보호책임자(`privacy_officer`), 시행일(`privacy_effective_date`)은 `site_settings` 값을 사용하며, 비어 있으면 "준비 중"으로 표시

## 관리자 문의 관리 (admin)

| 주소 | 내용 |
|---|---|
| / | 대시보드 (신규 수, 오늘 접수, 메일 실패, 최근 문의) |
| /consults, /partners | 목록 (상태·과정/유형·기간 필터, 이름·연락처·접수번호 검색, 20건씩) |
| /consults/{id}, /partners/{id} | 상세 + 처리(상태·담당자·메모) + 처리 이력 + 삭제 처리 |
| /consults/export, /partners/export | 현재 필터 결과 CSV (UTF-8 BOM, 엑셀 수식 실행 방지) |
| /notifications | 메일 알림 발송 기록 |
| /stats | 통계 (상담·협력 접수, 상담완료 비율, 과정 페이지 → 상담 전환, 날짜별·과정별·유입 경로별·신청 화면별, 많이 본 페이지) |

- 상태: 신규(new) / 연락(contacted) / 상담완료(done) / 보류(hold) — `app/Libraries/InquiryStatus.php`
- 처리할 때마다 `inquiry_memos` 에 이력 기록 (변경 전·후 상태, 담당자, 메모)
- 삭제는 `deleted_at` 표시만 (목록에서 제외, DB 에 보관)
- 유입 경로는 읽기 쉬운 이름으로 표시·필터 (`app/Libraries/SourceLabel.php`, 예: `ref:m.search.naver.com` → 네이버 검색)
- 상세 열람·CSV 내려받기·삭제는 `writable/logs/audit-날짜.log` 에 기록

## PHP-FPM (서버)

KBC 는 다른 사이트와 분리된 전용 pool 을 사용한다 (`docs/php-fpm/kbc.conf` → `/etc/php/8.3/fpm/pool.d/kbc.conf`).
- 소켓 `/run/php/php8.3-fpm-kbc.sock` (nginx snippet 의 fastcgi_pass)
- 업로드 6M / 요청 8M (관리자 경로 nginx `client_max_body_size 8m`)
- `open_basedir = /var/www/kbc/:/tmp/` — KBC 폴더 밖 파일 접근 차단

## 운영 (백업·로그·문의 파기)

| 작업 | 파일 | 서버 설치 위치 | 실행 |
|---|---|---|---|
| 자동 백업 (DB·업로드·.env 매일, 코드 주 1회, 14일 보관) | `scripts/backup.sh` | `/usr/local/sbin/kbc-backup` | 매일 03:30 (한국) |
| 로그 정리 (오류 30일, 열람 기록 1년) | `scripts/cleanup-logs.sh` | `/usr/local/sbin/kbc-cleanup-logs` | 매일 04:00 (한국) |
| 문의 자동 파기 | `admin/app/Commands/InquiryPurge.php` | `php spark inquiry:purge [--dry-run]` | 보존 기간 확정 후 예약 |

- 예약: `docs/ops/cron.kbc` → `/etc/cron.d/kbc`
- 실행 파일은 root 만 고칠 수 있는 위치에 설치한다 (웹 계정이 고칠 수 있는 폴더의 파일을 root 로 실행하지 않기 위해)
- 백업 위치: `/var/www/backups/kbc_auto/` (서버 밖 보관 위치는 회사 결정 필요)
- 공개 전 점검 결과·검수 문서: `docs/qa/`

## DB 마이그레이션

마이그레이션은 admin 프로젝트에서만 관리한다.

```bash
cd admin
env database.defaultGroup=migrate php spark migrate
```

초기 콘텐츠(과정 3개, 강사 분야 5개, 사이트 설정 항목) 입력:

```bash
cd admin
php spark db:seed InitialContentSeeder
```

이미 데이터가 있는 테이블은 건너뛴다. DB 구조 참고용 파일은 `database/schema.sql`.

## 테이블

| 테이블 | 용도 |
|---|---|
| admins | 관리자 계정 |
| courses | 교육과정 |
| instructors | 강사진 |
| notices | 소식·공지 |
| events | 설명회 일정 |
| faqs | 자주 묻는 질문 |
| site_settings | 사이트 설정 (연락처, 등록정보 등) |
| consult_inquiries | 교육 상담 신청 |
| partner_inquiries | 기관 협력 제안 |
| inquiry_memos | 문의 메모·상태 이력 |
| notification_logs | 알림 발송 기록 |
| page_views | 공개 페이지 조회수 (날짜·페이지·숫자만, 개인정보 없음) |

## 홈페이지 콘텐츠 관리 (admin)

| 주소 | 내용 |
|---|---|
| /courses | 교육과정 (확정 여부·교습비, 순서, 공개) — 상담 기록이 참조하므로 삭제 불가, 비공개만 |
| /instructors | 강사진 (사진 업로드, 게시 동의 체크 후에만 실명·사진·경력 공개) |
| /notices | 공지 (게시일이 지나야 홈페이지에 표시 → 예약 게시) |
| /events | 설명회 일정 (지난 일정은 홈페이지에서 자동 제외) |
| /faqs | 자주 묻는 질문 (분류, 순서) |
| /settings | 사이트 설정 18개 (안내 문구, 운영 정보, 개인정보, 알림 이메일) |

- 공통 처리: `app/Controllers/ContentController.php` (목록·등록·수정·공개 전환·순서·삭제), 메뉴별 컨트롤러는 입력 규칙만 정의
- 공지·FAQ 내용은 일반 글자만 저장 (HTML 에디터 없음)
- 강사 사진 (`app/Libraries/ImageUpload.php`): JPG·PNG·WEBP 5MB 이하, 파일 내용으로 형식 확인, 4000px 이하,
  휴대폰 방향 보정 후 가로 800px JPG 로 다시 저장(EXIF·위치정보 제거), 무작위 파일명
  → `user/public/uploads/instructors/` (git 제외, 서버 백업으로 관리). 사진 교체·강사 삭제 시 옛 파일 삭제
- 콘텐츠·설정 변경은 `writable/logs/audit-날짜.log` 에 기록
- 검사 오류 문구: `app/Language/ko/Validation.php` (관리자 `.env` 의 `app.defaultLocale = 'ko'`)

## 관리자 역할과 계정 관리 (admin)

| 기능 | 대표 관리자(owner) | 담당자(staff) |
|---|---|---|
| 문의 보기·처리, 콘텐츠, 통계, 알림 기록, 내 계정 | O | O |
| 문의 CSV 내려받기, 문의 삭제, 사이트 설정, 관리자 계정 관리 | O | X |

- 권한은 라우트 필터로 확인 (`adminauth:owner`), 화면의 메뉴·버튼 숨김은 보조 (`is_owner()`)
- `/admins`: 계정 추가(임시 비밀번호 1회 표시), 역할 변경, 사용 중지/다시 사용, 임시 비밀번호 발급(잠금 해제 포함), 2단계 초기화
- 임시 비밀번호로 로그인하면 ① 비밀번호 변경 → ② 2단계 인증 설정이 강제됨
- 계정 삭제 없음 (처리 이력 보존). 자기 자신·마지막 대표 관리자는 중지·강등 불가
- 역할 변경·중지는 매 요청 DB 확인으로 즉시 적용
- 서버 명령 `php spark admin:create` 도 역할 선택 (대표 관리자가 없으면 기본 owner)

## 관리자 로그인 / 2단계 인증 (admin)

1. 아이디·비밀번호 → 2. OTP 앱(Google Authenticator 등) 6자리 또는 복구 코드
- 2단계 인증을 설정하지 않은 계정은 로그인 후 설정 화면(`/account/2fa`)만 사용할 수 있다 (`app/Filters/AdminAuth.php`)
- OTP 비밀키는 `.env` 의 `encryption.key` 로 암호화해 저장, QR 코드는 서버에서 SVG 로 생성 (`app/Libraries/TwoFactor.php`)
- 복구 코드 8개는 설정 직후 한 번만 표시, 해시로 저장하고 한 번 쓰면 삭제
- 비밀번호·OTP 5회 실패 시 10분 잠금
- 비밀번호 변경: `/account/password` (10자 이상)
- 휴대폰과 복구 코드를 모두 잃어버린 경우: `php spark admin:reset-2fa 아이디`
- ⚠ `encryption.key` 가 바뀌면 기존 OTP 설정을 읽을 수 없으므로, 서버 이전 시 `.env` 를 그대로 옮기거나 모든 관리자 2단계 인증을 초기화한다

## 관리자 계정 생성

```bash
cd admin
php spark admin:create
```

비밀번호는 입력창으로만 받는다. (명령어 인자로 넘기지 않음)

## nginx

- `docs/nginx/kbc-user.conf`, `kbc-admin.conf` : 도메인 방식
- `docs/nginx/kbc-subpath.conf` : 개발 기간 kir1.cafe24.com/kbc/ (공개), /kbc/admin/ (관리자) 하위경로 방식
  - 두 경로 모두 Content-Security-Policy 적용 (같은 도메인의 스크립트만 실행) → 화면에 인라인 스크립트를 쓰지 않는다
- `docs/nginx/kbc-admin.conf` : 예전 개발 주소(admin.kbc.test)를 새 주소로 이동

도메인 연결 후에는 하위경로 include를 제거하고, user `.env`의 `app.baseURL`과 `cookie.path`를 도메인 기준으로 변경한다.
