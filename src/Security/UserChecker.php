<?php

namespace App\Security;

use App\Entity\Participant;
use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Participant) {
            return;
        }

        if (!$user->isActif()) {
            throw new CustomUserMessageAccountStatusException('Ton compte doit être validé par un admin');
        }

        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('Tu dois valider ton adresse mail avant de te connecter');
        }

    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof Participant) {
            return;
        }
    }
}