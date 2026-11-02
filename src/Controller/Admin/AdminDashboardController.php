<?php

namespace App\Controller\Admin;

use App\Repository\AsistenciaRepository;
use App\Repository\HorarioProfesorRepository;
use App\Repository\JustificacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(
        Request $request,
        AsistenciaRepository $asistencias,
        HorarioProfesorRepository $horarios,
        JustificacionRepository $justificaciones
    ): Response {
        $hoy = new \DateTime('today');
        $fecha = $this->fechaSeleccionada($request->query->get('fecha'), $hoy);
        $mes = $this->mesCalendario($request->query->get('mes'), $fecha);

        $diaSemana = (int) $fecha->format('N');
        $presentes = $asistencias->findByFecha($fecha);
        $justificados = $justificaciones->findByFecha($fecha);

        $presentesIds = [];
        foreach ($presentes as $asistencia) {
            $presentesIds[$asistencia->getProfesor()->getId()] = true;
        }

        $justificadosSinAsistencia = [];
        foreach ($justificados as $justificacion) {
            $id = $justificacion->getProfesor()->getId();
            if (!isset($presentesIds[$id])) {
                $justificadosSinAsistencia[$id] = true;
            }
        }

        $ausentes = [];
        $cursosDelDia = [];
        if ($diaSemana >= 1 && $diaSemana <= 5) {
            foreach ($horarios->findByDiaSemana($diaSemana) as $horario) {
                $profesor = $horario->getProfesor();
                $id = $profesor->getId();
                $cursosDelDia[$id][] = sprintf(
                    '%s %s–%s',
                    $horario->getProfesorCurso()->getEtiqueta(),
                    $horario->getHoraEntrada()->format('H:i'),
                    $horario->getHoraSalida()->format('H:i')
                );
                if (isset($presentesIds[$id]) || isset($justificadosSinAsistencia[$id]) || isset($ausentes[$id])) {
                    continue;
                }
                $ausentes[$id] = $profesor;
            }
        }

        return $this->render('admin/dashboard.html.twig', [
            'fecha' => $fecha,
            'hoy' => $hoy,
            'mes' => $mes,
            'dias' => $this->diasDelMes($mes),
            'presentes' => $presentes,
            'ausentes' => array_values($ausentes),
            'cursosDelDia' => $cursosDelDia,
            'justificados' => $justificados,
            'esFinDeSemana' => $diaSemana > 5,
        ]);
    }

    private function fechaSeleccionada(?string $raw, \DateTime $hoy): \DateTime
    {
        $fecha = $raw ? \DateTime::createFromFormat('Y-m-d', $raw) : false;
        if (!$fecha || $fecha->format('Y-m-d') !== $raw) {
            return clone $hoy;
        }

        $fecha->setTime(0, 0);
        if ($fecha > $hoy) {
            return clone $hoy;
        }

        return $fecha;
    }

    private function mesCalendario(?string $raw, \DateTime $fecha): \DateTime
    {
        $mes = $raw ? \DateTime::createFromFormat('Y-m', $raw) : false;
        if (!$mes || $mes->format('Y-m') !== $raw) {
            $mes = new \DateTime($fecha->format('Y-m-01'));
        } else {
            $mes = new \DateTime($mes->format('Y-m-01'));
        }

        return $mes;
    }

    /**
     * @return array<int, array{fecha: \DateTime, enMes: bool}>
     */
    private function diasDelMes(\DateTime $mes): array
    {
        $inicio = new \DateTime($mes->format('Y-m-01'));
        $cursor = clone $inicio;
        $cursor->modify('-'.((int) $inicio->format('N') - 1).' days');

        $dias = [];
        for ($i = 0; $i < 42; ++$i) {
            $dias[] = [
                'fecha' => clone $cursor,
                'enMes' => $cursor->format('m') === $inicio->format('m'),
            ];
            $cursor->modify('+1 day');
        }

        return $dias;
    }
}
