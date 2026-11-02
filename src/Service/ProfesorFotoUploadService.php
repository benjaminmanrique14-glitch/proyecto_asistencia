<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProfesorFotoUploadService
{
    public const MAX_BYTES = 2097152;

    public const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private $targetDirectory;

    public function __construct(string $profesoresFotosDirectory)
    {
        $this->targetDirectory = $profesoresFotosDirectory;
    }

    public function validate(?UploadedFile $file): ?string
    {
        if (!$file) {
            return null;
        }

        if ($file->getSize() > self::MAX_BYTES) {
            return 'La foto supera el máximo permitido de 2 MB.';
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return 'Formato de foto no permitido. Usá JPG, PNG o WEBP.';
        }

        $mime = $file->getMimeType();
        if (!in_array($mime, self::ALLOWED_MIME_TYPES, true)) {
            return 'El archivo de foto no es una imagen válida.';
        }

        return null;
    }

    public function upload(UploadedFile $file): string
    {
        if (!is_dir($this->targetDirectory) && !mkdir($this->targetDirectory, 0775, true) && !is_dir($this->targetDirectory)) {
            throw new FileException('No se pudo crear el directorio de fotos.');
        }

        $extension = strtolower((string) ($file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'jpg'));
        $filename = bin2hex(random_bytes(16)).'.'.$extension;
        $file->move($this->targetDirectory, $filename);

        return 'uploads/profesores/'.$filename;
    }

    public function delete(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $path = $this->targetDirectory.DIRECTORY_SEPARATOR.basename($relativePath);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
