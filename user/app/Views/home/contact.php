<?php
/**
 * 사전 상담 신청 (기획안 5장)
 * 저장 기능은 다음 단계(문의 폼)에서 연결한다. 지금은 화면만 제공.
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
            <a href="#contact" style="text-decoration:underline">협력 제안하기 →</a>
        </div>
    </aside>

    <form class="form-card" method="post" action="<?= site_url('consult') ?>" data-pending>
        <?= csrf_field() ?>
        <div class="row2">
            <div class="field">
                <label for="consult-name">이름 <span class="req">*</span></label>
                <input id="consult-name" name="name" required maxlength="50" autocomplete="name">
            </div>
            <div class="field">
                <label for="consult-phone">연락처 <span class="req">*</span></label>
                <input id="consult-phone" name="phone" type="tel" required maxlength="20" placeholder="010-0000-0000" autocomplete="tel">
            </div>
        </div>

        <fieldset class="field pills-field" style="border:0;padding:0;margin:0 0 16px">
            <legend class="legend">관심 과정</legend>
            <div class="pills">
                <label><input type="radio" name="course_id" value="" checked><span>아직 모르겠어요</span></label>
                <?php foreach ($courses as $course): ?>
                <label><input type="radio" name="course_id" value="<?= esc($course['id'], 'attr') ?>"><span><?= esc($course['title']) ?></span></label>
                <?php endforeach ?>
            </div>
        </fieldset>

        <div class="field">
            <label for="consult-time">희망 연락시간</label>
            <input id="consult-time" name="contact_time" maxlength="50" placeholder="예) 평일 오후 2시 이후">
        </div>
        <div class="field">
            <label for="consult-message">문의내용</label>
            <textarea id="consult-message" name="message" maxlength="2000" placeholder="궁금한 점을 자유롭게 적어주세요"></textarea>
        </div>

        <label class="agree"><input type="checkbox" name="agree_privacy" value="1" required> <span>[필수] 개인정보 수집·이용에 동의합니다. <a href="#" style="text-decoration:underline">내용 보기</a></span></label>
        <label class="agree"><input type="checkbox" name="agree_marketing" value="1"> <span>[선택] 설명회·모집 안내 등 홍보성 연락을 받겠습니다.</span></label>

        <button class="btn btn-navy btn-block" style="margin-top:8px">상담 신청하기</button>
        <p class="form-msg" hidden>상담 신청 접수 기능은 준비 중입니다.</p>
    </form>
</div></section>

<script>
// 저장 기능 연결 전까지 전송 막기
document.querySelectorAll('form[data-pending]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        form.querySelector('.form-msg').hidden = false;
    });
});
// "이 과정 상담하기" 누르면 해당 과정 선택
document.querySelectorAll('[data-course]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var radio = document.querySelector('input[name="course_id"][value="' + btn.dataset.course + '"]');
        if (radio) radio.checked = true;
    });
});
</script>
