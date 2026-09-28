<?php

if (! function_exists('asset')) {
    /**
     * public/ 아래 정적 파일 주소. 파일이 바뀌면 브라우저 캐시가 갱신되도록 수정 시각을 붙인다.
     * 예) asset('assets/css/site.css') → https://.../kbc/assets/css/site.css?v=1727461234
     */
    function asset(string $path): string
    {
        $file = FCPATH . ltrim($path, '/');
        $url  = base_url($path);

        return is_file($file) ? $url . '?v=' . filemtime($file) : $url;
    }
}

if (! function_exists('setting')) {
    /**
     * 사이트 설정값. 값이 없으면 $default 반환
     */
    function setting(array $settings, string $key, ?string $default = null): ?string
    {
        return $settings[$key] ?? $default;
    }
}

if (! function_exists('format_phone')) {
    /**
     * 연락처를 하이픈 형식으로 통일. 형식이 맞지 않으면 null
     * 예) 01012345678 → 010-1234-5678, 0212345678 → 02-1234-5678, 15881234 → 1588-1234
     */
    function format_phone(string $value): ?string
    {
        $digits = preg_replace('/\D/', '', $value);

        if (preg_match('/^(02)(\d{3,4})(\d{4})$/', $digits, $m)
            || preg_match('/^(0\d{2})(\d{3,4})(\d{4})$/', $digits, $m)) {
            return $m[1] . '-' . $m[2] . '-' . $m[3];
        }

        if (preg_match('/^(1\d{3})(\d{4})$/', $digits, $m)) {
            return $m[1] . '-' . $m[2];
        }

        return null;
    }
}

if (! function_exists('has_session')) {
    /**
     * 세션 쿠키가 있을 때만 true.
     * 방문자마다 세션 파일이 생기지 않도록, 폼 오류/입력값 복원은 이 경우에만 세션을 읽는다.
     */
    function has_session(): bool
    {
        return isset($_COOKIE[config('Session')->cookieName]);
    }
}

if (! function_exists('form_old')) {
    function form_old(string $key, string $default = ''): string
    {
        return has_session() ? (string) old($key, $default) : $default;
    }
}

if (! function_exists('form_error')) {
    /**
     * 칸별 오류 문구 ('form' 은 폼 전체 안내)
     */
    function form_error(string $key): ?string
    {
        if (! has_session()) {
            return null;
        }

        $errors = session('errors');

        return is_array($errors) ? ($errors[$key] ?? null) : null;
    }
}
