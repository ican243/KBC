<?php

namespace App\Libraries;

use App\Models\CourseModel;
use App\Models\PartnerInquiryModel;
use App\Models\SiteSettingModel;

/**
 * 새 문의 운영자 메일 알림
 *
 * - 받는 주소: site_settings.notify_email
 * - 개인정보 최소화: 이름은 가운데를 가리고, 연락처·문의내용은 넣지 않는다 (관리자 화면에서 확인)
 * - 결과(성공/실패, 오류 내용)는 notification_logs 에 남긴다
 */
class InquiryNotifier
{
    /**
     * 방문자에게 응답을 먼저 보낸 뒤 발송하도록 예약한다.
     */
    public static function queue(string $type, string $receiptNo, array $row): void
    {
        register_shutdown_function(static function () use ($type, $receiptNo, $row) {
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }

            (new self())->send($type, $receiptNo, $row);
        });
    }

    public function send(string $type, string $receiptNo, array $row): bool
    {
        $to = (new SiteSettingModel())->getMap()['notify_email'] ?? null;
        [$subject, $body] = $this->compose($type, $receiptNo, $row);

        if ($to === null) {
            $this->log($type, $row['id'] ?? null, $receiptNo, '', $subject, false, '받는 주소(notify_email)가 설정되지 않았습니다.');

            return false;
        }

        if (config('Email')->SMTPPass === '') {
            $this->log($type, $row['id'] ?? null, $receiptNo, $to, $subject, false, 'SMTP 비밀번호(.env email.SMTPPass)가 설정되지 않았습니다.');

            return false;
        }

        try {
            $email = service('email');
            $email->setTo($to);
            $email->setSubject($subject);
            $email->setMessage($body);

            $sent  = $email->send(false);
            $error = $sent ? null : $this->cleanError($email->printDebugger([]));
        } catch (\Throwable $e) {
            $sent  = false;
            $error = $this->cleanError($e->getMessage());
        }

        $this->log($type, $row['id'] ?? null, $receiptNo, $to, $subject, $sent, $error);

        return $sent;
    }

    /**
     * @return array{0: string, 1: string} [제목, 본문]
     */
    private function compose(string $type, string $receiptNo, array $row): array
    {
        $time = date('Y-m-d H:i');

        if ($type === 'partner') {
            $typeLabel = PartnerInquiryModel::TYPES[$row['partner_type']] ?? $row['partner_type'];

            return [
                "[KBC 협력 제안] {$receiptNo}",
                "새 협력 제안이 접수되었습니다.\n\n"
                . "접수번호: {$receiptNo}\n"
                . "기관명: {$row['org_name']}\n"
                . '담당자: ' . self::maskName($row['contact_name']) . "\n"
                . "제휴 유형: {$typeLabel}\n"
                . "접수 시각: {$time}\n\n"
                . "연락처와 제안 내용은 관리자 화면에서 확인해 주세요.\n",
            ];
        }

        $course = '미정';
        if (! empty($row['course_id'])) {
            $course = (new CourseModel())->find($row['course_id'])['title'] ?? '미정';
        }

        return [
            "[KBC 상담 신청] {$receiptNo}",
            "새 상담 신청이 접수되었습니다.\n\n"
            . "접수번호: {$receiptNo}\n"
            . '신청자: ' . self::maskName($row['name']) . "\n"
            . "관심 과정: {$course}\n"
            . "접수 시각: {$time}\n\n"
            . "연락처와 문의내용은 관리자 화면에서 확인해 주세요.\n",
        ];
    }

    /**
     * 이름 가운데 가리기  홍길동 → 홍*동, 홍길 → 홍*, 남궁민수 → 남**수
     */
    public static function maskName(string $name): string
    {
        $length = mb_strlen($name);

        if ($length <= 1) {
            return '*';
        }
        if ($length === 2) {
            return mb_substr($name, 0, 1) . '*';
        }

        return mb_substr($name, 0, 1) . str_repeat('*', $length - 2) . mb_substr($name, -1);
    }

    /**
     * SMTP 대화 기록을 한 줄로 정리하고, 핵심 원인을 맨 앞에 둔다.
     */
    private function cleanError(string $raw): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($raw)));

        if (preg_match('/SMTP error was encountered: (\d{3}[^:]*?)(?= \w+:|$)/', $text, $m)) {
            $text = '원인: ' . trim($m[1]) . ' | ' . $text;
        }

        return mb_substr($text, 0, 1000);
    }

    private function log(string $type, ?int $relatedId, string $receiptNo, string $to, string $subject, bool $sent, ?string $error): void
    {
        try {
            db_connect()->table('notification_logs')->insert([
                'channel'       => 'email',
                'recipient'     => $to,
                'subject'       => $subject,
                'related_type'  => $type,
                'related_id'    => $relatedId,
                'status'        => $sent ? 'sent' : 'failed',
                'error_message' => $error,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[notify] 발송 기록 저장 실패 (' . $receiptNo . '): ' . $e->getMessage());
        }
    }
}
