<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageGeneratorService
{
    /**
     * Génère une vraie image PNG pour une catégorie
     */
    public static function generateCategoryImage(string $name, string $color): string
    {
        // Vérifier si GD est disponible
        if (!function_exists('imagecreatetruecolor')) {
            return self::generateCategoryImageSVG($name, $color);
        }

        $width = 800;
        $height = 400;

        // Créer une image
        $image = imagecreatetruecolor($width, $height);

        if (!$image) {
            return self::generateCategoryImageSVG($name, $color);
        }

        try {
            // Convertir hex en RGB
            [$r, $g, $b] = sscanf($color, "#%02x%02x%02x");
            $bgColor = imagecolorallocate($image, $r, $g, $b);
            $darkColor = imagecolorallocate($image, max(0, $r - 40), max(0, $g - 40), max(0, $b - 40));
            $lightColor = imagecolorallocate($image, min(255, $r + 60), min(255, $g + 60), min(255, $b + 60));
            $textColor = imagecolorallocate($image, 255, 255, 255);

            // Remplir le fond
            imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

            // Ajouter des motifs géométriques
            // Carrés dégradés
            for ($i = 0; $i < 5; $i++) {
                $x = $i * ($width / 5);
                $y = 0;
                $size = 100;
                $opacity = 20 - ($i * 3);
                imagefilledrectangle($image, $x, $y, $x + $size, $y + $size, $lightColor);
            }

            // Cercles
            imagefilledarc($image, 200, 200, 200, 200, 0, 360, $lightColor, IMG_ARC_PIE);
            imagefilledarc($image, $width - 150, $height - 150, 150, 150, 0, 360, $darkColor, IMG_ARC_PIE);

            // Lignes diagonales
            for ($i = 0; $i < 10; $i++) {
                imageline($image, $i * 80, 0, $i * 80 + 400, $height, $lightColor);
            }

            // Texte du nom
            $fontSize = 5;
            $textLen = strlen($name);
            $x = ($width - ($textLen * 8)) / 2;
            $y = ($height - 8) / 2;
            imagestring($image, $fontSize, $x, $y, $name, $textColor);

            // Sauvegarder
            $filename = 'categories/category_' . str()->slug($name) . '.png';
            $path = storage_path('app/public/' . $filename);
            @mkdir(dirname($path), 0755, true);
            imagepng($image, $path, 9);
            imagedestroy($image);

            return $filename;
        } catch (\Exception $e) {
            imagedestroy($image);
            return self::generateCategoryImageSVG($name, $color);
        }
    }

    /**
     * Génère une vraie image PNG pour un article
     */
    public static function generateArticleImage(string $title, string $category, int $index): string
    {
        // Vérifier si GD est disponible
        if (!function_exists('imagecreatetruecolor')) {
            return self::generateArticleImageSVG($title, $category, $index);
        }

        $width = 1200;
        $height = 630;

        // Créer une image
        $image = imagecreatetruecolor($width, $height);

        if (!$image) {
            return self::generateArticleImageSVG($title, $category, $index);
        }

        try {
            $colors = [
                ['#667EEA', '#764BA2', '#3d5a80'],
                ['#F093FB', '#F5576C', '#d81445'],
                ['#4FACFE', '#00F2FE', '#0066cc'],
                ['#43E97B', '#38F9D7', '#00aa55'],
                ['#FA709A', '#FEE140', '#cc5500'],
                ['#30CFD0', '#330867', '#001a4d'],
                ['#FF2D20', '#FFA500', '#cc1100'],
                ['#42B883', '#35495E', '#00aa55'],
                ['#00DC82', '#2E5090', '#008844'],
                ['#6366F1', '#EC4899', '#6600ff'],
                ['#F59E0B', '#EF4444', '#cc3300'],
                ['#EF4444', '#F97316', '#cc0000'],
            ];

            $colorPair = $colors[$index % count($colors)];
            [$color1, $color2, $color3] = $colorPair;

            [$r1, $g1, $b1] = sscanf($color1, "#%02x%02x%02x");
            [$r2, $g2, $b2] = sscanf($color2, "#%02x%02x%02x");
            [$r3, $g3, $b3] = sscanf($color3, "#%02x%02x%02x");

            $bgColor = imagecolorallocate($image, $r1, $g1, $b1);
            $accentColor = imagecolorallocate($image, $r2, $g2, $b2);
            $darkColor = imagecolorallocate($image, $r3, $g3, $b3);
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $textDark = imagecolorallocate($image, 50, 50, 50);

            // Dégradé vertical
            for ($y = 0; $y < $height; $y++) {
                $ratio = $y / $height;
                $r = (int)($r1 + ($r2 - $r1) * $ratio);
                $g = (int)($g1 + ($g2 - $g1) * $ratio);
                $b = (int)($b1 + ($b2 - $b1) * $ratio);
                $color = imagecolorallocate($image, $r, $g, $b);
                imageline($image, 0, $y, $width, $y, $color);
            }

            // Ajouter des formes géométriques
            for ($i = 0; $i < 5; $i++) {
                $x = rand(0, $width);
                $y = rand(0, $height);
                $size = rand(100, 300);
                imagefilledarc($image, $x, $y, $size, $size, 0, 360, $accentColor, IMG_ARC_PIE);
            }

            // Overlay semi-transparent (rectangle noir 20% opacity)
            $overlay = imagecolorallocate($image, 0, 0, 0);
            imagealphablending($image, true);
            imagefilledrectangle($image, 0, 0, $width, $height, $overlay);
            imagealphablending($image, false);

            // Catégorie (avec fond)
            imagefilledrectangle($image, 60, 80, 350, 130, $darkColor);
            imagestring($image, 5, 80, 95, strtoupper($category), $textColor);

            // Titre (avec fond semi-opaque)
            $titleShort = substr($title, 0, 60);
            imagefilledrectangle($image, 60, 200, 1100, 400, $darkColor);

            // Écrire le titre sur plusieurs lignes
            $lines = str_split($titleShort, 40);
            $yPos = 230;
            foreach ($lines as $line) {
                imagestring($image, 5, 80, $yPos, $line, $textColor);
                $yPos += 40;
            }

            // Sauvegarder
            $filename = 'articles/article_' . $index . '_' . str()->slug(substr($title, 0, 30)) . '.png';
            $path = storage_path('app/public/' . $filename);
            @mkdir(dirname($path), 0755, true);
            imagepng($image, $path, 9);
            imagedestroy($image);

            return $filename;
        } catch (\Exception $e) {
            imagedestroy($image);
            return self::generateArticleImageSVG($title, $category, $index);
        }
    }

    /**
     * Fallback SVG pour catégorie
     */
    private static function generateCategoryImageSVG(string $name, string $color): string
    {
        $safeName = htmlspecialchars($name, ENT_QUOTES);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="400" viewBox="0 0 800 400">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$color};stop-opacity:1" />
      <stop offset="100%" style="stop-color:#111827;stop-opacity:0.95" />
    </linearGradient>
  </defs>
  <rect width="800" height="400" rx="24" fill="url(#grad)"/>
  <circle cx="690" cy="70" r="96" fill="white" opacity="0.08"/>
  <circle cx="110" cy="330" r="120" fill="white" opacity="0.06"/>
  <rect x="70" y="70" width="250" height="180" rx="18" fill="rgba(255,255,255,0.10)"/>
  <rect x="95" y="95" width="140" height="18" rx="9" fill="rgba(255,255,255,0.90)"/>
  <rect x="95" y="128" width="180" height="12" rx="6" fill="rgba(255,255,255,0.45)"/>
  <rect x="95" y="150" width="150" height="12" rx="6" fill="rgba(255,255,255,0.35)"/>
  <rect x="95" y="180" width="60" height="60" rx="12" fill="rgba(255,255,255,0.18)"/>
  <rect x="165" y="180" width="60" height="60" rx="12" fill="rgba(255,255,255,0.26)"/>
  <rect x="235" y="180" width="60" height="60" rx="12" fill="rgba(255,255,255,0.14)"/>
  <text x="400" y="305" font-family="Arial, sans-serif" font-size="54" font-weight="700" fill="white" text-anchor="middle">{$safeName}</text>
  <text x="400" y="340" font-family="Arial, sans-serif" font-size="18" fill="rgba(255,255,255,0.82)" text-anchor="middle">Catégorie du blog</text>
</svg>
SVG;

        $filename = 'categories/category_' . str()->slug($name) . '.svg';
        Storage::disk('public')->put($filename, $svg);
        return $filename;
    }

    /**
     * Fallback SVG pour article
     */
    private static function generateArticleImageSVG(string $title, string $category, int $index): string
    {
        $colors = [
            ['#667EEA', '#764BA2'],
            ['#F093FB', '#F5576C'],
            ['#4FACFE', '#00F2FE'],
            ['#43E97B', '#38F9D7'],
            ['#FA709A', '#FEE140'],
            ['#30CFD0', '#330867'],
            ['#FF2D20', '#FFA500'],
            ['#42B883', '#35495E'],
            ['#00DC82', '#2E5090'],
            ['#6366F1', '#EC4899'],
            ['#F59E0B', '#EF4444'],
            ['#EF4444', '#F97316'],
        ];

        $colorPair = $colors[$index % count($colors)];
        [$color1, $color2] = $colorPair;
        $titleShort = htmlspecialchars(substr($title, 0, 72), ENT_QUOTES);
        $categorySafe = htmlspecialchars($category, ENT_QUOTES);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">
  <defs>
    <linearGradient id="gradient$index" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$color1};stop-opacity:1" />
      <stop offset="100%" style="stop-color:{$color2};stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="1200" height="630" rx="28" fill="url(#gradient$index)"/>
  <circle cx="1050" cy="80" r="140" fill="white" opacity="0.10"/>
  <circle cx="160" cy="520" r="120" fill="white" opacity="0.08"/>
  <rect x="70" y="70" width="460" height="280" rx="26" fill="rgba(255,255,255,0.10)"/>
  <rect x="95" y="95" width="160" height="18" rx="9" fill="rgba(255,255,255,0.85)"/>
  <rect x="95" y="128" width="260" height="12" rx="6" fill="rgba(255,255,255,0.35)"/>
  <rect x="95" y="150" width="220" height="12" rx="6" fill="rgba(255,255,255,0.28)"/>
  <rect x="95" y="195" width="190" height="110" rx="16" fill="rgba(255,255,255,0.15)"/>
  <rect x="300" y="195" width="205" height="48" rx="14" fill="rgba(255,255,255,0.18)"/>
  <rect x="300" y="257" width="205" height="48" rx="14" fill="rgba(255,255,255,0.12)"/>
  <rect x="70" y="400" width="220" height="52" rx="26" fill="rgba(17,24,39,0.35)"/>
  <text x="180" y="433" font-family="Arial, sans-serif" font-size="28" font-weight="700" fill="white" text-anchor="middle">{$categorySafe}</text>
  <text x="70" y="520" font-family="Arial, sans-serif" font-size="54" font-weight="700" fill="white">{$titleShort}</text>
  <text x="70" y="570" font-family="Arial, sans-serif" font-size="22" fill="rgba(255,255,255,0.82)">Article BlogModerne</text>
</svg>
SVG;

        $filename = 'articles/article_' . $index . '_' . str()->slug(substr($title, 0, 30)) . '.svg';
        Storage::disk('public')->put($filename, $svg);
        return $filename;
    }
}
