<?php
/**
 * 2단계 인증 설정 (QR 코드)
 *
 * @var string $qr     SVG
 * @var string $secret 직접 입력용 키 (4자리씩 띄움)
 */
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1>2단계 인증 설정</h1>

<div class="box">
    <p>관리자 계정 보호를 위해 <b>2단계 인증 설정이 필요합니다.</b> 설정을 마쳐야 관리자 메뉴를 사용할 수 있습니다.</p>
    <ol class="steps-list">
        <li>휴대폰에 <b>Google Authenticator</b> 또는 <b>Microsoft Authenticator</b> 앱을 설치합니다.</li>
        <li>앱에서 <b>QR 코드 스캔</b>을 누르고 아래 QR 코드를 찍습니다.</li>
        <li>앱에 표시된 <b>6자리 숫자</b>를 아래에 입력합니다.</li>
    </ol>

    <div class="qr-wrap">
        <div class="qr"><?= $qr /* 서버에서 만든 SVG */ ?></div>
        <div>
            <p class="muted">QR 코드를 찍을 수 없으면 앱에서 "설정 키 입력"을 선택하고 아래 키를 입력하세요.</p>
            <code class="secret"><?= esc($secret) ?></code>

            <form method="post" action="<?= site_url('account/2fa') ?>" class="otp-form">
                <?= csrf_field() ?>
                <label>인증번호 6자리
                    <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="7" placeholder="123456" required>
                </label>
                <button class="btn" type="submit">확인하고 설정 완료</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
