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
| 관리자 | http://admin.kbc.test (PC hosts 파일로 접속) |

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

## 관리자 계정 생성

```bash
cd admin
php spark admin:create
```

비밀번호는 입력창으로만 받는다. (명령어 인자로 넘기지 않음)

## nginx

- `docs/nginx/kbc-user.conf`, `kbc-admin.conf` : 도메인 방식
- `docs/nginx/kbc-subpath.conf` : 개발 기간 kir1.cafe24.com/kbc/ 하위경로 방식

도메인 연결 후에는 하위경로 include를 제거하고, user `.env`의 `app.baseURL`과 `cookie.path`를 도메인 기준으로 변경한다.
