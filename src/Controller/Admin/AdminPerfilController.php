<?php

namespace App\Controller\Admin;

use App\Entity\Administrador;
use App\Repository\AdministradorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class AdminPerfilController extends AbstractController
{
    /**
     * @Route("/admin/perfil", name="admin_perfil")
     */
    public function editar(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        AdministradorRepository $admins,
        TokenStorageInterface $tokenStorage
    ): Response {
        /** @var Administrador $admin */
        $admin = $this->getUser();
        $error = null;
        $data = ['username' => $admin->getUsername()];

        if ($request->isMethod('POST')) {
            $data['username'] = trim((string) $request->request->get('username', ''));
            $error = $this->guardar($admin, $data, $request, $hasher, $admins, $em, $tokenStorage);
            if (null === $error) {
                $this->addFlash('success', 'Perfil actualizado.');

                return $this->redirectToRoute('admin_perfil');
            }
        }

        return $this->render('admin/perfil.html.twig', [
            'admin' => $admin,
            'data' => $data,
            'error' => $error,
        ]);
    }

    private function guardar(
        Administrador $admin,
        array $data,
        Request $request,
        UserPasswordHasherInterface $hasher,
        AdministradorRepository $admins,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage
    ): ?string {
        if (!$this->isCsrfTokenValid('admin_perfil', (string) $request->request->get('_token'))) {
            return 'La solicitud no es válida.';
        }

        $username = $data['username'];
        if (!preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $username)) {
            return 'El usuario debe tener entre 3 y 50 caracteres (letras, números, punto, guion o guion bajo).';
        }

        $existente = $admins->findOneBy(['username' => $username]);
        if ($existente && $existente->getId() !== $admin->getId()) {
            return 'Ese nombre de usuario ya está en uso.';
        }

        $passwordActual = (string) $request->request->get('password_actual', '');
        $passwordNueva = (string) $request->request->get('password_nueva', '');
        $passwordRepetir = (string) $request->request->get('password_repetir', '');
        $cambiaPassword = '' !== $passwordNueva || '' !== $passwordRepetir;

        if ($cambiaPassword || $username !== $admin->getUsername()) {
            if ('' === $passwordActual) {
                return 'Ingresá tu contraseña actual para confirmar los cambios.';
            }
            if (!$hasher->isPasswordValid($admin, $passwordActual)) {
                return 'La contraseña actual no es correcta.';
            }
        }

        if ($cambiaPassword) {
            if (strlen($passwordNueva) < 6) {
                return 'La nueva contraseña debe tener al menos 6 caracteres.';
            }
            if ($passwordNueva !== $passwordRepetir) {
                return 'La confirmación de la nueva contraseña no coincide.';
            }
            $admin->setPassword($hasher->hashPassword($admin, $passwordNueva));
        }

        $admin->setUsername($username);
        $em->flush();

        $token = new PostAuthenticationToken($admin, 'admin', $admin->getRoles());
        $tokenStorage->setToken($token);

        return null;
    }
}
