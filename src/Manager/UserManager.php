<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;


    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    public function updatePassword(User $user, string $password)
    {
        if (0 === strlen($password)) {
            return;
        }
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        // réinitialisation du token à null pour qu'il ne soit plus réutilisable
        $user->setConfirmationToken(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }
}