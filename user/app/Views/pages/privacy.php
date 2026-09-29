<?php
/**
 * 개인정보 처리방침 (개인정보 보호법 제30조)
 * 운영 주체, 보유 기간, 위탁, 보호책임자, 시행일은 site_settings 값을 사용한다. 비어 있으면 "준비 중".
 * 공개 전 법무 검토 필요.
 *
 * @var array $settings
 */
$val = static function (string $key) use ($settings): string {
    $value = setting($settings, $key);

    return $value !== null ? nl2br(esc($value)) : '<span class="pending">준비 중</span>';
};
$operator = esc(setting($settings, 'operator_name', setting($settings, 'brand_name', 'KBC아카데미')));
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark">안내</span>
    <h1>개인정보 처리방침</h1>
</div></section>

<section class="section"><div class="wrap narrow policy">
    <?php if (setting($settings, 'privacy_effective_date') === null): ?>
        <p class="form-alert">시행일 확정 전 초안입니다. 운영 주체·보유 기간·보호책임자 등 일부 항목은 확정 후 게시됩니다.</p>
    <?php endif ?>

    <p><?= $operator ?>(이하 "아카데미")는 「개인정보 보호법」 제30조에 따라 정보주체의 개인정보를 보호하고 관련 고충을 신속하게 처리하기 위하여 다음과 같이 개인정보 처리방침을 수립·공개합니다.</p>

    <h2>1. 개인정보의 처리 목적</h2>
    <p>아카데미는 다음 목적을 위하여 개인정보를 처리하며, 목적 외의 용도로는 이용하지 않습니다.</p>
    <ul>
        <li>교육 상담 신청 접수 및 상담 연락</li>
        <li>기관 협력(교육장·강사·채용) 제안 검토 및 회신</li>
        <li>홍보성 연락에 동의한 경우에 한하여 설명회·모집 안내</li>
        <li>부정 이용 방지 및 서비스 안정성 확보</li>
    </ul>

    <h2>2. 처리하는 개인정보 항목</h2>
    <table>
        <thead><tr><th>구분</th><th>항목</th></tr></thead>
        <tbody>
            <tr><td>교육 상담 신청</td><td>(필수) 이름, 연락처<br>(선택) 관심 과정, 희망 연락시간, 문의내용</td></tr>
            <tr><td>기관 협력 제안</td><td>(필수) 기관명, 담당자 이름, 연락처, 제휴 유형, 제안 내용</td></tr>
            <tr><td>자동 수집</td><td>접속 IP, 브라우저 정보, 유입 경로, 접수 일시</td></tr>
        </tbody>
    </table>

    <h2>3. 개인정보의 보유 및 이용 기간</h2>
    <p>아카데미는 처리 목적이 달성되면 개인정보를 지체 없이 파기합니다. 문의 정보의 보유 기간은 다음과 같습니다.</p>
    <ul>
        <li>문의 정보 보유 기간: <?= $val('inquiry_retention') ?></li>
        <li>홍보성 연락 동의 정보: 동의 철회 시까지</li>
    </ul>
    <p>다만 관계 법령에 따라 보존할 필요가 있는 경우 해당 법령에서 정한 기간 동안 보관합니다.</p>

    <h2>4. 개인정보의 제3자 제공</h2>
    <p>아카데미는 정보주체의 개인정보를 제3자에게 제공하지 않습니다. 상담 정보는 채용 업체에 자동으로 전달하지 않으며, 수료 후 채용 연계가 필요한 경우에는 지원 의사를 별도로 확인하고 제공 항목·대상·목적을 안내한 뒤 동의를 받습니다.</p>

    <h2>5. 개인정보 처리 위탁</h2>
    <p><?= $val('privacy_entrusted') ?></p>

    <h2>6. 개인정보의 파기 절차 및 방법</h2>
    <p>보유 기간이 지나거나 처리 목적이 달성된 개인정보는 지체 없이 파기합니다. 전자적 파일은 복구할 수 없는 방법으로 삭제하고, 종이 문서는 분쇄하거나 소각합니다.</p>

    <h2>7. 정보주체의 권리와 행사 방법</h2>
    <p>정보주체는 언제든지 개인정보 열람, 정정·삭제, 처리 정지, 동의 철회를 요구할 수 있습니다. 아래 개인정보 보호책임자에게 연락하시면 지체 없이 조치하겠습니다.</p>

    <h2>8. 개인정보의 안전성 확보 조치</h2>
    <ul>
        <li>관리자 접근 권한 최소화 및 계정 분리</li>
        <li>관리자 비밀번호 암호화 저장, 로그인 실패 시 접근 제한</li>
        <li>공개 홈페이지에서는 문의 정보를 조회할 수 없도록 데이터베이스 권한 분리</li>
        <li>접속 기록 보관, 암호화 통신(HTTPS) 적용</li>
    </ul>

    <h2>9. 자동으로 수집하는 장치의 설치·운영 및 거부</h2>
    <p>아카데미는 보안(위조 요청 방지)과 입력 오류 안내를 위해 쿠키를, 유입 경로 확인을 위해 브라우저 저장소를 사용합니다. 브라우저 설정에서 저장을 거부할 수 있으나, 이 경우 상담 신청이 제한될 수 있습니다.</p>

    <h2>10. 개인정보 보호책임자</h2>
    <ul>
        <li>개인정보 보호책임자: <?= $val('privacy_officer') ?></li>
        <li>연락처: <?= $val('phone') ?></li>
        <li>이메일: <?= $val('email') ?></li>
    </ul>

    <h2>11. 권익침해 구제 방법</h2>
    <p>개인정보 침해에 대한 신고나 상담이 필요한 경우 아래 기관에 문의할 수 있습니다.</p>
    <ul>
        <li>개인정보분쟁조정위원회: 1833-6972 (www.kopico.go.kr)</li>
        <li>개인정보침해신고센터: 118 (privacy.kisa.or.kr)</li>
        <li>대검찰청: 1301 (www.spo.go.kr)</li>
        <li>경찰청: 182 (ecrm.police.go.kr)</li>
    </ul>

    <h2>12. 시행일</h2>
    <p>이 개인정보 처리방침은 <?= $val('privacy_effective_date') ?>부터 적용됩니다.</p>
</div></section>
<?= $this->endSection() ?>
