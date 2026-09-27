<?php

namespace App\Controllers;

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

            return redirect()->back()->withInput()->with('error', $failMessage);
        }

        // 해시 방식이 바뀐 경우 새 방식으로 다시 저장
        if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
            $model->update($admin['id'], ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        $model->recordSuccess($admin, $this->request->getIPAddress());

        // 세션 고정 공격 방지
        session()->regenerate(true);
        session()->set([
            'admin_id'   => $admin['id'],
            'admin_name' => $admin['name'],
        ]);

        return redirect()->to(site_url('/'));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'));
    }
}
