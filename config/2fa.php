<?php
require_once '../vendor/autoload.php';

use RobThree\Auth\TwoFactorAuth;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class BaconQrProvider implements \RobThree\Auth\Providers\Qr\IQRCodeProvider
{
    public function getQRCodeImage(string $qrtext, int $size): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        return $writer->writeString($qrtext);
    }

    public function getMimeType(): string
    {
        return 'image/svg+xml';
    }
}

$tfa = new TwoFactorAuth(new BaconQrProvider());