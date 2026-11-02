<?php

namespace App\Controller\Totem;

use App\Entity\Profesor;
use App\Service\PhoneUrlResolver;
use App\Service\QrCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TotemQrController extends AbstractController
{
    /**
     * @Route("/totem/justificar", name="totem_qr", methods={"GET", "POST"})
     */
    public function generar(QrCodeService $qrCodeService, PhoneUrlResolver $phoneUrlResolver): Response
    {
        $profesor = $this->getUser();
        if (!$profesor instanceof Profesor) {
            throw $this->createAccessDeniedException();
        }

        $qrToken = $qrCodeService->createToken($profesor, 5);
        $url = $phoneUrlResolver->forPhone($this->generateUrl('justificacion_movil', [
            'token' => $qrToken->getToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('totem/qr.html.twig', [
            'profesor' => $profesor,
            'qrToken' => $qrToken,
            'qrDataUri' => $qrCodeService->toDataUri($url),
            'url' => $url,
        ]);
    }
}
