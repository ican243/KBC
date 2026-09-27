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

- user    : kbc_user    (필요한 테이블만 개별 권한 부여)
- admin   : kbc_admin   (조회/등록/수정/삭제)
- migrate : kbc_migrate (테이블 생성/변경, 마이그레이션 실행 시에만 사용)

## DB 마이그레이션

마이그레이션은 admin 프로젝트에서만 관리한다.

```bash
cd admin
env database.defaultGroup=migrate php spark migrate
```

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
