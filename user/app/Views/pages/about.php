<?php
/**
 * 아카데미 소개
 * 출처: 기획안 1·3장, 협업 제안서 1~3장. 공개 문구 규칙은 기획안 8장.
 * - 협업 학원 이름, 강사 실명, 소득·취업 관련 표현은 넣지 않는다.
 * - 공개 용어는 "협업형 라이브 방송"을 쓴다.
 *
 * @var array $settings
 * @var array $fields   공개 중인 강사 분야
 */
$steps = [
    ['방송 콘셉트 설정', '나에게 맞는 방송 분야와 캐릭터 찾기'],
    ['카메라 앞 실습', '소규모 조별 송출 중심 실습'],
    ['녹화 피드백', '녹화 영상으로 진행과 표현 점검'],
    ['포트폴리오 제작', '수료 영상과 산출물 완성'],
];
$basics = ['콘텐츠 기획', '저작권과 음원 사용', '출연자 권리', '채팅 관리', '광고·협찬 표시', '판매 정보의 정확한 전달'];
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark">아카데미</span>
    <h1>KBC아카데미 소개</h1>
    <p>개인방송·커머스방송·협업형 라이브 방송 진행자를 양성하는 실습 중심 교육</p>
    <?= view('partials/subnav', ['tabs' => [['소개', site_url('about')], ['강사진', site_url('instructors')]], 'active' => '소개']) ?>
</div></section>

<section class="section"><div class="wrap narrow prose">
    <h2 class="sub-title">교육 목표</h2>
    <p>KBC아카데미는 유튜브·틱톡 등 SNS의 <b>개인방송, 커머스방송, 협업형 라이브 방송 진행자</b>를 양성하는 실습 중심 교육 브랜드입니다.</p>
    <p>춤과 노래에 강점을 가진 분뿐 아니라, <b>진행력과 상품 설명 능력</b>을 갖춘 분도 자신에게 맞는 방송 분야를 찾을 수 있도록 돕습니다.</p>
    <p class="notice-box">특정 플랫폼의 수익 창출을 보장하는 교육이 아니라, 방송 형식별로 필요한 출연·제작·진행·판매 커뮤니케이션 역량을 실습하는 교육입니다.</p>

    <h2 class="sub-title">강남 협업 모델</h2>
    <p>강남 지역 댄스·공연예술 교육기관의 교육 공간과 강사진에 <b>방송 제작·커머스·SNS 교육을 결합한</b> 공동 교육과정을 준비하고 있습니다.</p>
    <?php if (setting($settings, 'status_notice')): ?>
        <p class="status-line"><span class="label">현재 상태</span> <?= esc(setting($settings, 'status_notice')) ?></p>
    <?php endif ?>

    <?php if ($fields !== []): ?>
    <h2 class="sub-title">교육 분야</h2>
    <ul class="field-grid">
        <?php foreach ($fields as $f): ?>
            <li><b><?= esc($f['field']) ?></b><?php if ($f['description']): ?><span><?= esc($f['description']) ?></span><?php endif ?></li>
        <?php endforeach ?>
    </ul>
    <p class="more"><a href="<?= site_url('instructors') ?>">강사진 보기 →</a></p>
    <?php endif ?>

    <h2 class="sub-title">실습·평가 흐름</h2>
    <div class="steps steps-compact">
        <?php foreach ($steps as $i => [$name, $desc]): ?>
        <div class="step"><em><?= $i + 1 ?></em><h3><?= esc($name) ?></h3><p><?= esc($desc) ?></p></div>
        <?php endforeach ?>
    </div>
    <p>실습은 <b>소규모 조별 송출과 녹화 피드백</b>을 중심으로 운영합니다. 실제 방송 여부는 연령·플랫폼 이용 요건에 맞춰 결정합니다.</p>

    <h2 class="sub-title">함께 배우는 기본</h2>
    <div class="tags tags-light">
        <?php foreach ($basics as $b): ?><span><?= esc($b) ?></span><?php endforeach ?>
    </div>
    <p>커머스 실습에서는 상품 특성·가격·배송·환불 안내를 확인하고, 협업형 라이브 실습에서는 출연자 간 합의와 안전한 진행 규칙을 먼저 교육합니다.</p>

    <div class="cta-box">
        <?php if (setting($settings, 'launch_notice')): ?><p class="live-note"><i></i><?= esc(setting($settings, 'launch_notice')) ?></p><?php endif ?>
        <h2>교육 과정과 일정이 궁금하다면</h2>
        <div class="card-actions">
            <a class="btn btn-live" href="<?= site_url('contact') ?>">사전 상담 신청</a>
            <a class="btn btn-line" href="<?= site_url('courses') ?>">교육과정 보기</a>
        </div>
    </div>
</div></section>
<?= $this->endSection() ?>
