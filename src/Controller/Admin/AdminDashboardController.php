<?php

namespace App\Controller\Admin;

use App\Entity\HorarioProfesor;
use App\Entity\Profesor;
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
        $ahora = new \DateTime('now');
        $fecha = $this->fechaSeleccionada($request->query->get('fecha'), $hoy);
        $mes = $this->mesCalendario($request->query->get('mes'), $fecha, $hoy);
        $fecha = $this->ajustarFechaAlMes($fecha, $mes, $hoy, $request->query->has('fecha'));

        $diaSemana = (int) $fecha->format('N');
        $presentes = $asistencias->findByFecha($fecha);
        $justificados = $justificaciones->findByFecha($fecha);

        $presentesIds = [];
        foreach ($presentes as $asistencia) {
            $presentesIds[$asistencia->getProfesor()->getId()] = true;
        }

        $justificadosIds = [];
        foreach ($justificados as $justificacion) {
            $justificadosIds[$justificacion->getProfesor()->getId()] = true;
        }

        $ausentes = [];
        $cursosDelDia = [];
        $pendientes = [];

        if ($diaSemana >= 1 && $diaSemana <= 5) {
            $horariosPorProfesor = [];
            foreach ($horarios->findByDiaSemana($diaSemana) as $horario) {
                $id = $horario->getProfesor()->getId();
                $horariosPorProfesor[$id][] = $horario;
                $cursosDelDia[$id][] = sprintf(
                    '%s %s–%s',
                    $horario->getProfesorCurso()->getEtiqueta(),
                    $horario->getHoraEntrada()->format('H:i'),
                    $horario->getHoraSalida()->format('H:i')
                );
            }

            foreach ($horariosPorProfesor as $id => $lista) {
                /** @var Profesor $profesor */
                $profesor = $lista[0]->getProfesor();
                if (isset($presentesIds[$id]) || isset($justificadosIds[$id])) {
                    continue;
                }

                if ($this->ausenciaEfectiva($fecha, $hoy, $ahora, $lista)) {
                    $ausentes[$id] = $profesor;
                } else {
                    $pendientes[$id] = $profesor;
                }
            }
        }

        $mesAnterior = (clone $mes)->modify('first day of previous month')->setTime(0, 0);
        $mesSiguiente = (clone $mes)->modify('first day of next month')->setTime(0, 0);
        $mesActual = new \DateTime($hoy->format('Y-m-01'));
        $puedeMesSiguiente = $mesSiguiente <= $mesActual;

        return $this->render('admin/dashboard.html.twig', [
            'fecha' => $fecha,
            'hoy' => $hoy,
            'mes' => $mes,
            'mesAnterior' => $mesAnterior,
            'mesSiguiente' => $mesSiguiente,
            'puedeMesSiguiente' => $puedeMesSiguiente,
            'dias' => $this->diasDelMes($mes),
            'presentes' => $presentes,
            'ausentes' => array_values($ausentes),
            'pendientes' => array_values($pendientes),
            'cursosDelDia' => $cursosDelDia,
            'justificados' => $justificados,
            'esFinDeSemana' => $diaSemana > 5,
        ]);
    }

    /**
     * Ausente solo si ya pasó el fin de su jornada (última hora de salida)
     * y no hay ingreso ni justificación.
     *
     * @param HorarioProfesor[] $horariosDelDia
     */
    private function ausenciaEfectiva(\DateTime $fecha, \DateTime $hoy, \DateTime $ahora, array $horariosDelDia): bool
    {
        if ($fecha < $hoy) {
            return true;
        }

        if ($fecha > $hoy) {
            return false;
        }

        $finJornada = $this->finJornada($fecha, $horariosDelDia);

        return $ahora > $finJornada;
    }

    /**
     * @param HorarioProfesor[] $horariosDelDia
     */
    private function finJornada(\DateTime $fecha, array $horariosDelDia): \DateTime
    {
        $fin = null;
        foreach ($horariosDelDia as $horario) {
            $salida = \DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $fecha->format('Y-m-d').' '.$horario->getHoraSalida()->format('H:i:s')
            );
            if (!$salida instanceof \DateTime) {
                continue;
            }
            if (null === $fin || $salida > $fin) {
                $fin = $salida;
            }
        }

        return $fin instanceof \DateTime ? $fin : (clone $fecha)->setTime(23, 59, 59);
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

    private function mesCalendario(?string $raw, \DateTime $fecha, \DateTime $hoy): \DateTime
    {
        $mes = $raw ? \DateTime::createFromFormat('Y-m', $raw) : false;
        if (!$mes || $mes->format('Y-m') !== $raw) {
            $mes = new \DateTime($fecha->format('Y-m-01'));
        } else {
            $mes = new \DateTime($mes->format('Y-m-01'));
        }

        $mesActual = new \DateTime($hoy->format('Y-m-01'));
        if ($mes > $mesActual) {
            return $mesActual;
        }

        return $mes;
    }

    /**
     * Si se cambió de mes sin elegir día, muestra el último día consultable de ese mes.
     */
    private function ajustarFechaAlMes(\DateTime $fecha, \DateTime $mes, \DateTime $hoy, bool $fechaExplicita): \DateTime
    {
        if ($fecha->format('Y-m') === $mes->format('Y-m')) {
            return $fecha;
        }

        if ($fechaExplicita) {
            // Día de otro mes: el mes del calendario se alinea a esa fecha.
            return $fecha;
        }

        $ultimo = new \DateTime($mes->format('Y-m-t'));
        $ultimo->setTime(0, 0);
        if ($ultimo > $hoy) {
            return clone $hoy;
        }

        return $ultimo;
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
