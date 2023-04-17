<?php

namespace App\Manager;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EmailVerifier $emailVerifier;


    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, EmailVerifier $emailVerifier)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->emailVerifier = $emailVerifier;
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

    public function sendEmailConfirmation($user)
    {
        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation($user);
    }
}