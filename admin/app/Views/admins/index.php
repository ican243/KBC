<?php
/**
 * 관리자 계정 목록 (대표 관리자만)
 *
 * @var array $admins
 * @var int   $meId
 */
use App\Models\AdminModel;
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="head-row">
    <h1>관리자 계정</h1>
    <a class="btn" href="<?= site_url('admins/new') ?>">+ 계정 추가</a>
</div>
<p class="help">담당자는 문의 처리·콘텐츠·통계를, 대표 관리자는 여기에 더해 CSV 내려받기·문의 삭제·사이트 설정·계정 관리를 할 수 있습니다. 퇴사자는 삭제하지 않고 사용 중지합니다.</p>

<div class="table-wrap"><table>
    <thead><tr><th>아이디</th><th>이름</th><th>역할</th><th>2단계 인증</th><th>마지막 로그인</th><th>상태</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($admins as $a): ?>
        <tr>
            <td><?= esc($a['username']) ?><?= (int) $a['id'] === $meId ? ' <small class="muted">(나)</small>' : '' ?></td>
            <td><?= esc($a['name']) ?></td>
            <td><?= esc(AdminModel::ROLES[$a['role']] ?? $a['role']) ?></td>
            <td><?= $a['totp_enabled_at'] ? '<span class="badge badge-done">사용 중</span>' : '<span class="badge badge-new">미설정</span>' ?>
                <?= $a['must_change_password'] ? ' <span class="badge badge-contacted">임시 비밀번호</span>' : '' ?></td>
            <td><?= dt($a['last_login_at']) ?></td>
            <td><?= $a['status'] === 'active' ? '<span class="badge badge-done">사용 중</span>' : '<span class="badge badge-hold">중지</span>' ?>
                <?= $a['locked_until'] && strtotime($a['locked_until']) > time() ? ' <span class="badge badge-new">잠김</span>' : '' ?></td>
            <td><a class="btn btn-line" href="<?= site_url('admins/' . $a['id'] . '/edit') ?>">관리</a></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table></div>
<?= $this->endSection() ?>
