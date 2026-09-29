<?php
/**
 * 복구 코드 (설정 직후 한 번만 표시)
 *
 * @var list<string> $codes
 */
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1>2단계 인증 설정 완료</h1>

<div class="box">
    <h2>복구 코드를 지금 저장하세요</h2>
    <p>휴대폰을 잃어버렸을 때 인증번호 대신 쓰는 코드입니다. <b>이 화면을 벗어나면 다시 볼 수 없습니다.</b><br>
       각 코드는 한 번만 사용할 수 있습니다. 종이에 적거나 안전한 곳에 보관하세요.</p>
    <ul class="codes">
        <?php foreach ($codes as $code): ?>
            <li><code><?= esc($code) ?></code></li>
        <?php endforeach ?>
    </ul>
    <a class="btn" href="<?= site_url('/') ?>">저장했습니다. 관리자 화면으로 이동</a>
</div>
<?= $this->endSection() ?>
