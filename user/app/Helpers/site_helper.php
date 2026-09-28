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
