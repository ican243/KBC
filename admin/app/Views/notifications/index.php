<?php
/**
 * 알림 발송 기록
 *
 * @var array                    $rows
 * @var \CodeIgniter\Pager\Pager $pager
 * @var string                   $only
 */
$links = ['consult' => 'consults', 'partner' => 'partners'];
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1>알림 기록</h1>

<div class="meta">
    <span>
        <a href="<?= site_url('notifications') ?>"<?= $only === '' ? ' style="font-weight:700"' : '' ?>>전체</a> ·
        <a href="<?= site_url('notifications?status=failed') ?>"<?= $only === 'failed' ? ' style="font-weight:700"' : '' ?>>실패만</a>
    </span>
</div>

<div class="table-wrap"><table>
    <thead><tr><th>일시</th><th>결과</th><th>문의</th><th>받는 주소</th><th>제목</th><th>실패 원인</th></tr></thead>
    <tbody>
    <?php if ($rows === []): ?>
        <tr><td colspan="6" class="empty">기록이 없습니다.</td></tr>
    <?php endif ?>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= dt($r['created_at']) ?></td>
            <td><span class="badge badge-<?= esc($r['status'], 'attr') ?>"><?= $r['status'] === 'sent' ? '성공' : '실패' ?></span></td>
            <td>
                <?php if (isset($links[$r['related_type']]) && $r['related_id']): ?>
                    <a href="<?= site_url($links[$r['related_type']] . '/' . $r['related_id']) ?>">보기</a>
                <?php else: ?>-<?php endif ?>
            </td>
            <td><?= esc($r['recipient'] !== '' ? $r['recipient'] : '-') ?></td>
            <td><?= esc($r['subject'] ?? '-') ?></td>
            <td class="wrap"><?= esc($r['error_message'] ?? '') ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table></div>

<?= $pager->links() ?>
<?= $this->endSection() ?>
