<?php

namespace App\Controller;

use App\Entity\Justificacion;
use App\Repository\QrTokenRepository;
use App\Service\JustificacionUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class JustificacionMovilController extends AbstractController
{
    /**
     * @Route("/justificar/{token}", name="justificacion_movil", methods={"GET", "POST"})
     */
    public function justificar(
        string $token,
        Request $request,
        QrTokenRepository $tokens,
        UserPasswordHasherInterface $passwordHasher,
        JustificacionUploadService $uploader,
        EntityManagerInterface $em
    ): Response {
        $qrToken = $tokens->findValidByToken($token);
        if (!$qrToken) {
            return $this->render('mobile/justificar.html.twig', [
                'estado' => 'invalido',
            ]);
        }

        $session = $request->getSession();
        $sessionKey = 'justificacion_ok_'.$token;
        $desbloqueado = $session->get($sessionKey) === true;
        $error = null;

        if ($request->isMethod('POST') && !$desbloqueado) {
            if (!$this->isCsrfTokenValid('justificar_password', (string) $request->request->get('_token'))) {
                $error = 'La solicitud no es válida. Recargue la página.';
            } else {
                $password = (string) $request->request->get('password', '');
                $profesor = $qrToken->getProfesor();
                if ($profesor && $passwordHasher->isPasswordValid($profesor, $password)) {
                    $session->set($sessionKey, true);
                    $desbloqueado = true;
                } else {
                    $error = 'La contraseña no coincide.';
                }
            }
        }

        if ($request->isMethod('POST') && $desbloqueado && $request->request->has('tipo')) {
            if (!$this->isCsrfTokenValid('justificar_form', (string) $request->request->get('_token'))) {
                $error = 'La solicitud no es válida. Recargue la página.';
            } else {
                $error = $this->guardar(
                    $request,
                    $qrToken->getProfesor(),
                    $uploader,
                    $em
                );

                if (null === $error) {
                    $qrToken->setIsUsed(true);
                    $em->flush();
                    $session->remove($sessionKey);

                    return $this->render('mobile/justificar.html.twig', [
                        'estado' => 'enviado',
                    ]);
                }
            }
        }

        return $this->render('mobile/justificar.html.twig', [
            'estado' => $desbloqueado ? 'formulario' : 'password',
            'profesor' => $qrToken->getProfesor(),
            'tipos' => Justificacion::getTipos(),
            'error' => $error,
            'hoy' => (new \DateTime('today'))->format('Y-m-d'),
        ]);
    }

    private function guardar(
        Request $request,
        $profesor,
        JustificacionUploadService $uploader,
        EntityManagerInterface $em
    ): ?string {
        $tipo = (string) $request->request->get('tipo', '');
        if (!array_key_exists($tipo, Justificacion::getTipos())) {
            return 'Seleccione un tipo de justificación.';
        }

        $motivo = trim((string) $request->request->get('motivo', ''));
        if ('' === $motivo) {
            return 'Escriba el motivo de la justificación.';
        }

        $enviadas = $request->request->all();
        $fechas = $enviadas['fechas'] ?? [];
        $fechas = $this->parseFechas($fechas, $tipo);
        if (isset($fechas['error'])) {
            return $fechas['error'];
        }

        $archivo = $request->files->get('archivo');
        if (!$archivo instanceof UploadedFile || !$archivo->isValid()) {
            return $archivo instanceof UploadedFile
                ? 'No se pudo recibir el archivo. Compruebe que no supere 4 MB.'
                : 'Adjunte el archivo de la justificación.';
        }

        $errorArchivo = $uploader->validate($archivo);
        if (null !== $errorArchivo) {
            return $errorArchivo;
        }

        $ruta = $uploader->upload($archivo);

        foreach ($fechas['fechas'] as $fecha) {
            $justificacion = new Justificacion();
            $justificacion->setProfesor($profesor);
            $justificacion->setTipo($tipo);
            $justificacion->setFechaAfectada($fecha);
            $justificacion->setMotivo($motivo);
            $justificacion->setArchivoUrl($ruta);
            $em->persist($justificacion);
        }

        return null;
    }

    /**
     * @param mixed $rawFechas
     *
     * @return array{fechas: \DateTime[]} | array{error: string}
     */
    private function parseFechas($rawFechas, string $tipo): array
    {
        if (!is_array($rawFechas)) {
            $rawFechas = [$rawFechas];
        }

        $hoy = new \DateTime('today');
        $fechas = [];
        $vistas = [];

        foreach ($rawFechas as $raw) {
            $raw = trim((string) $raw);
            if ('' === $raw) {
                continue;
            }

            $fecha = \DateTime::createFromFormat('Y-m-d', $raw);
            if (!$fecha || $fecha->format('Y-m-d') !== $raw) {
                return ['error' => 'Hay una fecha inválida.'];
            }
            $fecha->setTime(0, 0);

            if (Justificacion::TIPO_FALTA_FUTURA === $tipo && $fecha < $hoy) {
                return ['error' => 'Una falta futura debe ser de hoy en adelante.'];
            }
            if (Justificacion::TIPO_FALTA_FUTURA !== $tipo && $fecha > $hoy) {
                return ['error' => 'Esa justificación no puede cargarse en una fecha futura.'];
            }
            if (isset($vistas[$raw])) {
                continue;
            }

            $vistas[$raw] = true;
            $fechas[] = $fecha;
        }

        if (!$fechas) {
            return ['error' => 'Seleccione al menos una fecha.'];
        }

        return ['fechas' => $fechas];
    }
}
