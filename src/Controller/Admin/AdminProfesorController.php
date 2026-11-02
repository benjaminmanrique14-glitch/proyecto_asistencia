<?php

namespace App\Controller\Admin;

use App\Entity\Profesor;
use App\Repository\AsistenciaRepository;
use App\Repository\HorarioProfesorRepository;
use App\Repository\JustificacionRepository;
use App\Repository\ProfesorRepository;
use App\Service\ProfesorFotoUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminProfesorController extends AbstractController
{
    /**
     * @Route("/admin/profesores", name="admin_profesor_index")
     */
    public function index(ProfesorRepository $profesores): Response
    {
        return $this->render('admin/profesor/index.html.twig', [
            'profesores' => $profesores->findAllOrderedWithAsignaciones(),
        ]);
    }

    /**
     * @Route("/admin/profesores/nuevo", name="admin_profesor_new")
     */
    public function nuevo(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ProfesorRepository $profesores,
        ProfesorFotoUploadService $fotos
    ): Response {
        $error = null;
        $data = ['dni' => '', 'nombre' => '', 'apellido' => ''];

        if ($request->isMethod('POST')) {
            $data = $this->datos($request);
            $error = $this->validar($data, $request, $profesores, null, true, $fotos);
            if (null === $error) {
                $profesor = new Profesor();
                $this->aplicar($profesor, $data, $request, $hasher, true, $fotos);
                $em->persist($profesor);
                $em->flush();
                $this->addFlash('success', 'Profesor creado.');

                return $this->redirectToRoute('admin_profesor_show', ['id' => $profesor->getId()]);
            }
        }

        return $this->render('admin/profesor/form.html.twig', [
            'profesor' => null,
            'data' => $data,
            'error' => $error,
            'titulo' => 'Nuevo profesor',
        ]);
    }

    /**
     * @Route("/admin/profesores/{id}", name="admin_profesor_show", requirements={"id"="\d+"})
     */
    public function show(
        int $id,
        ProfesorRepository $profesores,
        AsistenciaRepository $asistencias,
        JustificacionRepository $justificaciones,
        HorarioProfesorRepository $horarios
    ): Response {
        $profesor = $profesores->findOneWithAsignaciones($id);
        if (!$profesor) {
            throw new NotFoundHttpException('Profesor no encontrado.');
        }

        return $this->render('admin/profesor/show.html.twig', [
            'profesor' => $profesor,
            'asistencias' => $asistencias->findByProfesorOrdered($profesor),
            'justificaciones' => $justificaciones->findByProfesorOrdered($profesor),
            'horarios' => $horarios->findByProfesorOrdered($profesor),
        ]);
    }

    /**
     * @Route("/admin/profesores/{id}/editar", name="admin_profesor_edit", requirements={"id"="\d+"})
     */
    public function editar(
        Profesor $profesor,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ProfesorRepository $profesores,
        ProfesorFotoUploadService $fotos
    ): Response {
        $error = null;
        $data = [
            'dni' => $profesor->getDni(),
            'nombre' => $profesor->getNombre(),
            'apellido' => $profesor->getApellido(),
        ];

        if ($request->isMethod('POST')) {
            $data = $this->datos($request);
            $error = $this->validar($data, $request, $profesores, $profesor, false, $fotos);
            if (null === $error) {
                $this->aplicar($profesor, $data, $request, $hasher, false, $fotos);
                $em->flush();
                $this->addFlash('success', 'Profesor actualizado.');

                return $this->redirectToRoute('admin_profesor_show', ['id' => $profesor->getId()]);
            }
        }

        return $this->render('admin/profesor/form.html.twig', [
            'profesor' => $profesor,
            'data' => $data,
            'error' => $error,
            'titulo' => 'Editar profesor',
        ]);
    }

    /**
     * @Route("/admin/profesores/{id}/estado", name="admin_profesor_estado", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function estado(Profesor $profesor, Request $request, EntityManagerInterface $em): Response
    {
        $volver = (string) $request->request->get('_redirect', '');
        $destino = 'show' === $volver
            ? $this->redirectToRoute('admin_profesor_show', ['id' => $profesor->getId()])
            : $this->redirectToRoute('admin_profesor_index');

        if (!$this->isCsrfTokenValid('profesor_estado_'.$profesor->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'La solicitud no es válida.');

            return $destino;
        }

        $profesor->setIsActive(!$profesor->getIsActive());
        $em->flush();
        $this->addFlash('success', $profesor->getIsActive() ? 'Profesor reactivado.' : 'Profesor dado de baja.');

        return $destino;
    }

    private function datos(Request $request): array
    {
        return [
            'dni' => trim((string) $request->request->get('dni', '')),
            'nombre' => trim((string) $request->request->get('nombre', '')),
            'apellido' => trim((string) $request->request->get('apellido', '')),
        ];
    }

    private function validar(
        array $data,
        Request $request,
        ProfesorRepository $profesores,
        ?Profesor $actual,
        bool $passwordObligatoria,
        ProfesorFotoUploadService $fotos
    ): ?string {
        if (!$this->isCsrfTokenValid('profesor_form', (string) $request->request->get('_token'))) {
            return 'La solicitud no es válida.';
        }
        if (!preg_match('/^\d{6,20}$/', $data['dni'])) {
            return 'El DNI debe tener entre 6 y 20 dígitos.';
        }
        if ('' === $data['nombre'] || '' === $data['apellido']) {
            return 'Nombre y apellido son obligatorios.';
        }
        if (mb_strlen($data['nombre']) > 100 || mb_strlen($data['apellido']) > 100) {
            return 'Nombre o apellido demasiado largo.';
        }

        $existente = $profesores->findOneBy(['dni' => $data['dni']]);
        if ($existente && (!$actual || $existente->getId() !== $actual->getId())) {
            return 'Ya existe un profesor con ese DNI.';
        }

        $password = (string) $request->request->get('password', '');
        if ($passwordObligatoria && '' === $password) {
            return 'Defina la contraseña inicial.';
        }
        if ('' !== $password && strlen($password) < 6) {
            return 'La contraseña debe tener al menos 6 caracteres.';
        }

        /** @var UploadedFile|null $foto */
        $foto = $request->files->get('foto');
        $errorFoto = $fotos->validate($foto);
        if (null !== $errorFoto) {
            return $errorFoto;
        }

        return null;
    }

    private function aplicar(
        Profesor $profesor,
        array $data,
        Request $request,
        UserPasswordHasherInterface $hasher,
        bool $passwordObligatoria,
        ProfesorFotoUploadService $fotos
    ): void {
        $profesor->setDni($data['dni']);
        $profesor->setNombre($data['nombre']);
        $profesor->setApellido($data['apellido']);

        $password = (string) $request->request->get('password', '');
        if ('' !== $password || $passwordObligatoria) {
            $profesor->setPassword($hasher->hashPassword($profesor, $password));
        }

        if ($request->request->getBoolean('quitar_foto') && $profesor->getFoto()) {
            $fotos->delete($profesor->getFoto());
            $profesor->setFoto(null);
        }

        /** @var UploadedFile|null $foto */
        $foto = $request->files->get('foto');
        if ($foto) {
            $anterior = $profesor->getFoto();
            $profesor->setFoto($fotos->upload($foto));
            if ($anterior) {
                $fotos->delete($anterior);
            }
        }
    }
}
