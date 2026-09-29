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
    <a class="stat" href="<?= site_url('consults?status=new') ?>"><span>신규 상담 문의</span><b><?= $newConsults ?></b></a>
    <a class="stat" href="<?= site_url('partners?status=new') ?>"><span>신규 협력 제안</span><b><?= $newPartners ?></b></a>
    <div class="stat"><span>오늘 접수</span><b><?= $todayCount ?></b></div>
    <a class="stat<?= $failedMails > 0 ? ' warn' : '' ?>" href="<?= site_url('notifications?status=failed') ?>"><span>메일 발송 실패 (7일)</span><b><?= $failedMails ?></b></a>
</div>

<div class="grid2">
    <div class="box">
        <h2>최근 상담 문의 <a href="<?= site_url('consults') ?>" style="float:right;font-weight:400;font-size:13px">전체 보기</a></h2>
        <?php if ($recentConsults === []): ?>
            <p class="empty">접수된 상담 문의가 없습니다.</p>
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
        <h2>최근 협력 제안 <a href="<?= site_url('partners') ?>" style="float:right;font-weight:400;font-size:13px">전체 보기</a></h2>
        <?php if ($recentPartners === []): ?>
            <p class="empty">접수된 협력 제안이 없습니다.</p>
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
