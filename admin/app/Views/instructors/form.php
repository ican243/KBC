<?php
/**
 * 강사 등록/수정 (사진 업로드)
 *
 * @var ?int  $id
 * @var array $row
 */
use App\Libraries\ImageUpload;

$action   = $id === null ? site_url('instructors') : site_url('instructors/' . $id);
$consent  = (string) old('consent', ($row['consent_at'] ?? null) !== null ? '1' : '0');
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1><?= esc($title) ?></h1>

<form class="box form-grid" method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <label><b>분야 *</b>
        <input type="text" name="field" value="<?= esc(field_value($row, 'field'), 'attr') ?>" required maxlength="50" placeholder="예) 댄스·표현">
        <?= field_error('field') ?>
    </label>
    <label><b>순서</b>
        <input type="number" name="sort_order" value="<?= esc(field_value($row, 'sort_order'), 'attr') ?>">
        <?= field_error('sort_order') ?>
    </label>
    <label><b>섭외 기준</b>
        <input type="text" name="criteria" value="<?= esc(field_value($row, 'criteria'), 'attr') ?>" maxlength="255">
        <?= field_error('criteria') ?>
    </label>
    <label><b>교육 내용 (홈페이지 카드에 표시)</b>
        <input type="text" name="description" value="<?= esc(field_value($row, 'description'), 'attr') ?>" maxlength="255">
        <?= field_error('description') ?>
    </label>

    <div class="full consent-box">
        <h2>실명·사진·경력 (게시 동의 후에만 홈페이지에 표시)</h2>
        <div class="form-grid">
            <label><b>실명</b>
                <input type="text" name="name" value="<?= esc(field_value($row, 'name'), 'attr') ?>" maxlength="50">
                <?= field_error('name') ?>
            </label>
            <label><b>사진</b>
                <?php if (! empty($row['photo_path'])): ?>
                    <img class="photo-preview" src="<?= esc(public_url($row['photo_path']), 'attr') ?>" alt="현재 사진">
                    <span class="check"><input type="checkbox" name="remove_photo" value="1"> 현재 사진 삭제</span>
                <?php endif ?>
                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
                <span class="help">JPG·PNG·WEBP, <?= ImageUpload::MAX_BYTES / 1024 / 1024 ?>MB 이하. 가로 800px로 줄여 저장하고 촬영 위치 정보는 지웁니다.</span>
                <?= field_error('photo') ?>
            </label>
            <label class="full"><b>프로필</b>
                <textarea name="profile" maxlength="2000"><?= esc(field_value($row, 'profile')) ?></textarea>
                <?= field_error('profile') ?>
            </label>
            <label class="full"><b>경력 (증빙 확인한 내용만)</b>
                <textarea name="career" maxlength="2000"><?= esc(field_value($row, 'career')) ?></textarea>
                <?= field_error('career') ?>
            </label>
            <label class="check full">
                <input type="hidden" name="consent" value="0">
                <input type="checkbox" name="consent" value="1"<?= $consent === '1' ? ' checked' : '' ?>>
                실명·사진·경력 게시 동의를 받았습니다
                <?php if (! empty($row['consent_at'])): ?><small class="muted">(<?= dt($row['consent_at']) ?> 동의)</small><?php endif ?>
            </label>
        </div>
    </div>

    <?= $this->include('partials/publish_check') ?>

    <div class="form-actions full">
        <button class="btn" type="submit">저장</button>
        <a class="btn btn-line" href="<?= site_url('instructors') ?>">취소</a>
    </div>
</form>
<?= $this->endSection() ?>
