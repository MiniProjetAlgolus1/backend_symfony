<?php

namespace App\EventListener;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

// Se déclenche juste avant l'enregistrement d'une nouvelle Task en base.
// On force TOUJOURS le owner à être l'utilisateur connecté, en ignorant
// ce que le client aurait pu envoyer (sécurité : impossible de créer une tâche
// au nom de quelqu'un d'autre).
#[AsEntityListener(event: Events::prePersist, entity: Task::class)]
class TaskOwnerListener
{
    public function __construct(private Security $security)
    {
    }

    public function prePersist(Task $task, PrePersistEventArgs $args): void
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $task->setOwner($user);
        }
    }
}
