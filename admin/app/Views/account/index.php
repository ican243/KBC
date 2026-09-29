<?php
/**
 * 내 계정
 *
 * @var array $admin
 * @var int   $remaining 남은 복구 코드 수
 */
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1>내 계정</h1>

<div class="box">
    <dl class="info">
        <dt>아이디</dt><dd><?= esc($admin['username']) ?></dd>
        <dt>이름</dt><dd><?= esc($admin['name']) ?></dd>
        <dt>마지막 로그인</dt><dd><?= dt($admin['last_login_at']) ?> (<?= esc($admin['last_login_ip'] ?? '-') ?>)</dd>
        <dt>2단계 인증</dt>
        <dd>
            <?php if ($admin['totp_enabled_at'] !== null): ?>
                <span class="badge badge-done">사용 중</span> <?= dt($admin['totp_enabled_at']) ?> 설정
                · 남은 복구 코드 <?= $remaining ?>개
                <?php if ($remaining <= 2): ?><br><small>복구 코드가 얼마 남지 않았습니다. 서버 관리자에게 초기화를 요청해 다시 설정하세요.</small><?php endif ?>
            <?php else: ?>
                <span class="badge badge-new">미설정</span> <a href="<?= site_url('account/2fa') ?>">설정하기</a>
            <?php endif ?>
        </dd>
    </dl>
</div>

<a class="btn" href="<?= site_url('account/password') ?>">비밀번호 변경</a>
<?= $this->endSection() ?>
