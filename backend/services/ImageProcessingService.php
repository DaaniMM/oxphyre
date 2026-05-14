<?php

/**
 * ImageProcessingService — pipeline local de imágenes visibles del tour.
 *
 * Responsabilidad: validar, convertir a WebP, generar temporal para MiDaS,
 * detectar baja calidad y devolver metadata. No escribe en BD ni llama a MiDaS.
 */
class ImageProcessingService
{
    public const LOW_QUALITY_RECOMMENDATION = 'Recomendación de Oxphyre: evita pasar las fotos por WhatsApp, Instagram u otras apps antes de subirlas, porque pueden reducir la calidad.';

    private const WEBP_QUALITY_NORMAL = 92;
    private const WEBP_QUALITY_PANORAMA = 96;
    private const MIDAS_JPEG_QUALITY = 92;
    private const MEMORY_SAFETY_BYTES = 33554432; // 32 MB de margen para evitar OOM en GD.
    private const PANORAMA_MAX_UPLOAD_SIZE = 15 * 1024 * 1024;
    private const PANORAMA_MAX_FINAL_WIDTH = 8192;
    private const HEIF_MIME_TYPES = [
        'image/heic',
        'image/heif',
        'image/heic-sequence',
        'image/heif-sequence',
    ];

    public function processUpload(array $file, string $uploadDir, string $direction, string $filenamePrefix): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            error_log("ImageProcessingService: error de upload {$file['error']} en {$direction}");
            return $this->result(false, 'No hemos podido recibir esta imagen. Inténtalo otra vez.');
        }

        $sizeLimit = $this->uploadSizeLimitForDirection($direction);
        $fileSize = (int) ($file['size'] ?? 0);
        if ($fileSize > $sizeLimit) {
            error_log("ImageProcessingService: tamaño excedido en {$direction}: {$fileSize} bytes; límite aplicado {$sizeLimit} bytes");
            return $this->result(false, 'Esta imagen es demasiado grande para subirla ahora. Prueba con una versión más ligera.');
        }

        $tmpPath = $file['tmp_name'] ?? '';
        $mime = $this->detectMime($tmpPath);
        if (!in_array($mime, ALLOWED_MIME_TYPES, true)) {
            error_log("ImageProcessingService: MIME no permitido en {$direction}: {$mime}");
            return $this->result(false, 'No hemos podido leer esta imagen. Sube una foto en JPG, PNG, WebP o HEIC/HEIF (foto original de iPhone).');
        }

        $dimensions = $this->imageDimensions($tmpPath, $mime);
        if ($dimensions === null) {
            error_log("ImageProcessingService: imagen sin dimensiones legibles en {$direction}");
            return $this->result(false, $this->messageForUnreadableImage($mime));
        }

        [$width, $height] = $dimensions;
        $canUseGd = $this->canProcessWithGd($width, $height);
        if ($direction !== '360' && !$canUseGd && !$this->isHeifMime($mime)) {
            error_log("ImageProcessingService: imagen demasiado grande para GD en {$direction}: {$width}x{$height}");
            return $this->result(false, 'Esta imagen es demasiado grande para procesarla ahora. Prueba con una versión más ligera.');
        }

        $baseName = uniqid($filenamePrefix, true);
        $filename = $baseName . '.webp';
        $finalPath = $uploadDir . $filename;
        $midasTempPath = $uploadDir . $baseName . '_midas.jpg';

        if (!$this->convertImage($tmpPath, $mime, $finalPath, $midasTempPath, $direction, $width, $height, $canUseGd)) {
            @unlink($finalPath);
            @unlink($midasTempPath);
            error_log("ImageProcessingService: conversión WebP fallida en {$direction}");
            return $this->result(false, $this->messageForConversionFailure($mime));
        }

        $finalDimensions = @getimagesize($finalPath);
        $finalWidth = $finalDimensions && !empty($finalDimensions[0]) ? (int) $finalDimensions[0] : $width;
        $finalHeight = $finalDimensions && !empty($finalDimensions[1]) ? (int) $finalDimensions[1] : $height;

        return $this->result(true, '', [
            'warning' => $this->qualityWarning($direction, $width, $height),
            'filename' => $filename,
            'finalPath' => $finalPath,
            'midasTempPath' => $midasTempPath,
            'originalName' => $file['name'] ?? '',
            'originalWidth' => $width,
            'originalHeight' => $height,
            'finalWidth' => $finalWidth,
            'finalHeight' => $finalHeight,
            'finalSize' => is_file($finalPath) ? filesize($finalPath) : 0,
        ]);
    }

    private function detectMime(string $path): string
    {
        if ($path === '' || !is_file($path)) {
            return '';
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($path) ?: '';
    }

    private function uploadSizeLimitForDirection(string $direction): int
    {
        return $direction === '360' ? self::PANORAMA_MAX_UPLOAD_SIZE : MAX_UPLOAD_SIZE;
    }

    private function imageDimensions(string $path, string $mime): ?array
    {
        $dimensions = @getimagesize($path);
        if ($dimensions && !empty($dimensions[0]) && !empty($dimensions[1])) {
            return [(int) $dimensions[0], (int) $dimensions[1]];
        }

        if (!$this->isHeifMime($mime)) {
            return null;
        }

        return $this->vipsDimensions($path);
    }

    private function convertImage(
        string $sourcePath,
        string $mime,
        string $webpPath,
        string $midasTempPath,
        string $direction,
        int $width,
        int $height,
        bool $canUseGd
    ): bool {
        if ($this->isHeifMime($mime)) {
            $vipsPath = $this->vipsPath();
            if ($vipsPath === '') {
                error_log("ImageProcessingService: libvips no disponible para HEIC/HEIF {$width}x{$height}");
                return false;
            }

            return $this->convertWithVips($vipsPath, $sourcePath, $webpPath, $midasTempPath, $direction, $width, $height);
        }

        if ($direction === '360' && (!$canUseGd || $width > self::PANORAMA_MAX_FINAL_WIDTH)) {
            $vipsPath = $this->vipsPath();
            if ($vipsPath === '') {
                error_log("ImageProcessingService: libvips no disponible para panoramica grande {$width}x{$height}");
                return false;
            }

            return $this->convertWithVips($vipsPath, $sourcePath, $webpPath, $midasTempPath, $direction, $width, $height);
        }

        return $this->convertToWebp($sourcePath, $mime, $webpPath, $midasTempPath, $this->webpQualityForDirection($direction));
    }

    private function convertToWebp(string $sourcePath, string $mime, string $webpPath, string $midasTempPath, int $webpQuality): bool
    {
        if (!function_exists('imagewebp') || !function_exists('imagejpeg')) {
            error_log('ImageProcessingService: GD/WebP no disponible en el servidor');
            return false;
        }

        $image = match ($mime) {
            'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($sourcePath) : false,
            'image/png'  => function_exists('imagecreatefrompng') ? @imagecreatefrompng($sourcePath) : false,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            default      => false,
        };

        if (!$image) {
            return false;
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $webpOk = imagewebp($image, $webpPath, $webpQuality);
        $jpegOk = imagejpeg($image, $midasTempPath, self::MIDAS_JPEG_QUALITY);
        imagedestroy($image);

        return $webpOk && $jpegOk && is_file($webpPath) && filesize($webpPath) > 0;
    }

    private function webpQualityForDirection(string $direction): int
    {
        return $direction === '360' ? self::WEBP_QUALITY_PANORAMA : self::WEBP_QUALITY_NORMAL;
    }

    private function convertWithVips(
        string $vipsPath,
        string $sourcePath,
        string $webpPath,
        string $midasTempPath,
        string $direction,
        int $width,
        int $height
    ): bool {
        $shouldResize = $direction === '360' && $width > self::PANORAMA_MAX_FINAL_WIDTH;
        $webpTarget = $webpPath . '[Q=' . $this->webpQualityForDirection($direction) . ',strip]';
        $jpegTarget = $midasTempPath . '[Q=' . self::MIDAS_JPEG_QUALITY . ',strip]';

        if ($shouldResize) {
            error_log("ImageProcessingService: libvips redimensiona 360 {$width}x{$height} a ancho " . self::PANORAMA_MAX_FINAL_WIDTH . "; final {$webpPath}; midas {$midasTempPath}");
            $webpCommand = $this->vipsCommand($vipsPath, 'thumbnail', [$sourcePath, $webpTarget, (string) self::PANORAMA_MAX_FINAL_WIDTH]);
            $jpegCommand = $this->vipsCommand($vipsPath, 'thumbnail', [$sourcePath, $jpegTarget, (string) self::PANORAMA_MAX_FINAL_WIDTH]);
        } else {
            $webpCommand = $this->vipsCommand($vipsPath, 'copy', [$sourcePath, $webpTarget]);
            $jpegCommand = $this->vipsCommand($vipsPath, 'copy', [$sourcePath, $jpegTarget]);
        }

        return $this->runCommand($webpCommand, "WebP {$direction} libvips")
            && $this->runCommand($jpegCommand, "JPG temporal MiDaS {$direction} libvips")
            && is_file($webpPath)
            && filesize($webpPath) > 0
            && is_file($midasTempPath)
            && filesize($midasTempPath) > 0;
    }

    private function vipsDimensions(string $path): ?array
    {
        $vipsHeaderPath = $this->vipsHeaderPath();
        if ($vipsHeaderPath === '') {
            error_log('ImageProcessingService: vipsheader no disponible para leer dimensiones HEIC/HEIF');
            return null;
        }

        $width = $this->vipsHeaderField($vipsHeaderPath, $path, 'width');
        $height = $this->vipsHeaderField($vipsHeaderPath, $path, 'height');

        if ($width <= 0 || $height <= 0) {
            error_log("ImageProcessingService: vipsheader no pudo leer dimensiones de {$path}");
            return null;
        }

        return [$width, $height];
    }

    private function vipsPath(): string
    {
        if (is_file('/usr/bin/vips') && is_executable('/usr/bin/vips')) {
            return '/usr/bin/vips';
        }

        if (!function_exists('shell_exec')) {
            return '';
        }

        $path = trim((string) @shell_exec('command -v vips 2>/dev/null'));
        return $path !== '' && is_executable($path) ? $path : '';
    }

    private function vipsHeaderPath(): string
    {
        if (is_file('/usr/bin/vipsheader') && is_executable('/usr/bin/vipsheader')) {
            return '/usr/bin/vipsheader';
        }

        if (!function_exists('shell_exec')) {
            return '';
        }

        $path = trim((string) @shell_exec('command -v vipsheader 2>/dev/null'));
        return $path !== '' && is_executable($path) ? $path : '';
    }

    private function vipsHeaderField(string $vipsHeaderPath, string $path, string $field): int
    {
        $command = implode(' ', [
            escapeshellarg($vipsHeaderPath),
            '-f',
            escapeshellarg($field),
            escapeshellarg($path),
        ]);

        $output = $this->commandOutput($command, "vipsheader {$field}");
        return $output === null ? 0 : (int) trim($output);
    }

    private function vipsCommand(string $vipsPath, string $operation, array $arguments): string
    {
        $escaped = [escapeshellarg($vipsPath), escapeshellarg($operation)];
        foreach ($arguments as $argument) {
            $escaped[] = escapeshellarg($argument);
        }

        return implode(' ', $escaped);
    }

    private function runCommand(string $command, string $context): bool
    {
        if (!function_exists('exec')) {
            error_log("ImageProcessingService: exec no disponible para {$context}");
            return false;
        }

        $output = [];
        $exitCode = 0;
        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            error_log("ImageProcessingService: {$context} fallo con codigo {$exitCode}: " . implode(' | ', $output));
            return false;
        }

        return true;
    }

    private function commandOutput(string $command, string $context): ?string
    {
        if (!function_exists('exec')) {
            error_log("ImageProcessingService: exec no disponible para {$context}");
            return null;
        }

        $output = [];
        $exitCode = 0;
        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            error_log("ImageProcessingService: {$context} fallo con codigo {$exitCode}: " . implode(' | ', $output));
            return null;
        }

        return implode("\n", $output);
    }

    private function isHeifMime(string $mime): bool
    {
        return in_array($mime, self::HEIF_MIME_TYPES, true);
    }

    private function messageForUnreadableImage(string $mime): string
    {
        if ($this->isHeifMime($mime)) {
            return 'No hemos podido procesar esta foto del iPhone ahora mismo. Inténtalo de nuevo o súbela como JPG.';
        }

        return 'No hemos podido leer esta imagen. Sube una foto en JPG, PNG, WebP o HEIC/HEIF (foto original de iPhone).';
    }

    private function messageForConversionFailure(string $mime): string
    {
        if ($this->isHeifMime($mime)) {
            return 'No hemos podido procesar esta foto del iPhone ahora mismo. Inténtalo de nuevo o súbela como JPG.';
        }

        return 'No hemos podido procesar esta imagen ahora mismo. Inténtalo de nuevo en unos segundos.';
    }

    private function qualityWarning(string $direction, int $width, int $height): string
    {
        $isPanorama = $direction === '360';
        $aspect = $height > 0 ? $width / $height : 0;

        if ($isPanorama && $height < 700) {
            if ($aspect > 3.5) {
                return 'La panorámica se ha subido, pero parece comprimida. Para que el tour se vea mejor, sube la foto original desde la galería del móvil.';
            }
            return 'La panorámica se ha subido, pero parece tener poca resolución. Para que el tour se vea mejor, sube la foto original desde la galería del móvil.';
        }

        if (!$isPanorama && ($width < 1000 || $height < 700)) {
            return 'La imagen se ha subido, pero parece tener poca resolución. Para mejor calidad, sube el archivo original desde la galería del móvil.';
        }

        return '';
    }

    private function canProcessWithGd(int $width, int $height): bool
    {
        $memoryLimit = $this->parseBytes(ini_get('memory_limit'));
        if ($memoryLimit <= 0) {
            return true;
        }

        $estimatedBytes = ($width * $height * 6) + self::MEMORY_SAFETY_BYTES;
        return $estimatedBytes < ($memoryLimit * 0.75);
    }

    private function parseBytes(string|false $value): int
    {
        if ($value === false || $value === '') {
            return 0;
        }

        $value = trim($value);
        if ($value === '-1') {
            return -1;
        }

        $unit = strtolower($value[strlen($value) - 1]);
        $bytes = (int) $value;

        return match ($unit) {
            'g' => $bytes * 1024 * 1024 * 1024,
            'm' => $bytes * 1024 * 1024,
            'k' => $bytes * 1024,
            default => $bytes,
        };
    }

    private function result(bool $success, string $message = '', array $data = []): array
    {
        return array_merge([
            'success' => $success,
            'message' => $message,
            'warning' => '',
            'filename' => '',
            'finalPath' => '',
            'midasTempPath' => '',
            'originalName' => '',
            'originalWidth' => null,
            'originalHeight' => null,
            'finalWidth' => null,
            'finalHeight' => null,
            'finalSize' => null,
        ], $data);
    }
}
