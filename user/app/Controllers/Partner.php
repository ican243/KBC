<?php

namespace App\Controllers;

use App\Libraries\ReceiptNumber;
use App\Models\PartnerInquiryModel;
use CodeIgniter\Model;

/**
 * 기관 협력 제안 (교육장 / 강사 / 채용)
 */
class Partner extends InquiryController
{
    protected string $form     = 'partner';
    protected string $prefix   = ReceiptNumber::PARTNER;
    protected string $donePath = 'partner/done';

    public function index()
    {
        return view('partner/form', $this->pageData(
            '교육 공간·채용 협력 제안',
            '교육장, 강사, 채용 협력을 제안해 주세요.'
        ) + ['types' => PartnerInquiryModel::TYPES]);
    }

    public function submit()
    {
        $post = $this->postValues(['org_name', 'contact_name', 'phone', 'partner_type', 'message', 'agree_privacy']);

        $rules = [
            'org_name' => [
                'rules'  => 'required|max_length[100]',
                'errors' => ['required' => '기관명을 입력해 주세요.', 'max_length' => '기관명은 100자 이내로 입력해 주세요.'],
            ],
            'contact_name' => [
                'rules'  => 'required|min_length[2]|max_length[50]',
                'errors' => ['required' => '담당자 이름을 입력해 주세요.', 'min_length' => '담당자 이름을 2자 이상 입력해 주세요.', 'max_length' => '담당자 이름은 50자 이내로 입력해 주세요.'],
            ],
            'phone' => [
                'rules'  => 'required|max_length[20]',
                'errors' => ['required' => '연락처를 입력해 주세요.', 'max_length' => '연락처 형식을 확인해 주세요.'],
            ],
            'partner_type' => [
                'rules'  => 'required|in_list[' . implode(',', array_keys(PartnerInquiryModel::TYPES)) . ']',
                'errors' => ['required' => '제휴 유형을 선택해 주세요.', 'in_list' => '제휴 유형을 다시 선택해 주세요.'],
            ],
            'message' => [
                'rules'  => 'required|max_length[2000]',
                'errors' => ['required' => '제안 내용을 입력해 주세요.', 'max_length' => '제안 내용은 2,000자 이내로 입력해 주세요.'],
            ],
            'agree_privacy' => [
                'rules'  => 'required|in_list[1]',
                'errors' => ['required' => '개인정보 수집·이용에 동의해 주세요.', 'in_list' => '개인정보 수집·이용에 동의해 주세요.'],
            ],
        ];

        return $this->handle($post, $rules, static fn (string $phone, string $now): array => [
            'org_name'     => $post['org_name'],
            'contact_name' => $post['contact_name'],
            'phone'        => $phone,
            'partner_type' => $post['partner_type'],
            'message'      => $post['message'],
        ]);
    }

    public function done()
    {
        return $this->showDone('협력 제안 접수 완료');
    }

    protected function formUrl(): string
    {
        return site_url('partner');
    }

    protected function model(): Model
    {
        return new PartnerInquiryModel();
    }
}
