<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
// use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

final class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'app_register', methods:['POST'])]
    public function register(
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $userPasswordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['error' => 'Email et mot de passe requis'], 400); //bad reaquest
        }
        $existing = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existing) {
            return $this->json(['error' => 'Cet email est déjà utilisé'], 409); //I know it's not a good suggestion; you need to return 400 or 404 "for security."
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($userPasswordHasher->hashPassword($user, $data['password']));

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Utilisateur créé', 'email' => $user->getEmail()], 201);
    }

}
