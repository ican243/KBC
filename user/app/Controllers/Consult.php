<?php

namespace App\Controllers;

use App\Libraries\ReceiptNumber;
use App\Models\ConsultInquiryModel;
use App\Models\CourseModel;
use CodeIgniter\Model;

/**
 * 교육 상담 신청 (홈 하단 폼)
 */
class Consult extends InquiryController
{
    protected string $form     = 'consult';
    protected string $prefix   = ReceiptNumber::CONSULT;
    protected string $donePath = 'consult/done';

    public function submit()
    {
        $post = $this->postValues(['name', 'phone', 'course_id', 'contact_time', 'message', 'agree_privacy', 'agree_marketing', 'entry_page']);

        // 관심 과정은 현재 공개 중인 과정만 허용
        $courseIds = implode(',', array_column((new CourseModel())->getPublished(), 'id'));

        $rules = [
            'name' => [
                'rules'  => 'required|min_length[2]|max_length[50]',
                'errors' => ['required' => '이름을 입력해 주세요.', 'min_length' => '이름을 2자 이상 입력해 주세요.', 'max_length' => '이름은 50자 이내로 입력해 주세요.'],
            ],
            'phone' => [
                'rules'  => 'required|max_length[20]',
                'errors' => ['required' => '연락처를 입력해 주세요.', 'max_length' => '연락처 형식을 확인해 주세요.'],
            ],
            'course_id' => [
                'rules'  => 'permit_empty|in_list[' . $courseIds . ']',
                'errors' => ['in_list' => '관심 과정을 다시 선택해 주세요.'],
            ],
            'contact_time' => [
                'rules'  => 'permit_empty|max_length[50]',
                'errors' => ['max_length' => '희망 연락시간은 50자 이내로 입력해 주세요.'],
            ],
            'message' => [
                'rules'  => 'permit_empty|max_length[2000]',
                'errors' => ['max_length' => '문의내용은 2,000자 이내로 입력해 주세요.'],
            ],
            'agree_privacy' => [
                'rules'  => 'required|in_list[1]',
                'errors' => ['required' => '개인정보 수집·이용에 동의해 주세요.', 'in_list' => '개인정보 수집·이용에 동의해 주세요.'],
            ],
        ];

        return $this->handle($post, $rules, static function (string $phone, string $now) use ($post): array {
            $marketing = $post['agree_marketing'] === '1';

            return [
                'name'               => $post['name'],
                'phone'              => $phone,
                'course_id'          => $post['course_id'] !== '' ? (int) $post['course_id'] : null,
                'contact_time'       => $post['contact_time'] !== '' ? $post['contact_time'] : null,
                'message'            => $post['message'] !== '' ? $post['message'] : null,
                'agree_marketing'    => $marketing ? 1 : 0,
                'agree_marketing_at' => $marketing ? $now : null,
                // 신청한 화면 (통계용). 정해진 형식이 아니면 기록하지 않음
                'entry_page'         => preg_match('#^(home|contact|courses/[a-z0-9_-]{1,50})$#', $post['entry_page']) ? $post['entry_page'] : null,
            ];
        });
    }

    public function done()
    {
        return $this->showDone('상담 신청 완료');
    }

    /**
     * 오류 시 돌아갈 곳: /contact 페이지에서 보냈으면 그 페이지, 아니면 홈 하단
     */
    protected function formUrl(): string
    {
        return $this->request->getPost('return_to') === 'contact'
            ? site_url('contact')
            : site_url('/') . '#contact';
    }

    protected function model(): Model
    {
        return new ConsultInquiryModel();
    }
}
