<?php

namespace App\Filters;

use App\Models\AdminModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * 관리자 접근 제어 (매 요청 DB 확인 → 역할 변경·정지가 바로 적용)
 *
 * 1) 로그인하지 않았거나 계정이 사용 중지되었으면 로그인 화면으로
 * 2) 비밀번호 변경이 필요하면 비밀번호 변경 화면만 허용   (필터 인자 password)
 * 3) 2단계 인증을 설정하지 않았으면 설정 화면만 허용       (필터 인자 setup)
 * 4) 대표 관리자 전용 주소는 역할 확인                     (필터 인자 owner)
 *
 * 예) 'filter' => 'adminauth:owner'
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

        // 화면(메뉴·버튼 표시)에서 쓰도록 최신 이름·역할을 세션에 반영
        session()->set(['admin_name' => $admin['name'], 'admin_role' => $admin['role']]);

        $args = (array) $arguments;

        if ($admin['must_change_password'] && ! in_array('password', $args, true)) {
            return redirect()->to(site_url('account/password'));
        }

        if (! $admin['must_change_password'] && $admin['totp_enabled_at'] === null && ! in_array('setup', $args, true)) {
            return redirect()->to(site_url('account/2fa'));
        }

        if (in_array('owner', $args, true) && $admin['role'] !== 'owner') {
            return service('response')->setStatusCode(403)->setBody(view('errors/forbidden', ['title' => '권한 없음']));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
