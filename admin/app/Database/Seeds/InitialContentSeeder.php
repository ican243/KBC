<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * 초기 콘텐츠
 * 기획안(2026-09-20)과 협업 제안서에 있는 내용만 넣는다. 확정 전 정보는 비워 둔다.
 * 이미 데이터가 있는 테이블은 건너뛴다.
 *
 * 사용: php spark db:seed InitialContentSeeder
 */
class InitialContentSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 교육과정: 제안 과정 (확정 전)
        if ($this->db->table('courses')->countAllResults() === 0) {
            $this->db->table('courses')->insertBatch([
                [
                    'slug'              => 'intensive-6w',
                    'title'             => '속성 6주',
                    'summary'           => '모의 라이브·숏폼',
                    'target'            => '기초 경험자·전환 희망자',
                    'duration'          => '6주',
                    'sessions_per_week' => '주 2회 (제안)',
                    'fields'            => '방송 콘셉트, 자기소개, 모의 라이브, 숏폼',
                    'assignments'       => '방송 콘셉트 설정, 3분 자기소개, 20분 모의 라이브 1회, 숏폼 2편',
                    'outputs'           => '자기소개 영상, 20분 모의 라이브, 숏폼 2편',
                    'is_confirmed'      => 0,
                    'is_published'      => 1,
                    'sort_order'        => 1,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ],
                [
                    'slug'              => 'basic-3m',
                    'title'             => '기본 3개월',
                    'summary'           => '분야별 기초와 포트폴리오',
                    'target'            => '입문자',
                    'duration'          => '3개월',
                    'sessions_per_week' => '주 2~3회 (제안)',
                    'fields'            => '댄스, 보컬, 화술, 메이크업, 촬영, 편집',
                    'assignments'       => '기초 진행 실습, 개인방송 및 커머스 모의방송',
                    'outputs'           => '포트폴리오',
                    'is_confirmed'      => 0,
                    'is_published'      => 1,
                    'sort_order'        => 2,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ],
                [
                    'slug'              => 'advanced-6m',
                    'title'             => '심화 6개월',
                    'summary'           => '특화 실습·성과 분석',
                    'target'            => '전업·심화 희망자',
                    'duration'          => '6개월',
                    'sessions_per_week' => '주 2~3회 (제안)',
                    'fields'            => '방송 형식별 특화',
                    'assignments'       => '정기 방송 실습, 협업 진행',
                    'outputs'           => '브랜드 제안서, 성과 분석',
                    'is_confirmed'      => 0,
                    'is_published'      => 1,
                    'sort_order'        => 3,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ],
            ]);
        }

        // 강사진: 분야 소개만 (실명·사진은 동의 후)
        if ($this->db->table('instructors')->countAllResults() === 0) {
            $fields = [
                ['댄스·표현', '댄스 전공자 또는 전문 교육과정 수료자', '카메라 동선, 안무, 리듬, 무대 표현'],
                ['보컬·화술', '보컬 전공자 또는 현역 가수', '발성, 호흡, 노래, 즉흥 멘트'],
                ['메이크업·스타일', '관련 교수 또는 현직 디자이너', '조명별 메이크업, 화면 이미지 설계'],
                ['방송 제작', '연출·촬영·음향·편집 실무자', '라이브 송출, 화면 전환, 오디오·영상 편집'],
                ['SNS·커머스', 'SNS 마케팅 및 라이브 판매 실무자', '콘텐츠 기획, 지표 분석, 상품 시연과 응대'],
            ];

            $rows = [];
            foreach ($fields as $i => [$field, $criteria, $description]) {
                $rows[] = [
                    'field'        => $field,
                    'criteria'     => $criteria,
                    'description'  => $description,
                    'is_published' => 1,
                    'sort_order'   => $i + 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
            $this->db->table('instructors')->insertBatch($rows);
        }

        // 사이트 설정: 확정 전 항목은 값 없이 키만 만든다
        if ($this->db->table('site_settings')->countAllResults() === 0) {
            $settings = [
                ['brand_name', '브랜드명', 'KBC아카데미'],
                ['status_notice', '현재 운영 상태 문구', 'KBC아카데미 개원 준비 중 / 강남 협업 교육장 검토 중'],
                ['launch_notice', '개강 안내 문구', '2026년 10월 시범 개강 목표'],
                ['fee_notice', '교습비·개강일 안내 문구', '세부 일정과 비용 확정 후 안내'],
                ['career_notice', '진로 연계 안내 문구', '진로 연계 체계 구축 및 업체 협약 추진 중'],
                ['operator_name', '운영 주체(법인명)', null],
                ['representative', '대표자', null],
                ['business_no', '사업자등록번호', null],
                ['academy_reg_no', '학원 등록번호', null],
                ['address', '교육장 주소', null],
                ['phone', '대표 연락처', null],
                ['email', '대표 이메일', null],
                ['privacy_officer', '개인정보 보호책임자', null],
                ['refund_policy', '교습비 반환 기준', null],
                ['inquiry_retention', '문의 정보 보존 기간', null],
                ['notify_email', '새 문의 알림 받을 이메일', null],
            ];

            $rows = [];
            foreach ($settings as $i => [$key, $label, $value]) {
                $rows[] = [
                    'setting_key' => $key,
                    'label'       => $label,
                    'value'       => $value,
                    'sort_order'  => $i + 1,
                    'updated_at'  => $now,
                ];
            }
            $this->db->table('site_settings')->insertBatch($rows);
        }
    }
}
