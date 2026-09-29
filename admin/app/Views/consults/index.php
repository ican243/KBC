<?php
/**
 * 상담 문의 목록
 *
 * @var array                       $rows
 * @var \CodeIgniter\Pager\Pager    $pager
 * @var int                         $total
 * @var array                       $filters
 * @var array                       $options  statuses, sources, courses
 */
use App\Libraries\SourceLabel;
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1>상담 문의</h1>

<form class="box filters" method="get" action="<?= site_url('consults') ?>">
    <label>상태
        <select name="status">
            <option value="">전체</option>
            <?php foreach ($options['statuses'] as $key => $label): ?>
            <option value="<?= esc($key, 'attr') ?>"<?= $filters['status'] === $key ? ' selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach ?>
        </select>
    </label>
    <label>관심 과정
        <select name="course_id">
            <option value="">전체</option>
            <?php foreach ($options['courses'] as $id => $title): ?>
            <option value="<?= esc($id, 'attr') ?>"<?= $filters['course_id'] === (string) $id ? ' selected' : '' ?>><?= esc($title) ?></option>
            <?php endforeach ?>
            <option value="none"<?= $filters['course_id'] === 'none' ? ' selected' : '' ?>>미정</option>
        </select>
    </label>
    <label>유입 경로
        <select name="source">
            <option value="">전체</option>
            <?php foreach ($options['sources'] as $label): ?>
            <option value="<?= esc($label, 'attr') ?>"<?= $filters['source'] === $label ? ' selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach ?>
        </select>
    </label>
    <label>접수일(부터) <input type="date" name="from" value="<?= esc($filters['from'], 'attr') ?>"></label>
    <label>접수일(까지) <input type="date" name="to" value="<?= esc($filters['to'], 'attr') ?>"></label>
    <label>검색 <input type="search" name="q" value="<?= esc($filters['q'], 'attr') ?>" placeholder="이름, 연락처, 접수번호"></label>
    <button class="btn" type="submit">조회</button>
    <a class="btn btn-line" href="<?= site_url('consults') ?>">초기화</a>
</form>

<div class="meta">
    <span>총 <?= number_format($total) ?>건</span>
    <a class="btn btn-line" href="<?= site_url('consults/export') . '?' . http_build_query(array_filter(array_diff_key($filters, ['source_values' => 1]))) ?>">CSV 내려받기</a>
</div>

<div class="table-wrap"><table>
    <thead><tr><th>접수번호</th><th>접수일시</th><th>이름</th><th>연락처</th><th>관심 과정</th><th>유입</th><th>상태</th><th>담당자</th></tr></thead>
    <tbody>
    <?php if ($rows === []): ?>
        <tr><td colspan="8" class="empty">조건에 맞는 문의가 없습니다.</td></tr>
    <?php endif ?>
    <?php foreach ($rows as $r): ?>
        <tr<?= $r['status'] === 'new' ? ' class="is-new"' : '' ?>>
            <td><a href="<?= site_url('consults/' . $r['id']) ?>"><?= esc($r['receipt_no']) ?></a></td>
            <td><?= dt($r['created_at']) ?></td>
            <td><?= esc($r['name']) ?></td>
            <td><?= esc($r['phone']) ?></td>
            <td><?= esc($r['course_title'] ?? '미정') ?></td>
            <td title="<?= esc($r['source'] ?? '', 'attr') ?>"><?= esc(SourceLabel::label($r['source'])) ?></td>
            <td><?= status_badge($r['status']) ?></td>
            <td><?= esc($r['admin_name'] ?? '-') ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table></div>

<?= $pager->links() ?>
<?= $this->endSection() ?>
