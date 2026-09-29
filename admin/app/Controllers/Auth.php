<?php

namespace App\Controllers;

use App\Libraries\AuditLog;
use App\Libraries\TwoFactor;
use App\Models\AdminModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session('admin_id')) {
            return redirect()->to(site_url('/'));
        }

        return view('auth/login');
    }

    public function attempt()
    {
        $rules = [
            'username' => 'required|max_length[50]',
            'password' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', '아이디와 비밀번호를 입력해 주세요.');
        }

        $username = (string) $this->request->getPost('username');
        $password = (string) $this->request->getPost('password');

        $model = new AdminModel();
        $admin = $model->findByUsername($username);

        // 아이디 존재 여부를 알 수 없도록 같은 문구 사용
        $failMessage = '아이디 또는 비밀번호가 올바르지 않습니다.';

        if ($admin === null || $admin['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', $failMessage);
        }

        if ($model->isLocked($admin)) {
            return redirect()->back()->withInput()
                ->with('error', '로그인 실패가 반복되어 잠시 차단되었습니다. ' . AdminModel::LOCK_MINUTES . '분 후 다시 시도해 주세요.');
        }

        if (! password_verify($password, $admin['password_hash'])) {
            $model->recordFailure($admin);
            AuditLog::write('login_fail', $admin['username']);

            return redirect()->back()->withInput()->with('error', $failMessage);
        }

        // 해시 방식이 바뀐 경우 새 방식으로 다시 저장
        if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
            $model->update($admin['id'], ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        // 세션 고정 공격 방지
        session()->regenerate(true);

        // 2단계 인증을 설정한 계정: OTP 확인 전까지는 로그인 완료가 아니다
        if ($admin['totp_enabled_at'] !== null) {
            session()->set('admin_2fa_pending', ['id' => (int) $admin['id'], 'at' => time()]);

            return redirect()->to(site_url('login/otp'));
        }

        // 아직 설정하지 않은 계정: 로그인은 되지만 설정 화면만 쓸 수 있다 (AdminAuth 필터)
        $this->completeLogin($model, $admin);

        return redirect()->to(site_url('account/2fa'));
    }

    /**
     * 2단계: OTP 6자리 또는 복구 코드 입력 화면
     */
    public function otp()
    {
        if ($this->pendingAdmin() === null) {
            return redirect()->to(site_url('login'));
        }

        return view('auth/otp');
    }

    public function verifyOtp()
    {
        $model = new AdminModel();
        $admin = $this->pendingAdmin();

        if ($admin === null) {
            return redirect()->to(site_url('login'))->with('error', '시간이 지나 다시 로그인해 주세요.');
        }

        if ($model->isLocked($admin)) {
            session()->remove('admin_2fa_pending');

            return redirect()->to(site_url('login'))
                ->with('error', '로그인 실패가 반복되어 잠시 차단되었습니다. ' . AdminModel::LOCK_MINUTES . '분 후 다시 시도해 주세요.');
        }

        $input = trim((string) $this->request->getPost('code'));
        $tfa   = new TwoFactor();
        $ok    = false;

        if (preg_match('/^\d[\d\s]{5,6}$/', $input)) {
            try {
                $ok = $tfa->verify($tfa->decrypt((string) $admin['totp_secret']), $input);
            } catch (\Throwable $e) {
                // 비밀키 복호화 실패 (암호화 키 변경 등) → 실패로 처리하고 원인은 로그에 남긴다
                log_message('error', '[2fa] 비밀키 확인 실패 (' . $admin['username'] . '): ' . $e->getMessage());
            }
        } else {
            // 복구 코드 (한 번 쓰면 삭제)
            $remaining = $tfa->useRecoveryCode($admin['recovery_codes'], $input);
            if ($remaining !== null) {
                $model->update($admin['id'], ['recovery_codes' => $remaining]);
                $ok = true;
                session()->setFlashdata('message', '복구 코드로 로그인했습니다. 남은 복구 코드: ' . TwoFactor::remainingRecoveryCodes($remaining) . '개');
            }
        }

        if (! $ok) {
            $model->recordFailure($admin);
            AuditLog::write('login_otp_fail', $admin['username']);

            return redirect()->to(site_url('login/otp'))->with('error', '인증번호가 올바르지 않습니다.');
        }

        session()->remove('admin_2fa_pending');
        session()->regenerate(true);
        $this->completeLogin($model, $admin);

        return redirect()->to(site_url('/'));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'));
    }

    private function completeLogin(AdminModel $model, array $admin): void
    {
        $model->recordSuccess($admin, $this->request->getIPAddress());
        session()->set([
            'admin_id'   => (int) $admin['id'],
            'admin_name' => $admin['name'],
        ]);
        AuditLog::write('login', $admin['username']);
    }

    /**
     * 비밀번호 확인을 마치고 OTP 를 기다리는 계정 (5분 제한)
     */
    private function pendingAdmin(): ?array
    {
        $pending = session('admin_2fa_pending');

        if (! is_array($pending) || time() - $pending['at'] > 300) {
            session()->remove('admin_2fa_pending');

            return null;
        }

        $admin = (new AdminModel())->find($pending['id']);

        return $admin !== null && $admin['status'] === 'active' && $admin['totp_enabled_at'] !== null ? $admin : null;
    }
}
