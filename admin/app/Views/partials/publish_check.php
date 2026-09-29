<?php
/**
 * 공개 여부 체크박스 (체크 해제도 전송되도록 hidden 0 을 먼저 둔다)
 *
 * @var array $row
 */
?>
<label class="check full">
    <input type="hidden" name="is_published" value="0">
    <input type="checkbox" name="is_published" value="1"<?= field_value($row, 'is_published') === '1' ? ' checked' : '' ?>>
    홈페이지에 공개
</label>
