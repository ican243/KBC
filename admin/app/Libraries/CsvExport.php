<?php

namespace App\Libraries;

use CodeIgniter\HTTP\DownloadResponse;

/**
 * CSV 파일 만들기 (엑셀에서 한글이 깨지지 않도록 UTF-8 BOM 추가)
 */
class CsvExport
{
    /**
     * @param list<string>       $headers 첫 줄 제목
     * @param list<list<string>> $rows
     */
    public static function download(string $filename, array $headers, array $rows): DownloadResponse
    {
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, $headers, ',', '"', '');

        foreach ($rows as $row) {
            fputcsv($handle, array_map(self::safeCell(...), $row), ',', '"', '');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response()->download($filename, $csv)->setContentType('text/csv; charset=UTF-8');
    }

    /**
     * 엑셀 수식 실행 방지 (=, +, -, @ 로 시작하는 값 앞에 ' 추가)
     * 연락처(010-...)처럼 숫자로 시작하는 값은 그대로 둔다.
     */
    private static function safeCell(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    }
}
