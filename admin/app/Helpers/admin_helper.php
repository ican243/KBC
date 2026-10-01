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

if (! function_exists('icon')) {
    /**
     * 선 아이콘 (SVG 를 화면에 직접 넣는다 - 외부 아이콘 파일·글꼴 없음)
     * 24×24 기준, 색은 글자색(currentColor)을 따른다.
     */
    function icon(string $name, string $class = 'ic'): string
    {
        static $paths = [
            'dashboard' => '<rect x="3" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1.5"/>',
            'chat'      => '<path d="M21 11.5a8.5 8.5 0 0 1-12.3 7.6L3.5 20.5l1.4-4.6A8.5 8.5 0 1 1 21 11.5z"/>',
            'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8.5 7V5.5a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2V7M3 12.5h18"/>',
            'chart'     => '<path d="M4 20V11M10 20V5M16 20v-6M3 20h18"/>',
            'book'      => '<path d="M4 19.5V5a2 2 0 0 1 2-2h14v14H6a2 2 0 0 0-2 2.5zM4 19.5A2 2 0 0 0 6 21h14v-4"/>',
            'users'     => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20.5a6.5 6.5 0 0 1 13 0M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.6a6.5 6.5 0 0 1 3.5 5.9"/>',
            'megaphone' => '<path d="M3 10v4h3l7.5 5V5L6 10H3zM17.5 9a4.2 4.2 0 0 1 0 6"/>',
            'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
            'help'      => '<circle cx="12" cy="12" r="9"/><path d="M9.6 9.4a2.5 2.5 0 1 1 3.4 2.4c-.6.3-1 .8-1 1.5v.5M12 17h.01"/>',
            'sliders'   => '<path d="M4 6h9M17 6h3M4 12h3M11 12h9M4 18h11M19 18h1"/><circle cx="15" cy="6" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="17" cy="18" r="2"/>',
            'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 7l8.5 6 8.5-6"/>',
            'shield'    => '<path d="M12 3l8 3v6c0 4.8-3.4 8-8 9-4.6-1-8-4.2-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
            'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
            'menu'      => '<path d="M4 6h16M4 12h16M4 18h16"/>',
            'close'     => '<path d="M6 6l12 12M18 6L6 18"/>',
            'logout'    => '<path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3M10 16.5l4.5-4.5L10 7.5M14.5 12H3.5"/>',
            'external'  => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
            'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.2 2"/>',
            'alert'     => '<path d="M12 3.5l9.5 17h-19L12 3.5z"/><path d="M12 10v4.5M12 17.5h.01"/>',
            'check'     => '<circle cx="12" cy="12" r="9"/><path d="M8 12.3l2.7 2.7L16 9.5"/>',
            'inbox'     => '<path d="M3 13.5L5.8 5h12.4l2.8 8.5V19a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-5.5z"/><path d="M3 13.5h5l1.2 3h5.6l1.2-3h5"/>',
            'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        ];

        return '<svg class="' . esc($class, 'attr') . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"'
            . ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
    }
}
