<?php
/**
 * 홈 하단 사전 상담 신청 (기획안 5장)
 *
 * @var array $courses
 */
?>
<section id="contact" class="section"><div class="wrap contact">
    <aside class="reveal">
        <span class="label">상담 문의</span>
        <h2>사전 상담 신청</h2>
        <p>남겨주신 연락처로 상담 담당자가 연락드립니다.</p>
        <div class="side-card">
            <b>교육 공간·강사·채용 협력</b><br>
            기관 협력은 별도 제안서로 받습니다.<br>
            <a href="<?= site_url('partner') ?>" style="text-decoration:underline">협력 제안하기 →</a>
        </div>
    </aside>

    <?= view('partials/consult_form', ['returnTo' => 'home', 'selectedCourse' => '', 'entryPage' => 'home']) ?>
</div></section>
