<?php
/**
 * KBC아카데미 공유 이미지·아이콘 생성
 *
 * 만드는 파일
 *   user/public/og-image.png          1200×630  카카오톡·페이스북 등 링크 미리보기 이미지
 *   user/public/apple-touch-icon.png  180×180   휴대폰 홈 화면 아이콘
 *   user/public/favicon.ico / .svg    16·32     브라우저 탭 아이콘 (빨간 점)
 *   admin/public/favicon.ico / .svg   16·32     관리자 탭 아이콘 (흰 점)
 *
 * 준비 (글꼴은 저장소에 넣지 않고 필요할 때 받는다. Pretendard, SIL OFL 1.1)
 *   T=$(mktemp -d) && cd $T && npm pack pretendard@1.3.9 && tar -xzf pretendard-1.3.9.tgz
 *   → 글꼴 폴더: $T/package/dist/public/static
 *
 * 실행
 *   php scripts/make-og-image.php <글꼴 폴더>              실제 위치에 덮어쓰기
 *   php scripts/make-og-image.php <글꼴 폴더> <출력 폴더>   출력 폴더에만 만들기 (미리 확인용)
 *
 * 바꾼 뒤: 카카오톡은 미리보기 이미지를 오래 저장해 두므로
 *          카카오 공유 디버거(developers.kakao.com/tool/debugger/sharing)에서 주소를 넣고 "캐시 초기화"를 한다.
 */

// ───────────── 여기만 바꾸면 됩니다 ─────────────
$TEXT = [
    'pill'     => '2026년 10월 시범 개강 목표',              // 상단 알약
    'brand'    => 'KBC아카데미',                             // 가장 큰 글자
    'line1'    => '방송 진행을 배우고,',                      // 첫째 줄 (흰색)
    'line2'    => '실습으로 완성합니다',                      // 둘째 줄 (강조색)
    'sub'      => '개인방송 · 커머스방송 · 협업형 라이브 실습', // 작은 설명
    'chat1'    => '실습 시작합니다',                          // 오른쪽 화면 속 채팅 1
    'chat2'    => '카메라 시선 좋아요',                       // 오른쪽 화면 속 채팅 2
];
$COLOR = [                       // [R, G, B]  (디자인 색상표 tokens.css 와 같은 값)
    'navy'   => [15, 29, 61],    // 배경 남색 --c-navy
    'live'   => [255, 61, 87],   // LIVE 빨강 --c-live
    'accent' => [159, 184, 255], // 둘째 줄 강조색
    'sub'    => [195, 205, 226], // 작은 설명 글자색
    'white'  => [255, 255, 255],
];
// ────────────────────────────────────────────────

if ($argc < 2 || ! is_dir($argv[1])) {
    fwrite(STDERR, "사용: php scripts/make-og-image.php <글꼴 폴더> [출력 폴더]\n");
    exit(1);
}
if (! function_exists('imagettftext')) {
    fwrite(STDERR, "PHP GD 확장(FreeType 포함)이 필요합니다. (예: apt install php8.3-gd)\n");
    exit(1);
}

$fontDir = rtrim($argv[1], '/');
$font    = [];
foreach (['medium' => 'Pretendard-Medium.otf', 'bold' => 'Pretendard-Bold.otf', 'extra' => 'Pretendard-ExtraBold.otf'] as $key => $file) {
    $font[$key] = "{$fontDir}/{$file}";
    if (! is_file($font[$key])) {
        fwrite(STDERR, "글꼴 파일이 없습니다: {$font[$key]}\n");
        exit(1);
    }
}

$root = dirname(__DIR__);
if (isset($argv[2])) {
    $userDir  = rtrim($argv[2], '/') . '/user';
    $adminDir = rtrim($argv[2], '/') . '/admin';
} else {
    $userDir  = "{$root}/user/public";
    $adminDir = "{$root}/admin/public";
}
foreach ([$userDir, $adminDir] as $dir) {
    if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
        fwrite(STDERR, "폴더를 만들 수 없습니다: {$dir}\n");
        exit(1);
    }
}

/** 색 만들기 */
function color($im, array $rgb): int
{
    return imagecolorallocate($im, ...$rgb);
}

/** 둥근 사각형 채우기 */
function roundRect($im, int $x1, int $y1, int $x2, int $y2, int $r, int $c): void
{
    imagefilledrectangle($im, $x1 + $r, $y1, $x2 - $r, $y2, $c);
    imagefilledrectangle($im, $x1, $y1 + $r, $x2, $y2 - $r, $c);
    foreach ([[$x1 + $r, $y1 + $r], [$x2 - $r, $y1 + $r], [$x1 + $r, $y2 - $r], [$x2 - $r, $y2 - $r]] as [$x, $y]) {
        imagefilledellipse($im, $x, $y, $r * 2, $r * 2, $c);
    }
}

// ── 1) 공유 이미지 1200×630 ──
$im = imagecreatetruecolor(1200, 630);
imagefill($im, 0, 0, color($im, $COLOR['navy']));

// 오른쪽 위 은은한 빛
for ($r = 460; $r > 0; $r -= 6) {
    $t = 1 - $r / 460;
    imagefilledellipse($im, 1000, 150, $r * 2, $r * 2, imagecolorallocatealpha($im, 91, 130, 255, (int) (126 - $t * 16)));
}

$white  = color($im, $COLOR['white']);
$live   = color($im, $COLOR['live']);
$accent = color($im, $COLOR['accent']);
$sub    = color($im, $COLOR['sub']);

// 왼쪽 글자
roundRect($im, 80, 88, 448, 138, 25, imagecolorallocate($im, 39, 53, 86));
imagefilledellipse($im, 108, 113, 16, 16, $live);
imagettftext($im, 21, 0, 128, 122, $white, $font['medium'], $TEXT['pill']);
imagettftext($im, 86, 0, 76, 285, $white, $font['extra'], $TEXT['brand']);
imagettftext($im, 42, 0, 80, 375, $white, $font['bold'], $TEXT['line1']);
imagettftext($im, 42, 0, 80, 435, $accent, $font['bold'], $TEXT['line2']);
imagettftext($im, 24, 0, 82, 530, $sub, $font['medium'], $TEXT['sub']);

// 오른쪽 세로 방송 화면 (사진 대신 도형으로 그림)
roundRect($im, 900, 110, 1110, 560, 34, imagecolorallocate($im, 6, 12, 27));
roundRect($im, 912, 122, 1098, 548, 26, imagecolorallocate($im, 42, 63, 122));
roundRect($im, 930, 142, 1004, 172, 8, $live);
imagefilledellipse($im, 946, 157, 8, 8, $white);
imagettftext($im, 15, 0, 958, 164, $white, $font['bold'], 'LIVE');
imagefilledellipse($im, 1005, 300, 110, 110, imagecolorallocate($im, 70, 89, 142));
roundRect($im, 988, 262, 1022, 322, 17, $white);   // 마이크
imagesetthickness($im, 5);
imagearc($im, 1005, 300, 70, 70, 20, 160, $white);
imageline($im, 1005, 335, 1005, 352, $white);
foreach ([[430, $TEXT['chat1']], [470, $TEXT['chat2']]] as [$y, $t]) {
    roundRect($im, 928, $y - 24, 1080, $y + 10, 14, imagecolorallocate($im, 24, 36, 72));
    imagettftext($im, 14, 0, 942, $y, $white, $font['medium'], $t);
}

imagepng($im, "{$userDir}/og-image.png", 9);
echo "만듦: {$userDir}/og-image.png\n";

// ── 2) 아이콘 (남색 둥근 사각형 + 가운데 점) ──
/** 4배 크기로 그린 뒤 줄여서 테두리를 부드럽게 한다 */
function icon(int $size, array $navy, array $dot)
{
    $s  = $size * 4;
    $im = imagecreatetruecolor($s, $s);
    imagesavealpha($im, true);
    imagealphablending($im, false);
    imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));
    imagealphablending($im, true);

    $bg = color($im, $navy);
    $r  = (int) ($s * 0.22);
    imagefilledrectangle($im, $r, 0, $s - $r, $s, $bg);
    imagefilledrectangle($im, 0, $r, $s, $s - $r, $bg);
    foreach ([[$r, $r], [$s - $r, $r], [$r, $s - $r], [$s - $r, $s - $r]] as [$x, $y]) {
        imagefilledellipse($im, $x, $y, $r * 2, $r * 2, $bg);
    }
    imagefilledellipse($im, (int) ($s / 2), (int) ($s / 2), (int) ($s * 0.44), (int) ($s * 0.44), color($im, $dot));

    $out = imagecreatetruecolor($size, $size);
    imagesavealpha($out, true);
    imagealphablending($out, false);
    imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
    imagecopyresampled($out, $im, 0, 0, 0, 0, $size, $size, $s, $s);

    return $out;
}

function pngBytes($im): string
{
    ob_start();
    imagepng($im);

    return (string) ob_get_clean();
}

/** 16·32 PNG 두 장을 담은 .ico 파일 */
function writeIco(string $path, array $navy, array $dot): void
{
    $images = [16 => pngBytes(icon(16, $navy, $dot)), 32 => pngBytes(icon(32, $navy, $dot))];
    $head   = pack('vvv', 0, 1, count($images));
    $offset = 6 + 16 * count($images);
    $dir    = '';
    foreach ($images as $size => $data) {
        $dir    .= pack('CCCCvvVV', $size, $size, 0, 0, 1, 32, strlen($data), $offset);
        $offset += strlen($data);
    }
    file_put_contents($path, $head . $dir . implode('', $images));
    echo "만듦: {$path}\n";
}

function writeSvg(string $path, array $navy, array $dot): void
{
    $hex = static fn (array $c) => sprintf('#%02x%02x%02x', ...$c);
    file_put_contents($path, '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
        . '<rect width="64" height="64" rx="14" fill="' . $hex($navy) . '"/>'
        . '<circle cx="32" cy="32" r="14" fill="' . $hex($dot) . '"/></svg>' . "\n");
    echo "만듦: {$path}\n";
}

imagepng(icon(180, $COLOR['navy'], $COLOR['live']), "{$userDir}/apple-touch-icon.png");
echo "만듦: {$userDir}/apple-touch-icon.png\n";
writeIco("{$userDir}/favicon.ico", $COLOR['navy'], $COLOR['live']);
writeSvg("{$userDir}/favicon.svg", $COLOR['navy'], $COLOR['live']);
writeIco("{$adminDir}/favicon.ico", $COLOR['navy'], $COLOR['white']);
writeSvg("{$adminDir}/favicon.svg", $COLOR['navy'], $COLOR['white']);

echo "완료. 공유 이미지를 바꿨다면 카카오 공유 디버거에서 캐시를 초기화하세요.\n";
