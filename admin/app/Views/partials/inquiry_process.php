<?php
/**
 * 문의 처리 (상태·담당자·메모) + 처리 이력 + 삭제 - 상담/협력 공용
 *
 * @var array  $row
 * @var array  $history
 * @var array  $admins   [id => 이름]
 * @var array  $statuses [key => 라벨]
 * @var string $path     consults | partners
 */
?>
<div class="box">
    <h2>처리하기</h2>
    <form method="post" action="<?= site_url($path . '/' . $row['id']) ?>" class="process">
        <?= csrf_field() ?>
        <label>상태
            <select name="status">
                <?php foreach ($statuses as $key => $label): ?>
                <option value="<?= esc($key, 'attr') ?>"<?= $row['status'] === $key ? ' selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach ?>
            </select>
        </label>
        <label>담당자
            <select name="assigned_admin_id">
                <option value="">지정 안 함</option>
                <?php foreach ($admins as $id => $name): ?>
                <option value="<?= esc($id, 'attr') ?>"<?= (string) $row['assigned_admin_id'] === (string) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                <?php endforeach ?>
            </select>
        </label>
        <label class="full">메모 (통화 내용, 다음 연락 예정 등)
            <textarea name="memo" maxlength="2000"></textarea>
        </label>
        <div class="full"><button class="btn" type="submit">저장</button></div>
    </form>
</div>

<div class="box">
    <h2>처리 이력</h2>
    <?php if ($history === []): ?>
        <p class="empty">아직 처리 이력이 없습니다.</p>
    <?php else: ?>
    <ul class="history">
        <?php foreach ($history as $h): ?>
        <li>
            <small><?= dt($h['created_at']) ?> · <?= esc($h['admin_name'] ?? '(삭제된 계정)') ?></small>
            <?php if ($h['status_from'] !== null): ?>
                · <?= status_badge($h['status_from']) ?> → <?= status_badge($h['status_to']) ?>
            <?php endif ?>
            <?php if ($h['memo'] !== null): ?><p><?= esc($h['memo']) ?></p><?php endif ?>
        </li>
        <?php endforeach ?>
    </ul>
    <?php endif ?>
</div>

<?php if (is_owner()): ?>
<form method="post" action="<?= site_url($path . '/' . $row['id'] . '/delete') ?>"
      data-confirm="이 문의를 삭제 처리할까요?&#10;목록에서 사라지며, 데이터는 복구를 위해 보관됩니다.">
    <?= csrf_field() ?>
    <button class="btn btn-danger" type="submit">삭제 처리</button>
    <a class="btn btn-line" href="<?= site_url($path) ?>">목록으로</a>
</form>
<?php else: ?>
<a class="btn btn-line" href="<?= site_url($path) ?>">목록으로</a>
<?php endif ?>
