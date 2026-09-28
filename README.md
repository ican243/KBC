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
