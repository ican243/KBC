<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

/**
 * 이미지 업로드 (강사 사진 등)
 *
 * 1) 5MB 이하  2) 파일 내용으로 JPG/PNG/WEBP 확인  3) 가로·세로 4000px 이하
 * 4) 휴대폰 사진 방향 바로잡기  5) 가로 800px 로 줄여 JPG 로 새로 저장 (촬영 위치 등 메타데이터 제거)
 * 6) 무작위 파일 이름  7) 공개 홈페이지(user/public/uploads/...)에 저장
 *
 * 공개 홈페이지가 보여줘야 하므로 user 프로젝트의 public 폴더에 저장한다.
 */
class ImageUpload
{
    public const MAX_BYTES  = 5 * 1024 * 1024;
    private const MAX_PIXEL = 4000;
    private const WIDTH     = 800;
    private const QUALITY   = 85;
    private const TYPES     = ['image/jpeg', 'image/png', 'image/webp'];

    /** 공개 홈페이지 public 폴더 (admin 과 user 는 같은 kbc 폴더 아래에 있다) */
    private static function userPublic(): string
    {
        return rtrim(ROOTPATH, '/') . '/../user/public/';
    }

    /**
     * 저장 후 DB 에 넣을 경로를 돌려준다. 예) uploads/instructors/3f9a...c1.jpg
     *
     * @throws RuntimeException 사용자에게 보여줄 안내 문구
     */
    public static function save(UploadedFile $file, string $folder): string
    {
        if (! $file->isValid()) {
            throw new RuntimeException(in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                ? '사진은 5MB 이하만 올릴 수 있습니다.'
                : '사진을 올리지 못했습니다. 다시 시도해 주세요.');
        }
        if ($file->getSize() > self::MAX_BYTES) {
            throw new RuntimeException('사진은 5MB 이하만 올릴 수 있습니다.');
        }

        // 확장자가 아니라 파일 내용으로 형식 확인
        $path = $file->getTempName();
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        $info = @getimagesize($path);

        if (! in_array($mime, self::TYPES, true) || $info === false || $info['mime'] !== $mime) {
            throw new RuntimeException('JPG, PNG, WEBP 사진만 올릴 수 있습니다.');
        }
        if ($info[0] > self::MAX_PIXEL || $info[1] > self::MAX_PIXEL) {
            throw new RuntimeException('사진이 너무 큽니다. 가로·세로 ' . self::MAX_PIXEL . 'px 이하로 줄여 주세요.');
        }

        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
        };
        if ($image === false) {
            throw new RuntimeException('사진 파일이 손상되었습니다. 다른 사진을 올려 주세요.');
        }

        if ($mime === 'image/jpeg') {
            $image = self::fixOrientation($image, $path);
        }

        $output = self::resize($image);

        $dir = self::userPublic() . 'uploads/' . $folder . '/';
        if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
            throw new RuntimeException('사진 저장 폴더를 만들 수 없습니다.');
        }

        $name = bin2hex(random_bytes(12)) . '.jpg';
        if (! imagejpeg($output, $dir . $name, self::QUALITY)) {
            throw new RuntimeException('사진을 저장하지 못했습니다.');
        }
        chmod($dir . $name, 0644);

        return 'uploads/' . $folder . '/' . $name;
    }

    /**
     * 저장한 사진 삭제 (이 클래스가 만든 파일 이름만 허용)
     */
    public static function delete(?string $relativePath): void
    {
        if ($relativePath === null || ! preg_match('#^uploads/[a-z]+/[a-f0-9]{24}\.jpg$#', $relativePath)) {
            return;
        }

        $file = self::userPublic() . $relativePath;
        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * 휴대폰 사진은 방향 정보(EXIF Orientation)만 있고 실제로는 누워 있는 경우가 많다.
     */
    private static function fixOrientation(\GdImage $image, string $path): \GdImage
    {
        $exif = function_exists('exif_read_data') ? @exif_read_data($path) : false;
        $angle = match ((int) ($exif['Orientation'] ?? 1)) {
            3       => 180,
            6       => -90,
            8       => 90,
            default => 0,
        };

        return $angle === 0 ? $image : imagerotate($image, $angle, 0);
    }

    /**
     * 가로 800px 이하로 줄이고, 투명 배경은 흰색으로 채운다.
     */
    private static function resize(\GdImage $image): \GdImage
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $scale = min(1, self::WIDTH / $w);
        $nw = (int) round($w * $scale);
        $nh = (int) round($h * $scale);

        $canvas = imagecreatetruecolor($nw, $nh);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $nw, $nh, $w, $h);

        return $canvas;
    }
}
