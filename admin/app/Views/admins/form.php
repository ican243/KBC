<?php
/**
 * 관리자 계정 추가 / 수정 (+ 사용 중지, 임시 비밀번호, 2단계 초기화)
 *
 * @var ?int  $id
 * @var array $row
 */
use App\Models\AdminModel;

$isMe = $id !== null && $id === (int) session('admin_id');
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1><?= esc($title) ?></h1>

<form class="box form-grid" method="post" action="<?= $id === null ? site_url('admins') : site_url('admins/' . $id) ?>">
    <?= csrf_field() ?>
    <label><b>아이디 *</b>
        <?php if ($id === null): ?>
            <input type="text" name="username" value="<?= esc(old('username', ''), 'attr') ?>" required minlength="4" maxlength="50" pattern="[A-Za-z0-9_\-]+" autocomplete="off">
            <span class="help">영문·숫자·밑줄·하이픈 4자 이상. 만든 뒤에는 바꿀 수 없습니다.</span>
            <?= field_error('username') ?>
        <?php else: ?>
            <input type="text" value="<?= esc($row['username'], 'attr') ?>" disabled>
        <?php endif ?>
    </label>
    <label><b>이름 *</b>
        <input type="text" name="name" value="<?= esc(field_value($row, 'name'), 'attr') ?>" required maxlength="50">
        <?= field_error('name') ?>
    </label>
    <label><b>역할 *</b>
        <select name="role">
            <?php foreach (AdminModel::ROLES as $key => $label): ?>
            <option value="<?= $key ?>"<?= field_value($row, 'role') === $key ? ' selected' : '' ?>><?= $label ?></option>
            <?php endforeach ?>
        </select>
        <?php if ($isMe): ?><span class="help">자기 자신의 역할은 낮출 수 없습니다.</span><?php endif ?>
        <?= field_error('role') ?>
    </label>
    <?php if ($id === null): ?>
        <p class="help full">저장하면 임시 비밀번호가 한 번만 표시됩니다. 받은 사람은 첫 로그인 때 비밀번호를 바꾸고 2단계 인증을 설정해야 합니다.</p>
    <?php endif ?>
    <div class="form-actions full">
        <button class="btn" type="submit">저장</button>
        <a class="btn btn-line" href="<?= site_url('admins') ?>">목록으로</a>
    </div>
</form>

<?php if ($id !== null): ?>
<div class="box">
    <h2>계정 관리</h2>
    <dl class="info">
        <dt>상태</dt><dd><?= $row['status'] === 'active' ? '사용 중' : '사용 중지' ?></dd>
        <dt>2단계 인증</dt><dd><?= $row['totp_enabled_at'] ? '사용 중 (' . dt($row['totp_enabled_at']) . ' 설정)' : '미설정' ?></dd>
        <dt>마지막 로그인</dt><dd><?= dt($row['last_login_at']) ?> (<?= esc($row['last_login_ip'] ?? '-') ?>)</dd>
    </dl>
    <div class="row-actions" style="margin-top:16px;flex-wrap:wrap">
        <?php if (! $isMe): ?>
        <form method="post" action="<?= site_url('admins/' . $id . '/status') ?>"
              data-confirm="<?= $row['status'] === 'active' ? '이 계정을 사용 중지할까요? 로그인 중이면 바로 로그아웃됩니다.' : '이 계정을 다시 사용할까요?' ?>">
            <?= csrf_field() ?>
            <button class="btn <?= $row['status'] === 'active' ? 'btn-danger' : 'btn-line' ?>" type="submit"><?= $row['status'] === 'active' ? '사용 중지' : '다시 사용' ?></button>
        </form>
        <form method="post" action="<?= site_url('admins/' . $id . '/2fa') ?>" data-confirm="2단계 인증을 초기화할까요? 다음 로그인 때 다시 설정합니다.">
            <?= csrf_field() ?>
            <button class="btn btn-line" type="submit">2단계 인증 초기화</button>
        </form>
        <?php endif ?>
        <form method="post" action="<?= site_url('admins/' . $id . '/password') ?>" data-confirm="임시 비밀번호를 새로 발급할까요? 지금 비밀번호는 더 이상 쓸 수 없습니다.">
            <?= csrf_field() ?>
            <button class="btn btn-line" type="submit">임시 비밀번호 발급</button>
        </form>
    </div>
    <?php if ($isMe): ?><p class="help">자기 자신은 사용 중지·2단계 초기화를 할 수 없습니다. 비밀번호는 "내 계정"에서 바꾸세요.</p><?php endif ?>
</div>
<?php endif ?>
<?= $this->endSection() ?>
