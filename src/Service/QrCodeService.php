<?php

namespace App\Service;

use App\Entity\Profesor;
use App\Entity\QrToken;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createToken(Profesor $profesor, int $ttlMinutes = 5): QrToken
    {
        $qrToken = new QrToken();
        $qrToken->setProfesor($profesor);
        $qrToken->setToken(bin2hex(random_bytes(32)));
        $qrToken->setExpiresAt(new \DateTime(sprintf('+%d minutes', $ttlMinutes)));
        $qrToken->setIsUsed(false);

        $this->entityManager->persist($qrToken);
        $this->entityManager->flush();

        return $qrToken;
    }

    public function toDataUri(string $content): string
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($content)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
            ->size(420)
            ->margin(12)
            ->build();

        return $result->getDataUri();
    }
}
