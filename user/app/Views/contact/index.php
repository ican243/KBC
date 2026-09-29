<?php
/**
 * 상담 문의 페이지 (홈 하단 폼과 같은 조각 사용)
 *
 * @var array  $courses
 * @var string $selectedCourse
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-head"><div class="wrap">
    <span class="label label-dark">상담 문의</span>
    <h1>사전 상담 신청</h1>
    <p>남겨주신 연락처로 상담 담당자가 연락드립니다.</p>
</div></section>

<section class="section"><div class="wrap contact">
    <aside>
        <div class="side-card">
            <b>상담은 이렇게 진행됩니다</b><br>
            ① 상담 신청 → ② 담당자 연락 → ③ 과정·일정 안내
        </div>
        <div class="side-card">
            <b>교육 공간·강사·채용 협력</b><br>
            기관 협력은 별도 제안서로 받습니다.<br>
            <a href="<?= site_url('partner') ?>" style="text-decoration:underline">협력 제안하기 →</a>
        </div>
    </aside>
    <?= view('partials/consult_form', ['returnTo' => 'contact', 'selectedCourse' => $selectedCourse]) ?>
</div></section>
<?= $this->endSection() ?>
