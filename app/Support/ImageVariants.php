<?php
// app/Support/ImageVariants.php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;

class ImageVariants
{
    protected ImageManager $im;

    public function __construct()
    {
        // GD por padrão (rápido e presente). Se tiver Imagick, pode trocar:
        // $this->im = new ImageManager(new \Intervention\Image\Drivers\Imagick\Driver());
        $this->im = new ImageManager(new GdDriver());
    }

    /**
     * Gera variants (webp + jpeg) e retorna o caminho "base" salvo no banco.
     * Ex.: base = posts/123/cover -> gerará:
     *  - posts/123/cover-sm.webp, cover-sm.jpg, ... md, lg, xl
     */
    public function generate(UploadedFile $file, string $baseDir, ?string $existingBase = null): string
    {
        $cfg   = config('images');
        $disk  = $cfg['disk'];
        $sizes = $cfg['variants'];
        $q     = $cfg['quality'];

        // Apaga variants antigas se existirem
        if ($existingBase) {
            $this->deleteVariants($existingBase);
        }

        // cria pasta se não existir
        Storage::disk($disk)->makeDirectory($baseDir);

        // base path (sem extensão) — ex: "posts/123/cover"
        $base = trim($baseDir, '/').'/cover';

        // abre a imagem
        $img = $this->im->read($file->getPathname());

        foreach ($sizes as $label => $width) {
            $resized = clone $img;
            $resized->scaleDown(width: $width);

            // WEBP
            $webpPath = $base.'-'.$label.'.webp';
            Storage::disk($disk)->put($webpPath, $resized->toWebp($q['webp']));

            // JPEG (fallback)
            $jpgPath = $base.'-'.$label.'.jpg';
            Storage::disk($disk)->put($jpgPath, $resized->toJpeg($q['jpeg']));
        }

        return $base; // salvamos só o "base" no DB
    }

    public function deleteVariants(string $base): void
    {
        $disk  = config('images.disk');
        $sizes = array_keys(config('images.variants'));
        foreach ($sizes as $label) {
            Storage::disk($disk)->delete([$base.'-'.$label.'.webp', $base.'-'.$label.'.jpg']);
        }
    }

    /**
     * Retorna srcset (webp e jpeg) para usar no <picture>.
     */
    public static function srcsets(string $base): array
    {
        $disk  = config('images.disk');
        $url   = fn($path) => Storage::disk($disk)->url($path);
        $outW  = config('images.variants');

        $webp = [];
        $jpeg = [];
        foreach ($outW as $label => $width) {
            $webp[] = $url($base.'-'.$label.'.webp').' '.$width.'w';
            $jpeg[] = $url($base.'-'.$label.'.jpg').' '.$width.'w';
        }

        // fallback (menor)
        $fallback = $url($base.'-sm.jpg');

        return [
            'webp'     => implode(', ', $webp),
            'jpeg'     => implode(', ', $jpeg),
            'fallback' => $fallback,
        ];
    }
}
