<?php

namespace App\Controllers;

use App\Libraries\ReceiptNumber;
use App\Libraries\SpamGuard;
use App\Models\SiteSettingModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Model;

/**
 * 상담 신청 / 협력 제안 공통 처리
 *
 * 처리 순서: 봇 검사 → 입력값 검사 → IP 제한 → 연락처 중복 → 접수번호 발급 후 저장 → 완료 화면
 */
abstract class InquiryController extends BaseController
{
    /** 폼 구분 (스팸 방지 기록용) */
    protected string $form;

    /** 접수번호 접두어 */
    protected string $prefix;

    /** 완료 화면 주소 */
    protected string $donePath;

    /** 입력 오류 시 돌아갈 주소 */
    abstract protected function formUrl(): string;

    /**
     * 공통 접수 처리
     *
     * @param array    $post     검사할 입력값
     * @param array    $rules    CI4 검증 규칙
     * @param callable $buildRow fn(string $phone, string $now): array  폼별 저장 값
     *                           (접수번호, 동의 일시, 유입경로, IP 등 공통 값은 여기서 채운다)
     */
    protected function handle(array $post, array $rules, callable $buildRow): RedirectResponse
    {
        $guard = new SpamGuard();
        $back  = redirect()->to($this->formUrl())->withInput();

        // 1) 봇이면 저장하지 않고 성공한 것처럼 보여준다
        if ($guard->isBot($this->request)) {
            return redirect()->to(site_url($this->donePath))->with('receipt_no', ReceiptNumber::make($this->prefix));
        }

        // 2) 입력값 검사
        $errors = $this->validateData($post, $rules) ? [] : $this->validator->getErrors();

        $phone = format_phone($post['phone'] ?? '');
        if (! isset($errors['phone']) && $phone === null) {
            $errors['phone'] = '연락처 형식을 확인해 주세요. 예) 010-1234-5678';
        }

        if ($errors !== []) {
            return $back->with('errors', $errors);
        }

        // 3) 같은 IP 연속 제출 제한
        if ($guard->isTooFrequent($this->request->getIPAddress(), $this->form)) {
            return $back->with('errors', ['form' => '짧은 시간에 신청이 많아 잠시 제한되었습니다. 10분 후 다시 시도해 주세요.']);
        }

        // 4) 같은 연락처 중복 접수 제한
        if ($guard->isDuplicate($phone, $this->form)) {
            return $back->with('errors', ['form' => '이미 접수된 연락처입니다. 담당자가 곧 연락드리겠습니다.']);
        }

        // 5) 저장
        $now = date('Y-m-d H:i:s');
        $row = $buildRow($phone, $now) + [
            'agree_privacy_at' => $now,
            'source'           => $this->source(),
            'ip'               => $this->request->getIPAddress(),
            'user_agent'       => mb_substr((string) $this->request->getUserAgent(), 0, 255),
        ];

        try {
            $receiptNo = $this->insertWithReceipt($this->model(), $row);
        } catch (\Throwable $e) {
            log_message('error', '[' . $this->form . '] 문의 저장 실패: ' . $e->getMessage());

            return $back->with('errors', ['form' => '일시적인 오류로 접수되지 않았습니다. 잠시 후 다시 시도해 주세요.']);
        }

        $guard->remember($phone, $this->form);

        // 새로고침해도 다시 저장되지 않도록 완료 화면으로 이동
        return redirect()->to(site_url($this->donePath))->with('receipt_no', $receiptNo);
    }

    abstract protected function model(): Model;

    /**
     * 접수번호를 붙여 저장. 번호가 겹치면(드묾) 새 번호로 최대 3번 시도
     */
    private function insertWithReceipt(Model $model, array $row): string
    {
        for ($try = 1; ; $try++) {
            $row['receipt_no'] = ReceiptNumber::make($this->prefix);

            try {
                $model->insert($row);

                return $row['receipt_no'];
            } catch (DatabaseException $e) {
                $duplicate = str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'receipt_no');
                if (! $duplicate || $try >= 3) {
                    throw $e;
                }
            }
        }
    }

    /**
     * 유입 경로 (site.js 가 채워 보내는 값)
     */
    private function source(): ?string
    {
        $value = trim(strip_tags((string) $this->request->getPost('source')));

        return $value === '' ? null : mb_substr($value, 0, 255);
    }

    /**
     * 완료 화면. 접수번호는 접수 직후 한 번만 보여준다.
     */
    protected function showDone(string $title)
    {
        $receiptNo = session()->getFlashdata('receipt_no');

        if ($receiptNo === null) {
            return redirect()->to(site_url('/'));
        }

        return view('inquiry/done', $this->pageData($title) + ['receiptNo' => $receiptNo]);
    }

    protected function pageData(string $title, string $description = ''): array
    {
        return [
            'title'       => $title . ' | KBC아카데미',
            'description' => $description,
            'settings'    => (new SiteSettingModel())->getMap(),
        ];
    }

    /**
     * POST 값 앞뒤 공백 제거 (없는 값은 빈 문자열)
     */
    protected function postValues(array $keys): array
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = trim((string) $this->request->getPost($key));
        }

        return $values;
    }
}
