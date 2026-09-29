<?php

namespace App\Libraries;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

/**
 * 관리자 2단계 인증 (OTP 앱: Google Authenticator 등)
 *
 * - 비밀키는 DB 에 암호화해서 저장한다 (.env encryption.key)
 * - QR 코드는 서버에서 직접 SVG 로 만든다 (외부 서비스로 비밀키를 보내지 않음)
 * - 복구 코드는 비밀번호처럼 해시로만 저장하고, 한 번 쓰면 삭제한다
 */
class TwoFactor
{
    private const ISSUER         = 'KBC아카데미 관리자';
    private const RECOVERY_COUNT = 8;

    private Google2FA $otp;

    public function __construct()
    {
        $this->otp = new Google2FA();
    }

    public function newSecret(): string
    {
        return $this->otp->generateSecretKey(32);
    }

    /**
     * 6자리 확인. 앞뒤 30초까지 허용(휴대폰 시계 오차 대비)
     */
    public function verify(string $secret, string $code): bool
    {
        $code = preg_replace('/\D/', '', $code);

        return strlen($code) === 6 && $this->otp->verifyKey($secret, $code, 1) !== false;
    }

    /**
     * 앱에서 찍을 QR 코드 (SVG)
     */
    public function qrSvg(string $username, string $secret): string
    {
        $url    = $this->otp->getQRCodeUrl(self::ISSUER, $username, $secret);
        $writer = new Writer(new ImageRenderer(new RendererStyle(220, 1), new SvgImageBackEnd()));

        // HTML 안에 넣으므로 XML 선언은 뺀다
        return preg_replace('/^<\?xml[^>]*>\s*/', '', $writer->writeString($url));
    }

    public function encrypt(string $secret): string
    {
        return base64_encode(service('encrypter')->encrypt($secret));
    }

    public function decrypt(string $stored): string
    {
        return service('encrypter')->decrypt(base64_decode($stored, true));
    }

    /**
     * 복구 코드 새로 만들기
     *
     * @return array{0: list<string>, 1: string} [보여줄 코드, DB 에 저장할 JSON(해시)]
     */
    public function newRecoveryCodes(): array
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $codes = [];

        for ($i = 0; $i < self::RECOVERY_COUNT; $i++) {
            $code = '';
            for ($j = 0; $j < 8; $j++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $codes[] = substr($code, 0, 4) . '-' . substr($code, 4);
        }

        $hashes = array_map(static fn (string $c): string => password_hash($c, PASSWORD_DEFAULT), $codes);

        return [$codes, json_encode($hashes)];
    }

    /**
     * 복구 코드 확인. 맞으면 해당 코드를 뺀 새 JSON 을, 틀리면 null 을 돌려준다.
     */
    public function useRecoveryCode(?string $storedJson, string $input): ?string
    {
        $hashes = json_decode((string) $storedJson, true) ?: [];
        $input  = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $input));

        if (strlen($input) !== 8) {
            return null;
        }
        $input = substr($input, 0, 4) . '-' . substr($input, 4);

        foreach ($hashes as $i => $hash) {
            if (password_verify($input, $hash)) {
                unset($hashes[$i]);

                return json_encode(array_values($hashes));
            }
        }

        return null;
    }

    public static function remainingRecoveryCodes(?string $storedJson): int
    {
        return count(json_decode((string) $storedJson, true) ?: []);
    }
}
