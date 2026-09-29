<?php

namespace App\Controllers;

use App\Libraries\AuditLog;
use App\Libraries\TwoFactor;
use App\Models\AdminModel;

/**
 * 내 계정: 2단계 인증 설정, 비밀번호 변경
 */
class Account extends BaseController
{
    public function index()
    {
        $admin = $this->me();

        return view('account/index', [
            'title'     => '내 계정',
            'admin'     => $admin,
            'remaining' => TwoFactor::remainingRecoveryCodes($admin['recovery_codes']),
        ]);
    }

    /**
     * 2단계 인증 설정 화면 (QR 코드)
     * 비밀키는 확인이 끝날 때까지 세션에만 둔다.
     */
    public function twoFactor()
    {
        $admin = $this->me();
        if ($admin['totp_enabled_at'] !== null) {
            return redirect()->to(site_url('account'));
        }

        $tfa    = new TwoFactor();
        $secret = session('tfa_setup_secret');
        if (! is_string($secret)) {
            $secret = $tfa->newSecret();
            session()->set('tfa_setup_secret', $secret);
        }

        return view('account/two_factor', [
            'title'  => '2단계 인증 설정',
            'qr'     => $tfa->qrSvg($admin['username'], $secret),
            'secret' => trim(chunk_split($secret, 4, ' ')),
        ]);
    }

    public function enableTwoFactor()
    {
        $admin  = $this->me();
        $secret = session('tfa_setup_secret');

        if ($admin['totp_enabled_at'] !== null || ! is_string($secret)) {
            return redirect()->to(site_url('account'));
        }

        $tfa = new TwoFactor();
        if (! $tfa->verify($secret, (string) $this->request->getPost('code'))) {
            return redirect()->to(site_url('account/2fa'))->with('error', '인증번호가 올바르지 않습니다. 앱에 표시된 최신 6자리를 입력해 주세요.');
        }

        [$codes, $hashes] = $tfa->newRecoveryCodes();

        (new AdminModel())->update($admin['id'], [
            'totp_secret'     => $tfa->encrypt($secret),
            'totp_enabled_at' => date('Y-m-d H:i:s'),
            'recovery_codes'  => $hashes,
        ]);

        session()->remove('tfa_setup_secret');
        AuditLog::write('2fa_enabled', $admin['username']);

        // 복구 코드는 이 한 번만 보여준다
        return redirect()->to(site_url('account/2fa/codes'))->with('recovery_codes', $codes);
    }

    public function recoveryCodes()
    {
        $codes = session()->getFlashdata('recovery_codes');
        if (! is_array($codes)) {
            return redirect()->to(site_url('account'));
        }

        return view('account/recovery_codes', ['title' => '복구 코드', 'codes' => $codes]);
    }

    public function password()
    {
        return view('account/password', ['title' => '비밀번호 변경', 'forced' => (bool) $this->me()['must_change_password']]);
    }

    public function changePassword()
    {
        $admin = $this->me();
        $back  = redirect()->to(site_url('account/password'));

        $rules = [
            'current'  => 'required',
            'new'      => 'required|min_length[10]|max_length[72]',
            'confirm'  => 'required|matches[new]',
        ];
        if (! $this->validate($rules)) {
            return $back->with('error', '새 비밀번호는 10자 이상이어야 하고, 확인 칸과 같아야 합니다.');
        }

        $current = (string) $this->request->getPost('current');
        $new     = (string) $this->request->getPost('new');

        if (! password_verify($current, $admin['password_hash'])) {
            AuditLog::write('password_change_fail', $admin['username']);

            return $back->with('error', '현재 비밀번호가 올바르지 않습니다.');
        }
        if ($current === $new) {
            return $back->with('error', '현재 비밀번호와 다른 비밀번호를 입력해 주세요.');
        }

        (new AdminModel())->update($admin['id'], [
            'password_hash'        => password_hash($new, PASSWORD_DEFAULT),
            'must_change_password' => 0,
        ]);
        AuditLog::write('password_changed', $admin['username']);

        // 첫 로그인(임시 비밀번호)이면 다음 단계인 2단계 인증 설정으로
        if ($admin['totp_enabled_at'] === null) {
            return redirect()->to(site_url('account/2fa'))->with('message', '비밀번호를 변경했습니다. 이어서 2단계 인증을 설정해 주세요.');
        }

        return redirect()->to(site_url('account'))->with('message', '비밀번호를 변경했습니다.');
    }

    private function me(): array
    {
        return (new AdminModel())->find(session('admin_id'));
    }
}
