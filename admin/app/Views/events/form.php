<?php
/**
 * 설명회 등록/수정
 *
 * @var ?int  $id
 * @var array $row
 */
$action = $id === null ? site_url('events') : site_url('events/' . $id);
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1><?= esc($title) ?></h1>

<form class="box form-grid" method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <label class="full"><b>제목 *</b>
        <input type="text" name="title" value="<?= esc(field_value($row, 'title'), 'attr') ?>" required maxlength="200" placeholder="예) 10월 입학 설명회">
        <?= field_error('title') ?>
    </label>
    <label><b>일시 *</b>
        <input type="datetime-local" name="event_at" value="<?= esc(old('event_at', datetime_local($row['event_at'] ?? null)), 'attr') ?>" required>
        <?= field_error('event_at') ?>
    </label>
    <label><b>장소</b>
        <input type="text" name="place" value="<?= esc(field_value($row, 'place'), 'attr') ?>" maxlength="200">
        <?= field_error('place') ?>
    </label>
    <label class="full"><b>설명</b>
        <textarea name="description" maxlength="2000"><?= esc(field_value($row, 'description')) ?></textarea>
        <?= field_error('description') ?>
    </label>
    <?= $this->include('partials/publish_check') ?>
    <p class="help full">지난 일정은 홈페이지에서 자동으로 빠집니다.</p>

    <div class="form-actions full">
        <button class="btn" type="submit">저장</button>
        <a class="btn btn-line" href="<?= site_url('events') ?>">취소</a>
    </div>
</form>
<?= $this->endSection() ?>
