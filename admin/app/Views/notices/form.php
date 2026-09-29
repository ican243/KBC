<?php
/**
 * 공지 등록/수정
 *
 * @var ?int  $id
 * @var array $row
 */
$action = $id === null ? site_url('notices') : site_url('notices/' . $id);
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1><?= esc($title) ?></h1>

<form class="box form-grid" method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <label class="full"><b>제목 *</b>
        <input type="text" name="title" value="<?= esc(field_value($row, 'title'), 'attr') ?>" required maxlength="200">
        <?= field_error('title') ?>
    </label>
    <label class="full"><b>내용 *</b>
        <span class="help">일반 글자만 입력됩니다. 줄바꿈은 그대로 표시됩니다.</span>
        <textarea name="content" rows="12" required maxlength="10000"><?= esc(field_value($row, 'content')) ?></textarea>
        <?= field_error('content') ?>
    </label>
    <label><b>게시일 *</b>
        <input type="datetime-local" name="published_at" value="<?= esc(old('published_at', datetime_local($row['published_at'] ?? null)), 'attr') ?>" required>
        <span class="help">미래 날짜로 두면 그때부터 홈페이지에 나옵니다 (예약 게시).</span>
        <?= field_error('published_at') ?>
    </label>
    <?= $this->include('partials/publish_check') ?>

    <div class="form-actions full">
        <button class="btn" type="submit">저장</button>
        <a class="btn btn-line" href="<?= site_url('notices') ?>">취소</a>
    </div>
</form>
<?= $this->endSection() ?>
