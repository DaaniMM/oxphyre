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

    private const WEBP_QUALITY = 92;
    private const MEMORY_SAFETY_BYTES = 33554432; // 32 MB de margen para evitar OOM en GD.
    private const PANORAMA_MAX_UPLOAD_SIZE = 15 * 1024 * 1024;

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
            return $this->result(false, 'No hemos podido leer esta imagen. Sube una foto en JPG, PNG o WebP.');
        }

        $dimensions = @getimagesize($tmpPath);
        if (!$dimensions || empty($dimensions[0]) || empty($dimensions[1])) {
            error_log("ImageProcessingService: imagen sin dimensiones legibles en {$direction}");
            return $this->result(false, 'No hemos podido leer esta imagen. Sube una foto en JPG, PNG o WebP.');
        }

        [$width, $height] = [(int) $dimensions[0], (int) $dimensions[1]];
        if (!$this->canProcessWithGd($width, $height)) {
            error_log("ImageProcessingService: imagen demasiado grande para GD en {$direction}: {$width}x{$height}");
            return $this->result(false, 'Esta imagen es demasiado grande para procesarla ahora. Prueba con una versión más ligera.');
        }

        $baseName = uniqid($filenamePrefix, true);
        $filename = $baseName . '.webp';
        $finalPath = $uploadDir . $filename;
        $midasTempPath = $uploadDir . $baseName . '_midas.jpg';

        if (!$this->convertToWebp($tmpPath, $mime, $finalPath, $midasTempPath)) {
            @unlink($finalPath);
            @unlink($midasTempPath);
            error_log("ImageProcessingService: conversión WebP fallida en {$direction}");
            return $this->result(false, 'No hemos podido procesar esta imagen ahora mismo. Inténtalo de nuevo en unos segundos.');
        }

        return $this->result(true, '', [
            'warning' => $this->qualityWarning($direction, $width, $height),
            'filename' => $filename,
            'finalPath' => $finalPath,
            'midasTempPath' => $midasTempPath,
            'originalName' => $file['name'] ?? '',
            'originalWidth' => $width,
            'originalHeight' => $height,
            'finalWidth' => $width,
            'finalHeight' => $height,
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

    private function convertToWebp(string $sourcePath, string $mime, string $webpPath, string $midasTempPath): bool
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

        $webpOk = imagewebp($image, $webpPath, self::WEBP_QUALITY);
        $jpegOk = imagejpeg($image, $midasTempPath, 92);
        imagedestroy($image);

        return $webpOk && $jpegOk && is_file($webpPath) && filesize($webpPath) > 0;
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
