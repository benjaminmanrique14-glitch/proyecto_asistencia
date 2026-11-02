<?php

namespace App\Security;

use App\Entity\Profesor;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfesorUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Profesor) {
            return;
        }

        if (!$user->getIsActive()) {
            throw new CustomUserMessageAccountStatusException('Esta cuenta de profesor está deshabilitada.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
