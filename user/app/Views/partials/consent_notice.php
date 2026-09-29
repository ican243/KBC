<?php
/**
 * 폼 동의 안내 (개인정보 보호법 제15조: 목적, 항목, 보유 기간, 거부 권리)
 * "내용 보기"를 누르면 그 자리에서 펼쳐진다.
 *
 * @var array  $settings
 * @var string $noticeType consult | partner | marketing
 */
$retention = setting($settings, 'inquiry_retention') ?? '준비 중';
?>
<details class="consent">
    <summary>내용 보기</summary>
    <?php if ($noticeType === 'marketing'): ?>
        <dl>
            <dt>목적</dt><dd>설명회·모집 일정 등 홍보성 안내</dd>
            <dt>항목</dt><dd>이름, 연락처</dd>
            <dt>보유 기간</dt><dd>동의 철회 시까지</dd>
            <dt>거부 권리</dt><dd>동의하지 않아도 상담 신청은 가능합니다.</dd>
        </dl>
    <?php else: ?>
        <dl>
            <dt>목적</dt><dd><?= $noticeType === 'partner' ? '협력 제안 검토 및 회신' : '교육 상담 접수 및 상담 연락' ?></dd>
            <dt>항목</dt>
            <dd>
                <?= $noticeType === 'partner'
                    ? '기관명, 담당자 이름, 연락처, 제휴 유형, 제안 내용'
                    : '(필수) 이름, 연락처 (선택) 관심 과정, 희망 연락시간, 문의내용' ?>
                / 접속 IP, 브라우저 정보, 유입 경로
            </dd>
            <dt>보유 기간</dt><dd><?= esc($retention) ?></dd>
            <dt>거부 권리</dt><dd>동의를 거부할 수 있으며, 거부 시 <?= $noticeType === 'partner' ? '협력 제안' : '상담 신청' ?>이 제한됩니다.</dd>
        </dl>
        <p><a href="<?= site_url('privacy') ?>">개인정보 처리방침 전체 보기</a></p>
    <?php endif ?>
</details>
