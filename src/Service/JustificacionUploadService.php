<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class JustificacionUploadService
{
    public const MAX_BYTES = 4194304;

    public const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];

    private $targetDirectory;

    public function __construct(string $justificacionesDirectory)
    {
        $this->targetDirectory = $justificacionesDirectory;
    }

    public function validate(UploadedFile $file): ?string
    {
        if ($file->getSize() > self::MAX_BYTES) {
            return 'El archivo supera el máximo permitido de 4 MB.';
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return 'Formato no permitido. Use JPG, PNG, PDF, DOC o DOCX.';
        }

        $mime = $file->getMimeType();
        if (!in_array($mime, self::ALLOWED_MIME_TYPES, true)) {
            return 'El tipo de archivo no es válido.';
        }

        return null;
    }

    public function upload(UploadedFile $file): string
    {
        if (!is_dir($this->targetDirectory) && !mkdir($this->targetDirectory, 0775, true) && !is_dir($this->targetDirectory)) {
            throw new FileException('No se pudo crear el directorio de justificaciones.');
        }

        $extension = strtolower((string) $file->guessExtension() ?: $file->getClientOriginalExtension());
        $filename = bin2hex(random_bytes(16)).'.'.$extension;
        $file->move($this->targetDirectory, $filename);

        return 'uploads/justificaciones/'.$filename;
    }
}
