<?php
/**
 * 콘텐츠 공통 목록 (교육과정, 공지, 설명회, FAQ)
 * 공개 전환·삭제 버튼은 formaction 으로 각자 주소에 보낸다(표 전체가 하나의 폼).
 *
 * @var string $path
 * @var array  $rows
 * @var array  $columns   [제목 => fn(array): string]
 * @var bool   $deletable
 * @var bool   $sortable
 * @var array  $confirms  [id => 삭제 확인 창 내용] (ContentController::deleteConfirm)
 */
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="head-row">
    <h1><?= esc($title) ?></h1>
    <div class="row-actions">
        <a class="btn btn-line" href="<?= esc(public_url(), 'attr') ?>" target="_blank" rel="noopener">홈페이지에서 보기</a>
        <a class="btn" href="<?= site_url($path . '/new') ?>">+ 새로 등록</a>
    </div>
</div>

<form method="post" action="<?= site_url($path . '/sort') ?>">
    <?= csrf_field() ?>
    <div class="table-wrap"><table>
        <thead><tr>
            <?php if ($sortable): ?><th>순서</th><?php endif ?>
            <?php foreach (array_keys($columns) as $head): ?><th><?= esc($head) ?></th><?php endforeach ?>
            <th>공개</th><th></th>
        </tr></thead>
        <tbody>
        <?php if ($rows === []): ?>
            <tr><td colspan="<?= count($columns) + ($sortable ? 3 : 2) ?>" class="empty">등록된 항목이 없습니다.</td></tr>
        <?php endif ?>
        <?php foreach ($rows as $r): ?>
            <tr>
                <?php if ($sortable): ?>
                    <td><input class="order-input" type="number" name="order[<?= (int) $r['id'] ?>]" value="<?= (int) $r['sort_order'] ?>"></td>
                <?php endif ?>
                <?php foreach ($columns as $render): ?><td class="wrap"><?= $render($r) ?></td><?php endforeach ?>
                <td><?= $r['is_published'] ? '<span class="badge badge-done">공개</span>' : '<span class="badge badge-hold">비공개</span>' ?></td>
                <td class="row-actions">
                    <a class="btn btn-line" href="<?= site_url($path . '/' . $r['id'] . '/edit') ?>">수정</a>
                    <button class="btn btn-line" type="submit" formaction="<?= site_url($path . '/' . $r['id'] . '/toggle') ?>">
                        <?= $r['is_published'] ? '비공개로' : '공개로' ?>
                    </button>
                    <?php if ($deletable): $c = $confirms[$r['id']]; ?>
                    <button class="btn btn-danger" type="submit" formaction="<?= site_url($path . '/' . $r['id'] . '/delete') ?>"
                            data-confirm="<?= esc($c['message'], 'attr') ?>" data-confirm-ok="<?= esc($c['ok'], 'attr') ?>" data-confirm-tone="<?= esc($c['tone'], 'attr') ?>"
                            <?= $c['unpublish'] ? 'name="unpublish" value="1"' : '' ?><?= $c['info'] ? ' data-confirm-info' : '' ?>>삭제</button>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table></div>

    <?php if ($sortable && $rows !== []): ?>
        <p class="meta"><span class="help">순서 숫자가 작을수록 홈페이지 앞쪽에 표시됩니다.</span>
            <button class="btn" type="submit">순서 저장</button></p>
    <?php endif ?>
</form>
<?= $this->endSection() ?>
