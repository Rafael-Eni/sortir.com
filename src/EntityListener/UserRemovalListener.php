<?php

namespace App\EntityListener;

use App\Entity\Participant;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserRemovalListener
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // Vérifie si l'entité supprimée est un utilisateur
        if ($entity instanceof Participant) {
            // Vérifie si l'utilisateur supprimé est l'utilisateur actuellement connecté
            $token = $this->tokenStorage->getToken();
            if ($token && $token->getUser() === $entity) {
                // Supprime les données de session associées à l'utilisateur
                $this->tokenStorage->setToken(null);
            }
        }
    }
}