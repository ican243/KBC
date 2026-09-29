<?php

namespace App\Filters;

use App\Models\AdminModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * 관리자 접근 제어
 * - 로그인하지 않았거나 계정이 사용 중지되었으면 로그인 화면으로
 * - 2단계 인증을 아직 설정하지 않았으면 설정 화면으로 (매 요청 DB 확인)
 *   (adminauth:setup 으로 지정한 주소 = 설정 화면, 로그아웃은 예외)
 */
class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session('admin_id')) {
            // 비밀번호는 맞고 OTP 를 기다리는 중이면 OTP 화면으로
            return redirect()->to(site_url(session('admin_2fa_pending') ? 'login/otp' : 'login'));
        }

        $admin = (new AdminModel())->find(session('admin_id'));

        if ($admin === null || $admin['status'] !== 'active') {
            session()->destroy();

            return redirect()->to(site_url('login'));
        }

        $setupAllowed = in_array('setup', (array) $arguments, true);

        if ($admin['totp_enabled_at'] === null && ! $setupAllowed) {
            return redirect()->to(site_url('account/2fa'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
