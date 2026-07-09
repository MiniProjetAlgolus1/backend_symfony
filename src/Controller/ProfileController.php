<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends AbstractController
{
    // GET /api/me : renvoie les infos de l'utilisateur connecté (identifié via son token JWT)
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ]);
    }

    // PUT /api/me : modifie prénom/nom et (optionnellement) le mot de passe
    #[Route('/api/me', name: 'api_me_update', methods: ['PUT'])]
    public function update(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!empty($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }
        if (!empty($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }
        // Changement de mot de passe optionnel : seulement si le champ est fourni
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                return $this->json(['error' => 'Le mot de passe doit contenir au moins 8 caractères'], 400);
            }
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $violation) {
                $messages[] = $violation->getMessage();
            }
            return $this->json(['error' => implode(', ', $messages)], 400);
        }

        $em->flush();

        return $this->json([
            'message' => 'Profil mis à jour',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ],
        ]);
    }
}
