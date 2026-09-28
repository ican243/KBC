<?php
/**
 * 교육 경험 흐름 (기획안 3장 4번)
 */
$steps = [
    ['방송 콘셉트 설정', '나에게 맞는 방송 분야와 캐릭터 찾기'],
    ['카메라 앞 실습', '소규모 조별 송출 중심 실습'],
    ['녹화 피드백', '녹화 영상으로 진행과 표현 점검'],
    ['포트폴리오 제작', '수료 영상과 산출물 완성'],
];
?>
<section class="section section-soft"><div class="wrap">
    <div class="sec-head reveal">
        <span class="label">교육 흐름</span>
        <h2>보고 듣는 수업이 아니라<br>직접 송출하는 수업</h2>
    </div>
    <div class="steps reveal">
        <?php foreach ($steps as $i => [$name, $desc]): ?>
        <div class="step"><em><?= $i + 1 ?></em><h3><?= esc($name) ?></h3><p><?= esc($desc) ?></p></div>
        <?php endforeach ?>
    </div>
</div></section>
