<?php

namespace App\Controllers;

use App\Libraries\AuditLog;
use App\Models\AdminModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * 관리자 계정 관리 (대표 관리자만 - 라우트 필터 adminauth:owner)
 *
 * - 계정 삭제는 하지 않는다 (처리 이력의 작성자를 보존하기 위해 "사용 중지"로 대신)
 * - 임시 비밀번호는 화면에 한 번만 보여주고, 받은 사람은 첫 로그인 때 변경 + 2단계 인증 설정이 강제된다
 * - 자기 자신과 마지막 대표 관리자는 중지·강등할 수 없다
 */
class Admins extends BaseController
{
    public function index()
    {
        return view('admins/index', [
            'title'  => '관리자 계정',
            'admins' => (new AdminModel())->orderBy('status')->orderBy('role')->orderBy('id')->findAll(),
            'meId'   => (int) session('admin_id'),
        ]);
    }

    public function create()
    {
        return view('admins/form', ['title' => '관리자 계정 추가', 'row' => ['role' => 'staff'], 'id' => null]);
    }

    public function store()
    {
        $rules = [
            'username' => ['label' => '아이디', 'rules' => 'required|alpha_dash|min_length[4]|max_length[50]|is_unique[admins.username]'],
            'name'     => ['label' => '이름', 'rules' => 'required|max_length[50]'],
            'role'     => ['label' => '역할', 'rules' => 'required|in_list[owner,staff]'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $password = self::tempPassword();
        $id       = (new AdminModel())->insert([
            'username'             => trim((string) $this->request->getPost('username')),
            'name'                 => trim((string) $this->request->getPost('name')),
            'role'                 => $this->request->getPost('role'),
            'password_hash'        => password_hash($password, PASSWORD_DEFAULT),
            'must_change_password' => 1,
            'status'               => 'active',
        ]);
        AuditLog::write('admin_create', "#{$id} " . $this->request->getPost('username') . ' (' . $this->request->getPost('role') . ')');

        return $this->issue($id, $password, '계정을 만들었습니다.');
    }

    public function edit(int $id)
    {
        return view('admins/form', ['title' => '관리자 계정 수정', 'row' => $this->findOr404($id), 'id' => $id]);
    }

    public function update(int $id)
    {
        $row   = $this->findOr404($id);
        $rules = [
            'name' => ['label' => '이름', 'rules' => 'required|max_length[50]'],
            'role' => ['label' => '역할', 'rules' => 'required|in_list[owner,staff]'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role = (string) $this->request->getPost('role');
        if ($row['role'] === 'owner' && $role !== 'owner' && ($error = $this->ownerGuard($row)) !== null) {
            return redirect()->back()->with('error', $error);
        }

        (new AdminModel())->update($id, ['name' => trim((string) $this->request->getPost('name')), 'role' => $role]);
        AuditLog::write('admin_update', "#{$id} {$row['username']} 역할 {$row['role']} → {$role}");

        return redirect()->to(site_url('admins'))->with('message', '저장했습니다.');
    }

    /**
     * 사용 중지 ↔ 다시 사용
     */
    public function toggleStatus(int $id)
    {
        $row = $this->findOr404($id);
        $new = $row['status'] === 'active' ? 'disabled' : 'active';

        if ($new === 'disabled' && ($error = $this->ownerGuard($row, '중지')) !== null) {
            return redirect()->to(site_url('admins'))->with('error', $error);
        }

        (new AdminModel())->update($id, ['status' => $new]);
        AuditLog::write('admin_status', "#{$id} {$row['username']} → {$new}");

        return redirect()->to(site_url('admins'))->with('message', $row['username'] . ($new === 'active' ? ' 계정을 다시 사용합니다.' : ' 계정을 사용 중지했습니다.'));
    }

    /**
     * 임시 비밀번호 발급 (로그인 잠금도 함께 해제)
     */
    public function resetPassword(int $id)
    {
        $row      = $this->findOr404($id);
        $password = self::tempPassword();

        (new AdminModel())->update($id, [
            'password_hash'        => password_hash($password, PASSWORD_DEFAULT),
            'must_change_password' => 1,
            'failed_attempts'      => 0,
            'locked_until'         => null,
        ]);
        AuditLog::write('admin_password_reset', "#{$id} {$row['username']}");

        return $this->issue($id, $password, '임시 비밀번호를 발급했습니다.');
    }

    /**
     * 2단계 인증 초기화 (다음 로그인 때 다시 설정)
     */
    public function resetTwoFactor(int $id)
    {
        $row = $this->findOr404($id);
        if ($id === (int) session('admin_id')) {
            return redirect()->to(site_url('admins'))->with('error', '자기 자신의 2단계 인증은 여기서 초기화할 수 없습니다.');
        }

        (new AdminModel())->update($id, ['totp_secret' => null, 'totp_enabled_at' => null, 'recovery_codes' => null]);
        AuditLog::write('admin_2fa_reset', "#{$id} {$row['username']}");

        return redirect()->to(site_url('admins'))->with('message', $row['username'] . ' 계정의 2단계 인증을 초기화했습니다. 다음 로그인 때 다시 설정합니다.');
    }

    /**
     * 임시 비밀번호 1회 표시
     */
    public function issued()
    {
        $issued = session()->getFlashdata('issued');
        if (! is_array($issued)) {
            return redirect()->to(site_url('admins'));
        }

        return view('admins/issued', ['title' => '임시 비밀번호', 'issued' => $issued]);
    }

    private function issue(int $id, string $password, string $message)
    {
        $row = (new AdminModel())->find($id);

        return redirect()->to(site_url('admins/issued'))->with('issued', [
            'username' => $row['username'],
            'name'     => $row['name'],
            'password' => $password,
            'message'  => $message,
        ]);
    }

    /**
     * 자기 자신 / 마지막 대표 관리자를 중지·강등하려 하면 안내 문구, 아니면 null
     */
    private function ownerGuard(array $row, string $action = '강등'): ?string
    {
        if ((int) $row['id'] === (int) session('admin_id')) {
            return "자기 자신은 {$action}할 수 없습니다.";
        }
        if ($row['role'] === 'owner' && $row['status'] === 'active' && (new AdminModel())->countActiveOwners() <= 1) {
            return "마지막 대표 관리자는 {$action}할 수 없습니다.";
        }

        return null;
    }

    private function findOr404(int $id): array
    {
        return (new AdminModel())->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }

    /**
     * 임시 비밀번호 (헷갈리는 글자 제외, 12자리를 4자리씩)
     */
    private static function tempPassword(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $out   = '';
        for ($i = 0; $i < 12; $i++) {
            $out .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return implode('-', str_split($out, 4));
    }
}
