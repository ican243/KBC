<?php
/**
 * @var int   $newConsults
 * @var int   $newPartners
 * @var int   $todayCount
 * @var int   $failedMails
 * @var array $recentConsults
 * @var array $recentPartners
 */
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1><?= esc($adminName) ?>님, 환영합니다.</h1>

<div class="stats">
    <a class="stat" href="<?= site_url('consults?status=new') ?>">
        <span class="stat-icon i-blue"><?= icon('chat') ?></span><b><?= $newConsults ?></b><span>신규 상담 문의</span></a>
    <a class="stat" href="<?= site_url('partners?status=new') ?>">
        <span class="stat-icon i-purple"><?= icon('briefcase') ?></span><b><?= $newPartners ?></b><span>신규 협력 제안</span></a>
    <div class="stat">
        <span class="stat-icon i-cyan"><?= icon('clock') ?></span><b><?= $todayCount ?></b><span>오늘 접수</span></div>
    <a class="stat<?= $failedMails > 0 ? ' warn' : '' ?>" href="<?= site_url('notifications?status=failed') ?>">
        <span class="stat-icon <?= $failedMails > 0 ? 'i-red' : 'i-green' ?>"><?= icon($failedMails > 0 ? 'alert' : 'check') ?></span><b><?= $failedMails ?></b><span>메일 발송 실패 (7일)</span></a>
</div>

<div class="grid2">
    <div class="box">
        <div class="card-head"><h2>최근 상담 문의</h2><a href="<?= site_url('consults') ?>">전체 보기 <?= icon('arrow') ?></a></div>
        <?php if ($recentConsults === []): ?>
            <p class="empty"><?= icon('inbox', 'ic empty-ic') ?>접수된 상담 문의가 없습니다.</p>
        <?php else: ?>
        <div class="table-wrap"><table>
            <?php foreach ($recentConsults as $r): ?>
            <tr<?= $r['status'] === 'new' ? ' class="is-new"' : '' ?>>
                <td><a href="<?= site_url('consults/' . $r['id']) ?>"><?= esc($r['receipt_no']) ?></a></td>
                <td><?= esc($r['name']) ?></td>
                <td><?= status_badge($r['status']) ?></td>
                <td><?= dt($r['created_at'], 'm-d H:i') ?></td>
            </tr>
            <?php endforeach ?>
        </table></div>
        <?php endif ?>
    </div>
    <div class="box">
        <div class="card-head"><h2>최근 협력 제안</h2><a href="<?= site_url('partners') ?>">전체 보기 <?= icon('arrow') ?></a></div>
        <?php if ($recentPartners === []): ?>
            <p class="empty"><?= icon('inbox', 'ic empty-ic') ?>접수된 협력 제안이 없습니다.</p>
        <?php else: ?>
        <div class="table-wrap"><table>
            <?php foreach ($recentPartners as $r): ?>
            <tr<?= $r['status'] === 'new' ? ' class="is-new"' : '' ?>>
                <td><a href="<?= site_url('partners/' . $r['id']) ?>"><?= esc($r['receipt_no']) ?></a></td>
                <td><?= esc($r['org_name']) ?></td>
                <td><?= status_badge($r['status']) ?></td>
                <td><?= dt($r['created_at'], 'm-d H:i') ?></td>
            </tr>
            <?php endforeach ?>
        </table></div>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
