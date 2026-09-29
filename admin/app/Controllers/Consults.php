<?php

namespace App\Controllers;

use App\Libraries\InquiryStatus;
use App\Models\ConsultInquiryModel;
use App\Models\CourseModel;
use CodeIgniter\Model;

/**
 * 교육 상담 문의 관리
 */
class Consults extends InquiryAdminController
{
    protected string $type  = 'consult';
    protected string $path  = 'consults';
    protected string $label = '상담 문의';

    protected function model(): Model
    {
        return new ConsultInquiryModel();
    }

    protected function filters(): array
    {
        $course  = trim((string) $this->request->getGet('course_id'));
        $courses = (new CourseModel())->options();

        return $this->commonFilters() + [
            'course_id' => $course === 'none' || isset($courses[(int) $course]) ? $course : '',
        ];
    }

    protected function filterOptions(): array
    {
        return [
            'statuses' => InquiryStatus::LABELS,
            'sources'  => array_keys($this->sourceGroups()),
            'courses'  => (new CourseModel())->options(),
        ];
    }

    protected function csvColumns(): array
    {
        return [
            ['접수번호', '접수일시', '상태', '이름', '연락처', '관심 과정', '희망 연락시간', '문의내용', '홍보 동의', '담당자', '유입 경로'],
            static fn (array $r): array => [
                $r['receipt_no'],
                $r['created_at'],
                InquiryStatus::label($r['status']),
                $r['name'],
                $r['phone'],
                $r['course_title'] ?? '미정',
                $r['contact_time'],
                $r['message'],
                $r['agree_marketing'] ? '동의' : '-',
                $r['admin_name'],
                $r['source'],
            ],
        ];
    }
}
