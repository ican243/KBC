<?php
/**
 * 진로 연계
 * 출처: 기획안 2·3·5장, 협업 제안서 6장. 공개 문구 규칙은 기획안 8장.
 * - "취업 보장", "수료 즉시 취업", 소득 금액 표현은 사용하지 않는다.
 * - 직업소개로 보일 수 있는 "알선", "취업 소개" 표현은 쓰지 않는다 (신고·등록 요건 검토 전).
 *
 * @var array $settings
 */
$steps = [
    ['포트폴리오 정리', '수료 포트폴리오와 방송 실습 결과를 정리합니다.'],
    ['업체 설명회', '제휴 업체의 설명회에 참여할 수 있습니다.'],
    ['면접·현장 테스트', '희망자에 한해 면접·오디션·현장 테스트로 연결합니다.'],
];
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark">진로 연계</span>
    <h1>수료 후, 현장으로 이어지는 길</h1>
    <p><?= esc(setting($settings, 'career_notice', '진로 연계 체계 구축 및 업체 협약 추진 중')) ?></p>
</div></section>

<section class="section"><div class="wrap narrow prose">
    <p>커머스·라이브 방송 운영업체와 <b>면접 연계 협약을 추진 중</b>입니다. 제휴 확정 후 참여 조건을 안내합니다.</p>

    <h2 class="sub-title">연계 절차</h2>
    <ol class="flow-list">
        <?php foreach ($steps as $i => [$name, $desc]): ?>
        <li><em><?= $i + 1 ?></em><div><b><?= esc($name) ?></b><p><?= esc($desc) ?></p></div></li>
        <?php endforeach ?>
    </ol>
    <p class="notice-box">채용 여부는 각 업체의 심사와 채용 조건에 따릅니다.</p>

    <h2 class="sub-title">개인정보 보호</h2>
    <p>상담 정보는 채용 업체에 <b>자동으로 전달하지 않습니다.</b> 수료 후 지원 의사를 별도로 확인하고, 제공 항목·대상·목적을 안내한 뒤 진행합니다.</p>

    <h2 class="sub-title">제휴 업체</h2>
    <p>협약 업체명과 참여 가능 인원은 확정 후 공개합니다.</p>
    <p class="muted">교육 공간·강사·채용 협력을 원하는 기관은 <a href="<?= site_url('partner') ?>">협력 제안</a>을 남겨 주세요.</p>

    <div class="cta-box">
        <h2>과정과 진로가 궁금하다면</h2>
        <div class="card-actions">
            <a class="btn btn-live" href="<?= site_url('contact') ?>">사전 상담 신청</a>
            <a class="btn btn-line" href="<?= site_url('courses') ?>">교육과정 보기</a>
        </div>
    </div>
</div></section>
<?= $this->endSection() ?>
