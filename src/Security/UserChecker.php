<?php

namespace App\Security;

use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    private ContainerInterface $container;
    private UserManager $userManager;

    /**
     * @param ContainerInterface $container
     * @param UserManager $userManager
     */
    public function __construct(ContainerInterface $container, UserManager $userManager)
    {
        $this->container = $container;
        $this->userManager = $userManager;
    }

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isVerified()) {
            $this->userManager->sendEmailConfirmation($user);

            throw new CustomUserMessageAuthenticationException(
                'Votre compte n\'est pas encore confirmé, vérifier votre boîte mail.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        $this->checkPreAuth($user);
    }
}