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

if (! function_exists('field_value')) {
    /**
     * 폼 값: 저장 실패로 돌아왔으면 입력했던 값, 아니면 DB 값
     */
    function field_value(array $row, string $key): string
    {
        return (string) old($key, $row[$key] ?? '');
    }
}

if (! function_exists('field_error')) {
    function field_error(string $key): string
    {
        $errors = session('errors');

        return is_array($errors) && isset($errors[$key])
            ? '<p class="field-error">' . esc($errors[$key]) . '</p>'
            : '';
    }
}

if (! function_exists('datetime_local')) {
    /**
     * DB 날짜(2026-10-01 14:00:00) → 입력칸 형식(2026-10-01T14:00)
     */
    function datetime_local(?string $value): string
    {
        return $value ? date('Y-m-d\TH:i', strtotime($value)) : '';
    }
}

if (! function_exists('public_url')) {
    /**
     * 공개 홈페이지 주소 (.env site.publicURL)
     */
    function public_url(string $path = ''): string
    {
        return rtrim((string) env('site.publicURL', ''), '/') . '/' . ltrim($path, '/');
    }
}

if (! function_exists('is_owner')) {
    /**
     * 대표 관리자인지 (메뉴·버튼 표시용. 실제 권한 확인은 AdminAuth 필터가 한다)
     */
    function is_owner(): bool
    {
        return session('admin_role') === 'owner';
    }
}
