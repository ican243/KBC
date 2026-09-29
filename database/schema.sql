-- KBC아카데미 DB 구조 (데이터 제외)
-- 원본은 admin/app/Database/Migrations 이며, 이 파일은 참고/인수인계용으로 migrate 후 생성한다.
-- 생성: 2026-09-29

CREATE TABLE `admins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '로그인 아이디',
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'password_hash() 결과',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '표시 이름',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'active / disabled',
  `failed_attempts` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '연속 로그인 실패 횟수',
  `locked_until` datetime DEFAULT NULL COMMENT '이 시각까지 로그인 차단',
  `totp_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '2단계 인증 키 (추후 적용)',
  `totp_enabled_at` datetime DEFAULT NULL COMMENT '2단계 인증 설정 일시',
  `recovery_codes` text COLLATE utf8mb4_unicode_ci COMMENT '복구 코드 해시 (JSON)',
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='관리자 계정';
CREATE TABLE `consult_inquiries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `receipt_no` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '접수번호 (C+날짜-코드)',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_id` int unsigned DEFAULT NULL COMMENT '관심 과정 (미정이면 NULL)',
  `contact_time` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '희망 연락시간',
  `message` text COLLATE utf8mb4_unicode_ci COMMENT '문의내용',
  `agree_privacy_at` datetime NOT NULL COMMENT '개인정보 수집·이용 동의 일시 (필수)',
  `agree_marketing` tinyint(1) NOT NULL DEFAULT '0' COMMENT '홍보성 연락 동의 (선택)',
  `agree_marketing_at` datetime DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '유입 경로',
  `entry_page` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '신청한 화면 (home / contact / courses/주소이름)',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new' COMMENT 'new/contacted/done/hold',
  `assigned_admin_id` int unsigned DEFAULT NULL COMMENT '담당자',
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL COMMENT '접수일',
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제 처리 일시',
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_no` (`receipt_no`),
  KEY `consult_inquiries_course_id_foreign` (`course_id`),
  KEY `consult_inquiries_assigned_admin_id_foreign` (`assigned_admin_id`),
  KEY `status_created_at` (`status`,`created_at`),
  CONSTRAINT `consult_inquiries_assigned_admin_id_foreign` FOREIGN KEY (`assigned_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `consult_inquiries_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='교육 상담 신청';
CREATE TABLE `courses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '주소용 이름',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '과정명',
  `summary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '카드용 한 줄 소개',
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '대상',
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '기간',
  `sessions_per_week` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '주당 수업 횟수',
  `fields` text COLLATE utf8mb4_unicode_ci COMMENT '교육 분야',
  `assignments` text COLLATE utf8mb4_unicode_ci COMMENT '실습 과제',
  `outputs` text COLLATE utf8mb4_unicode_ci COMMENT '수료 산출물',
  `fee_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '교습비 안내 (확정 후 입력)',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1: 확정 / 0: 제안 과정(준비 중 표시)',
  `is_published` tinyint(1) NOT NULL DEFAULT '0' COMMENT '홈페이지 노출 여부',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='교육과정';
CREATE TABLE `events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_at` datetime NOT NULL COMMENT '일시',
  `place` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '장소',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_at` (`event_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='설명회 일정';
CREATE TABLE `faqs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '분류 (수강, 환불, 연령 등)',
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='자주 묻는 질문';
CREATE TABLE `inquiry_memos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'consult / partner',
  `inquiry_id` int unsigned NOT NULL,
  `admin_id` int unsigned DEFAULT NULL COMMENT '작성 관리자',
  `memo` text COLLATE utf8mb4_unicode_ci,
  `status_from` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '변경 전 상태',
  `status_to` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '변경 후 상태',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inquiry_memos_admin_id_foreign` (`admin_id`),
  KEY `inquiry_type_inquiry_id` (`inquiry_type`,`inquiry_id`),
  CONSTRAINT `inquiry_memos_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='문의 메모·상태 이력';
CREATE TABLE `instructors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `field` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '분야 (댄스·표현 등)',
  `criteria` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '강사 섭외 기준',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '교육 및 실습 내용',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '실명 (동의 후 입력)',
  `profile` text COLLATE utf8mb4_unicode_ci,
  `career` text COLLATE utf8mb4_unicode_ci COMMENT '검증된 경력',
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consent_at` datetime DEFAULT NULL COMMENT '실명·사진 게시 동의 일시',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='강사진';
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `notices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` datetime DEFAULT NULL COMMENT '게시일',
  `admin_id` int unsigned DEFAULT NULL COMMENT '작성 관리자',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notices_admin_id_foreign` (`admin_id`),
  KEY `is_published_published_at` (`is_published`,`published_at`),
  CONSTRAINT `notices_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='소식·공지';
CREATE TABLE `notification_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `channel` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email' COMMENT 'email (추후 sms 등)',
  `recipient` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '받는 곳',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'consult / partner',
  `related_id` int unsigned DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'sent / failed',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_created_at` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='알림 발송 기록';
CREATE TABLE `page_views` (
  `view_date` date NOT NULL COMMENT '날짜 (한국 시간)',
  `path` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '페이지 주소 (예: /courses/basic-3m)',
  `views` int unsigned NOT NULL DEFAULT '0' COMMENT '조회수',
  PRIMARY KEY (`view_date`,`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='공개 페이지 조회수 (개인정보 없음)';
CREATE TABLE `partner_inquiries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `receipt_no` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '접수번호 (P+날짜-코드)',
  `org_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '기관명',
  `contact_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '담당자',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `partner_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'space(교육장)/instructor(강사)/hiring(채용)',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '제안 내용',
  `agree_privacy_at` datetime NOT NULL COMMENT '개인정보 수집·이용 동의 일시 (필수)',
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '유입 경로',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new' COMMENT 'new/contacted/done/hold',
  `assigned_admin_id` int unsigned DEFAULT NULL COMMENT '담당자',
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL COMMENT '접수일',
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT '삭제 처리 일시',
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_no` (`receipt_no`),
  KEY `partner_inquiries_assigned_admin_id_foreign` (`assigned_admin_id`),
  KEY `status_created_at` (`status`,`created_at`),
  CONSTRAINT `partner_inquiries_assigned_admin_id_foreign` FOREIGN KEY (`assigned_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='기관 협력 제안';
CREATE TABLE `site_settings` (
  `setting_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '관리자 화면 표시명',
  `value` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사이트 설정';
