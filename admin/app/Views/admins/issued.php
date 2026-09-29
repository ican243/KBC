<?php
/**
 * 임시 비밀번호 (한 번만 표시)
 *
 * @var array $issued username, name, password, message
 */
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1><?= esc($issued['message']) ?></h1>

<div class="box">
    <dl class="info">
        <dt>이름</dt><dd><?= esc($issued['name']) ?></dd>
        <dt>아이디</dt><dd><code class="secret"><?= esc($issued['username']) ?></code></dd>
        <dt>임시 비밀번호</dt><dd><code class="secret"><?= esc($issued['password']) ?></code></dd>
        <dt>로그인 주소</dt><dd><?= esc(site_url('login')) ?></dd>
    </dl>
    <p class="flash flash-err" style="margin-top:16px">이 화면을 벗어나면 임시 비밀번호를 다시 볼 수 없습니다. 본인에게 직접 전달해 주세요.<br>
        첫 로그인 때 비밀번호 변경과 2단계 인증 설정이 필요합니다.</p>
    <a class="btn" href="<?= site_url('admins') ?>">전달했습니다. 목록으로</a>
</div>
<?= $this->endSection() ?>
