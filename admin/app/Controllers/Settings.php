<?php

namespace App\Controllers;

use App\Libraries\AuditLog;
use App\Models\SiteSettingModel;

/**
 * 사이트 설정 (푸터 운영 정보, 안내 문구, 개인정보, 알림)
 * 비워 두면 홈페이지에 표시하지 않거나 "준비 중"으로 나온다.
 */
class Settings extends BaseController
{
    /** 묶음 => [설정키 => 입력 형식(text|textarea|email|date)] */
    public const GROUPS = [
        '안내 문구' => [
            'brand_name'    => 'text',
            'status_notice' => 'text',
            'launch_notice' => 'text',
            'fee_notice'    => 'text',
            'career_notice' => 'text',
        ],
        '운영 정보 (홈페이지 하단)' => [
            'operator_name'  => 'text',
            'representative' => 'text',
            'business_no'    => 'text',
            'academy_reg_no' => 'text',
            'address'        => 'text',
            'phone'          => 'text',
            'email'          => 'email',
            'refund_policy'  => 'textarea',
        ],
        '개인정보 처리방침' => [
            'privacy_officer'        => 'text',
            'inquiry_retention'      => 'text',
            'privacy_entrusted'      => 'textarea',
            'privacy_effective_date' => 'date',
        ],
        '알림' => [
            'notify_email' => 'email',
        ],
    ];

    /** 설정 화면 도움말 */
    public const HELP = [
        'phone'                  => '비워 두면 홈페이지에 전화번호를 표시하지 않습니다.',
        'operator_name'          => '비워 두면 하단에 "운영 주체·등록 정보: 준비 중"으로 표시됩니다.',
        'inquiry_retention'      => '예) 상담 완료 후 1년',
        'privacy_entrusted'      => '예) 카페24(서버 호스팅), 네이버(메일 발송)',
        'privacy_effective_date' => '비워 두면 처리방침에 "확정 전 초안" 안내가 표시됩니다.',
        'notify_email'           => '새 문의가 오면 이 주소로 알림 메일을 보냅니다.',
    ];

    public function index()
    {
        return view('settings/index', [
            'title'    => '사이트 설정',
            'settings' => (new SiteSettingModel())->allByKey(),
            'groups'   => self::GROUPS,
            'help'     => self::HELP,
        ]);
    }

    public function save()
    {
        $model    = new SiteSettingModel();
        $current  = $model->allByKey();
        $post     = (array) $this->request->getPost('settings');
        $rules    = [];
        $data     = [];

        foreach (self::GROUPS as $fields) {
            foreach ($fields as $key => $type) {
                // 폼에서 넘어온 항목만 저장 (빠진 항목이 빈 값으로 지워지지 않게)
                if (! isset($current[$key]) || ! array_key_exists($key, $post)) {
                    continue;
                }
                $data[$key] = trim((string) ($post[$key] ?? ''));

                $rule = match ($type) {
                    'email'    => 'permit_empty|valid_email|max_length[255]',
                    'date'     => 'permit_empty|valid_date[Y-m-d]',
                    'textarea' => 'permit_empty|max_length[2000]',
                    default    => 'permit_empty|max_length[500]',
                };
                $rules[$key] = ['label' => $current[$key]['label'], 'rules' => $rule];
            }
        }

        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $changed = [];
        foreach ($data as $key => $value) {
            $value = $value === '' ? null : $value;
            if ($value !== $current[$key]['value']) {
                $model->update($key, ['value' => $value, 'updated_at' => date('Y-m-d H:i:s')]);
                $changed[] = $key;
            }
        }

        if ($changed === []) {
            return redirect()->to(site_url('settings'))->with('error', '변경된 내용이 없습니다.');
        }

        AuditLog::write('settings_update', implode(',', $changed));

        return redirect()->to(site_url('settings'))->with('message', count($changed) . '개 항목을 저장했습니다.');
    }
}
