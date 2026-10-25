<?php
/**
 * app/Services/ImageUploader.php
 */

declare(strict_types=1);

namespace App\Services;

use Exception;

class ImageUploader
{
    private string $uploadDir;
    private array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    private int $maxFileSize = 2097152; // 2 MB

    public function __construct()
    {
        $this->uploadDir = PUBLIC_PATH . '/assets/img/products/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload(array $file): string
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception('Parámetros de archivo no válidos.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return 'default_product.png';
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('El archivo supera el tamaño límite permitido de 2 MB.');
            default:
                throw new Exception('Error al subir el archivo al servidor.');
        }

        if ($file['size'] > $this->maxFileSize) {
            throw new Exception('El archivo supera el tamaño máximo permitido de 2 MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $this->allowedMimeTypes, true)) {
            throw new Exception('Tipo de archivo no permitido. Solo se aceptan imágenes JPG, PNG y WebP.');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions, true)) {
            throw new Exception('Extensión de archivo no permitida.');
        }

        $fileName = sprintf('%s.%s', bin2hex(random_bytes(16)), $extension);
        $destination = $this->uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('No se pudo guardar la imagen subida en el servidor.');
        }

        return $fileName;
    }

    /**
     * Elimina una imagen previa de manera segura
     */
    public function deleteOldImage(?string $fileName): void
    {
        if (!empty($fileName) && $fileName !== 'default_product.png') {
            $filePath = $this->uploadDir . $fileName;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }
}