<?php
/**
 * FAQ 등록/수정
 *
 * @var ?int  $id
 * @var array $row
 */
use App\Controllers\Faqs;

$action = $id === null ? site_url('faqs') : site_url('faqs/' . $id);
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1><?= esc($title) ?></h1>

<form class="box form-grid" method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <label><b>분류</b>
        <input type="text" name="category" list="faq-categories" value="<?= esc(field_value($row, 'category'), 'attr') ?>" maxlength="50">
        <datalist id="faq-categories">
            <?php foreach (Faqs::CATEGORIES as $cat): ?><option value="<?= esc($cat, 'attr') ?>"><?php endforeach ?>
        </datalist>
        <?= field_error('category') ?>
    </label>
    <label><b>순서</b>
        <input type="number" name="sort_order" value="<?= esc(field_value($row, 'sort_order'), 'attr') ?>">
        <?= field_error('sort_order') ?>
    </label>
    <label class="full"><b>질문 *</b>
        <input type="text" name="question" value="<?= esc(field_value($row, 'question'), 'attr') ?>" required maxlength="255">
        <?= field_error('question') ?>
    </label>
    <label class="full"><b>답변 *</b>
        <span class="help">일반 글자만 입력됩니다. "취업 보장", "소득 보장" 같은 표현은 쓰지 않습니다 (기획안).</span>
        <textarea name="answer" rows="8" required maxlength="5000"><?= esc(field_value($row, 'answer')) ?></textarea>
        <?= field_error('answer') ?>
    </label>
    <?= $this->include('partials/publish_check') ?>

    <div class="form-actions full">
        <button class="btn" type="submit">저장</button>
        <a class="btn btn-line" href="<?= site_url('faqs') ?>">취소</a>
    </div>
</form>
<?= $this->endSection() ?>
