<?php
/**
 * 홈 - 섹션별 파일을 순서대로 불러온다. 순서를 바꾸거나 빼려면 여기만 수정.
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <?= $this->include('home/hero') ?>
    <?= $this->include('home/fields') ?>
    <?= $this->include('home/courses') ?>
    <?= $this->include('home/crew') ?>
    <?= $this->include('home/steps') ?>
    <?= $this->include('home/career') ?>
    <?= $this->include('home/news') ?>
    <?= $this->include('home/contact') ?>
<?= $this->endSection() ?>
