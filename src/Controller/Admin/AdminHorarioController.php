<?php

namespace App\Controller\Admin;

use App\Entity\HorarioProfesor;
use App\Entity\Profesor;
use App\Entity\ProfesorCurso;
use App\Repository\CursoRepository;
use App\Repository\DivisionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminHorarioController extends AbstractController
{
    /**
     * @Route("/admin/profesores/{id}/horarios", name="admin_horario_edit")
     */
    public function editar(
        Profesor $profesor,
        Request $request,
        EntityManagerInterface $em,
        CursoRepository $cursos,
        DivisionRepository $divisiones
    ): Response {
        $listaCursos = $cursos->findBy([], ['numero' => 'ASC']);
        $listaDivisiones = $divisiones->findBy([], ['numero' => 'ASC']);
        $error = null;

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('horario_'.$profesor->getId(), (string) $request->request->get('_token'))) {
                $error = 'La solicitud no es válida.';
            } else {
                $error = $this->guardar($profesor, $request, $em, $cursos, $divisiones);
                if (null === $error) {
                    $this->addFlash('success', 'Cursos y horarios actualizados.');

                    return $this->redirectToRoute('admin_horario_edit', ['id' => $profesor->getId()]);
                }
            }
        }

        $asignaciones = $profesor->getAsignaciones()->toArray();
        usort($asignaciones, function (ProfesorCurso $a, ProfesorCurso $b) {
            $porCurso = $a->getCurso()->getNumero() <=> $b->getCurso()->getNumero();

            return 0 !== $porCurso ? $porCurso : $a->getDivision()->getNumero() <=> $b->getDivision()->getNumero();
        });

        $bloques = [];
        foreach ($asignaciones as $asignacion) {
            $porDia = [];
            foreach ($asignacion->getHorarios() as $horario) {
                $porDia[$horario->getDiaSemana()] = $horario;
            }
            $bloques[] = ['asignacion' => $asignacion, 'porDia' => $porDia];
        }

        $turnos = [];
        foreach ($listaDivisiones as $division) {
            $turnos[$division->getId()] = $division->getTurno();
        }

        return $this->render('admin/horario/edit.html.twig', [
            'profesor' => $profesor,
            'dias' => HorarioProfesor::getDias(),
            'bloques' => $bloques,
            'cursos' => $listaCursos,
            'divisiones' => $listaDivisiones,
            'turnos' => $turnos,
            'error' => $error,
        ]);
    }

    private function guardar(
        Profesor $profesor,
        Request $request,
        EntityManagerInterface $em,
        CursoRepository $cursos,
        DivisionRepository $divisiones
    ): ?string {
        $posted = $request->request->all('curso');
        if (!is_array($posted)) {
            $posted = [];
        }

        $propias = [];
        foreach ($profesor->getAsignaciones() as $asignacion) {
            $propias[$asignacion->getId()] = $asignacion;
        }

        $planes = [];
        foreach ($posted as $id => $datos) {
            if (!isset($propias[(int) $id]) || !is_array($datos)) {
                return 'Hay un curso que no corresponde a este profesor.';
            }
            $asignacion = $propias[(int) $id];
            if (!empty($datos['quitar'])) {
                $planes[] = ['quitar' => true, 'asignacion' => $asignacion];
                continue;
            }

            $horarios = $this->armarHorarios($datos, $asignacion->getEtiqueta());
            if (isset($horarios['error'])) {
                return $horarios['error'];
            }
            $planes[] = ['quitar' => false, 'asignacion' => $asignacion, 'horarios' => $horarios['horarios']];
        }

        $nuevo = $this->prepararNuevo($profesor, $request, $cursos, $divisiones);
        if (isset($nuevo['error'])) {
            return $nuevo['error'];
        }

        foreach ($planes as $plan) {
            foreach ($plan['asignacion']->getHorarios()->toArray() as $horario) {
                $em->remove($horario);
            }
            if ($plan['quitar']) {
                $em->remove($plan['asignacion']);
            }
        }
        $em->flush();

        foreach ($planes as $plan) {
            if ($plan['quitar']) {
                continue;
            }
            foreach ($plan['horarios'] as $horario) {
                $plan['asignacion']->addHorario($horario);
                $em->persist($horario);
            }
        }

        if ($nuevo) {
            $asignacion = new ProfesorCurso();
            $asignacion->setCurso($nuevo['curso']);
            $asignacion->setDivision($nuevo['division']);
            $profesor->addAsignacion($asignacion);
            $em->persist($asignacion);
            foreach ($nuevo['horarios'] as $horario) {
                $asignacion->addHorario($horario);
                $em->persist($horario);
            }
        }

        $em->flush();

        return null;
    }

    /**
     * @return array{curso: \App\Entity\Curso, division: \App\Entity\Division, horarios: HorarioProfesor[]}|array{error: string}|null
     */
    private function prepararNuevo(Profesor $profesor, Request $request, CursoRepository $cursos, DivisionRepository $divisiones): ?array
    {
        $cursoId = (int) $request->request->get('nuevo_curso');
        $divisionId = (int) $request->request->get('nuevo_division');
        if (0 === $cursoId && 0 === $divisionId) {
            return null;
        }
        if (0 === $cursoId || 0 === $divisionId) {
            return ['error' => 'Para agregar un curso elegí el año y la división.'];
        }

        $curso = $cursos->find($cursoId);
        $division = $divisiones->find($divisionId);
        if (!$curso || !$division) {
            return ['error' => 'El curso o la división no existen.'];
        }

        foreach ($profesor->getAsignaciones() as $asignacion) {
            if ($asignacion->getCurso()->getId() === $curso->getId() && $asignacion->getDivision()->getId() === $division->getId()) {
                return ['error' => 'Ese curso y división ya están asignados.'];
            }
        }

        $horarios = $this->armarHorarios([
            'dias' => $request->request->all('nuevo_dias'),
            'entrada' => $request->request->all('nuevo_entrada'),
            'salida' => $request->request->all('nuevo_salida'),
        ], $curso->getNombre().' '.$division->getNombre());
        if (isset($horarios['error'])) {
            return ['error' => $horarios['error']];
        }

        return [
            'curso' => $curso,
            'division' => $division,
            'horarios' => $horarios['horarios'],
        ];
    }

    /**
     * @param array<string, mixed> $datos
     *
     * @return array{horarios: HorarioProfesor[]}|array{error: string}
     */
    private function armarHorarios(array $datos, string $etiqueta): array
    {
        $seleccionados = $datos['dias'] ?? [];
        if (!is_array($seleccionados)) {
            $seleccionados = [];
        }
        $entradas = isset($datos['entrada']) && is_array($datos['entrada']) ? $datos['entrada'] : [];
        $salidas = isset($datos['salida']) && is_array($datos['salida']) ? $datos['salida'] : [];
        $horarios = [];

        foreach (HorarioProfesor::getDias() as $numero => $nombre) {
            if (!in_array((string) $numero, $seleccionados, true) && !in_array($numero, $seleccionados, true)) {
                continue;
            }

            $entrada = $this->parseHora((string) ($entradas[$numero] ?? ''));
            $salida = $this->parseHora((string) ($salidas[$numero] ?? ''));
            if (!$entrada || !$salida) {
                return ['error' => 'Indique hora de entrada y de salida del '.$nombre.' para '.$etiqueta.'.'];
            }
            if ($salida <= $entrada) {
                return ['error' => 'La salida del '.$nombre.' en '.$etiqueta.' debe ser posterior a la entrada.'];
            }

            $horario = new HorarioProfesor();
            $horario->setDiaSemana($numero);
            $horario->setHoraEntrada($entrada);
            $horario->setHoraSalida($salida);
            $horarios[] = $horario;
        }

        return ['horarios' => $horarios];
    }

    private function parseHora(string $valor): ?\DateTime
    {
        if (!preg_match('/^(\d{2}):(\d{2})/', trim($valor), $coincide)) {
            return null;
        }

        $hora = \DateTime::createFromFormat('!H:i', $coincide[1].':'.$coincide[2]);

        return $hora instanceof \DateTime ? $hora : null;
    }
}
