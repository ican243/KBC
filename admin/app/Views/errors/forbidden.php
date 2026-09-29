<?php
/**
 * 권한 없음 (담당자가 대표 관리자 전용 기능에 접근)
 */
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="box">
    <h1>권한이 없습니다</h1>
    <p>이 기능은 <b>대표 관리자</b>만 사용할 수 있습니다. 필요하면 대표 관리자에게 요청해 주세요.</p>
    <a class="btn btn-line" href="<?= site_url('/') ?>">대시보드로</a>
</div>
<?= $this->endSection() ?>
