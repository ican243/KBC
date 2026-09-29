<?php

use App\Libraries\InquiryStatus;

if (! function_exists('asset')) {
    /**
     * public/ 아래 정적 파일 주소 (수정 시각을 붙여 캐시 갱신)
     */
    function asset(string $path): string
    {
        $file = FCPATH . ltrim($path, '/');
        $url  = base_url($path);

        return is_file($file) ? $url . '?v=' . filemtime($file) : $url;
    }
}

if (! function_exists('status_badge')) {
    function status_badge(string $status): string
    {
        return '<span class="badge badge-' . esc($status, 'attr') . '">' . esc(InquiryStatus::label($status)) . '</span>';
    }
}

if (! function_exists('dt')) {
    /**
     * 날짜 표시 (없으면 -)
     */
    function dt(?string $value, string $format = 'Y-m-d H:i'): string
    {
        return $value ? date($format, strtotime($value)) : '-';
    }
}
