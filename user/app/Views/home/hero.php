<?php
/**
 * 첫 화면 (기획안 3장 1번)
 *
 * @var array $settings
 */
?>
<section class="hero"><div class="wrap">
    <div>
        <?php if (setting($settings, 'launch_notice')): ?>
        <span class="live-pill"><i></i><?= esc(setting($settings, 'launch_notice')) ?></span>
        <?php endif ?>
        <h1>방송 진행을 배우고,<br><span>실습으로 완성합니다</span></h1>
        <p>개인방송·커머스방송·협업형 라이브 진행을 위한 댄스, 보컬, 화술, 촬영, 편집, SNS 실습</p>
        <div class="actions">
            <a class="btn btn-live" href="#contact">사전 상담 신청</a>
            <a class="btn btn-white" href="#courses">과정 살펴보기</a>
        </div>
    </div>

    <!-- 세로 방송 화면 일러스트 (실제 수업 사진 확보 전) -->
    <div class="stage" aria-hidden="true">
        <div class="phone"><div class="screen">
            <div class="screen-top"><span class="live-tag"><i></i>LIVE</span><span>모의 라이브 실습</span></div>
            <div class="mic">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3M8 21h8"/></svg>
            </div>
            <div class="chat">
                <div><b>진행</b>3분 자기소개 시작합니다</div>
                <div><b>피드백</b>카메라 시선 좋아요</div>
                <div><b>실습</b>다음은 상품 소개 파트</div>
            </div>
            <div class="product"><i></i><div><b>커머스 실습</b><br>상품 특성·가격·배송 안내</div></div>
        </div></div>
        <div class="float float-1">녹화 피드백<small>조별 송출 후 바로 점검</small></div>
        <div class="float float-2">수료 포트폴리오<small>영상 산출물로 완성</small></div>
    </div>
</div></section>
