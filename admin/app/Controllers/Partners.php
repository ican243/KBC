<?php

namespace App\Controllers;

use App\Libraries\InquiryStatus;
use App\Models\PartnerInquiryModel;
use CodeIgniter\Model;

/**
 * 기관 협력 제안 관리
 */
class Partners extends InquiryAdminController
{
    protected string $type  = 'partner';
    protected string $path  = 'partners';
    protected string $label = '협력 제안';

    protected function model(): Model
    {
        return new PartnerInquiryModel();
    }

    protected function filters(): array
    {
        $type = trim((string) $this->request->getGet('partner_type'));

        return $this->commonFilters() + [
            'partner_type' => isset(PartnerInquiryModel::TYPES[$type]) ? $type : '',
        ];
    }

    protected function filterOptions(): array
    {
        return [
            'statuses' => InquiryStatus::LABELS,
            'types'    => PartnerInquiryModel::TYPES,
        ];
    }

    protected function csvColumns(): array
    {
        return [
            ['접수번호', '접수일시', '상태', '기관명', '담당자', '연락처', '제휴 유형', '제안 내용', '처리 담당', '유입 경로'],
            static fn (array $r): array => [
                $r['receipt_no'],
                $r['created_at'],
                InquiryStatus::label($r['status']),
                $r['org_name'],
                $r['contact_name'],
                $r['phone'],
                PartnerInquiryModel::TYPES[$r['partner_type']] ?? $r['partner_type'],
                $r['message'],
                $r['admin_name'],
                $r['source'],
            ],
        ];
    }
}
