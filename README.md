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
| 공개 홈페이지 | http://kbc.test |
| 관리자 | http://admin.kbc.test |

## 설치

```bash
cd user  && composer install && cp env .env
cd admin && composer install && cp env .env
```

각 `.env`에 `app.baseURL`과 DB 접속 정보를 입력한다.
DB 계정은 user/admin이 서로 다른 계정을 사용한다.

- user  : kbc_user  (조회 위주, 문의 등록만 허용 예정)
- admin : kbc_admin (관리 권한)

nginx 설정 예시는 `docs/nginx/` 참고.
