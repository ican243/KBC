<?php
/**
 * 교육과정 등록/수정
 *
 * @var ?int  $id
 * @var array $row
 */
$action = $id === null ? site_url('courses') : site_url('courses/' . $id);
$text   = static fn (string $key, string $label, string $extra = '') => '<label><b>' . $label . '</b>'
    . '<input type="text" name="' . $key . '" value="' . esc(field_value($row, $key), 'attr') . '"' . $extra . '>'
    . field_error($key) . '</label>';
$area   = static fn (string $key, string $label, string $help = '') => '<label class="full"><b>' . $label . '</b>'
    . ($help !== '' ? '<span class="help">' . $help . '</span>' : '')
    . '<textarea name="' . $key . '" maxlength="2000">' . esc(field_value($row, $key)) . '</textarea>'
    . field_error($key) . '</label>';
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1><?= esc($title) ?></h1>

<form class="box form-grid" method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <?= $text('title', '과정명 *', ' required maxlength="100"') ?>
    <label><b>주소용 이름 *</b>
        <input type="text" name="slug" value="<?= esc(field_value($row, 'slug'), 'attr') ?>" required maxlength="50" pattern="[A-Za-z0-9_\-]+">
        <span class="help">영문·숫자·하이픈만. 예) basic-3m (나중에 과정 상세 주소에 사용)</span>
        <?= field_error('slug') ?>
    </label>
    <?= $text('summary', '한 줄 소개', ' maxlength="255"') ?>
    <?= $text('target', '대상', ' maxlength="255"') ?>
    <?= $text('duration', '기간', ' maxlength="50" placeholder="예) 3개월"') ?>
    <?= $text('sessions_per_week', '주당 수업', ' maxlength="50" placeholder="예) 주 2~3회 (제안)"') ?>
    <?= $area('fields', '교육 분야') ?>
    <?= $area('assignments', '실습 과제') ?>
    <?= $area('outputs', '수료 산출물') ?>

    <label class="check full">
        <input type="hidden" name="is_confirmed" value="0">
        <input type="checkbox" name="is_confirmed" value="1"<?= field_value($row, 'is_confirmed') === '1' ? ' checked' : '' ?>>
        확정된 과정 (체크 전에는 홈페이지에 "시간표·교습비 확정 전"으로 표시되고 교습비는 나오지 않습니다)
    </label>
    <?= $text('fee_text', '교습비 안내 (확정 후 표시)', ' maxlength="255" placeholder="예) 월 00만원 (교재 포함)"') ?>
    <label><b>순서</b>
        <input type="number" name="sort_order" value="<?= esc(field_value($row, 'sort_order'), 'attr') ?>">
        <?= field_error('sort_order') ?>
    </label>
    <?= $this->include('partials/publish_check') ?>

    <div class="form-actions full">
        <button class="btn" type="submit">저장</button>
        <a class="btn btn-line" href="<?= site_url('courses') ?>">취소</a>
    </div>
</form>
<p class="help">교육과정은 상담 신청 기록과 연결되어 있어 삭제할 수 없습니다. 숨기려면 공개를 해제하세요.</p>
<?= $this->endSection() ?>
