<?php

namespace App\Controller\Totem;

use App\Entity\Asistencia;
use App\Entity\Profesor;
use App\Repository\AsistenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TotemAsistenciaController extends AbstractController
{
    /**
     * @Route("/totem", name="totem_home", methods={"GET"})
     */
    public function home(AsistenciaRepository $asistencias): Response
    {
        $profesor = $this->requireProfesor();
        $hoy = new \DateTime('today');

        return $this->render('totem/home.html.twig', [
            'profesor' => $profesor,
            'asistencia' => $asistencias->findOneByProfesorAndFecha($profesor, $hoy),
            'hoy' => $hoy,
        ]);
    }

    /**
     * @Route("/totem/marcar", name="totem_marcar", methods={"POST"})
     */
    public function marcar(Request $request, AsistenciaRepository $asistencias, EntityManagerInterface $em): Response
    {
        $profesor = $this->requireProfesor();

        if (!$this->isCsrfTokenValid('totem_marcar', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'La solicitud no es válida. Intente de nuevo.');

            return $this->redirectToRoute('totem_home');
        }

        $hoy = new \DateTime('today');
        $ahora = new \DateTime();
        $asistencia = $asistencias->findOneByProfesorAndFecha($profesor, $hoy);

        if (!$asistencia) {
            $asistencia = new Asistencia();
            $asistencia->setProfesor($profesor);
            $asistencia->setFecha($hoy);
            $asistencia->setHoraIngreso($ahora);
            $em->persist($asistencia);
            $this->addFlash('success', 'Ingreso registrado a las '.$ahora->format('H:i').'.');
        } elseif (null === $asistencia->getHoraSalida()) {
            $asistencia->setHoraSalida($ahora);
            $this->addFlash('success', 'Salida registrada a las '.$ahora->format('H:i').'.');
        } else {
            $this->addFlash('error', 'La jornada de hoy ya está cerrada.');
        }

        $em->flush();

        return $this->redirectToRoute('totem_home');
    }

    private function requireProfesor(): Profesor
    {
        $profesor = $this->getUser();
        if (!$profesor instanceof Profesor) {
            throw $this->createAccessDeniedException();
        }

        return $profesor;
    }
}
