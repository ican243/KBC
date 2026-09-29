<?php
/**
 * 페이지 상단 하위 탭 (예: 소개 | 강사진)
 *
 * @var array  $tabs    [[라벨, 주소], ...]
 * @var string $active  현재 탭 라벨
 */
?>
<nav class="subnav" aria-label="하위 메뉴">
    <?php foreach ($tabs as [$label, $url]): ?>
        <a href="<?= $url ?>"<?= $label === $active ? ' class="on" aria-current="page"' : '' ?>><?= esc($label) ?></a>
    <?php endforeach ?>
</nav>
