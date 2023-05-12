<?php

namespace App\Manager;

use App\Entity\User;
use App\Manager\CustomMailer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Twig\Environment;


class ResettingManager
{

    private CustomMailer $mailer;
    private UserRepository $userRepository;
    private UrlGeneratorInterface $router;
    private TokenGeneratorInterface $tokenGenerator;
    private EntityManagerInterface $entityManager;
    private ContainerInterface $container;
    private Environment $twig;

    /**
     * @param CustomMailer $mailer
     * @param UserRepository $userRepository
     * @param UrlGeneratorInterface $router
     * @param TokenGeneratorInterface $tokenGenerator
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     * @param Environment $twig
     */
    public function __construct(CustomMailer $mailer, UserRepository $userRepository, UrlGeneratorInterface $router, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager, ContainerInterface $container, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->tokenGenerator = $tokenGenerator;
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->twig = $twig;
    }


    public function resetPassword(string $email)
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user) {
            $url = $this->generateUrlToken($user);
            $htmlContents = $this->twig->render('mail/password_reset.html.twig', [
                'expiration_date' => new \DateTime('+1 days'),
                'firstName' => $user->getFullName(),
                'url' => $url

            ]);

            $this->mailer->send('Réinitialisation du mot de passe', $htmlContents, $user->getEmail());
        }
        else {
            $this->container->get('session')->getFlashBag()->add('danger', 'Cet email n\'existe pas');
        }
    }

    public function sendJoinLink(string $email)
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (null !== $user) {

            $url = $this->generateUrlToken($user);
            $htmlContents = $this->twig->render('mail/mail_user_created.html.twig', [
                'expiration_date' => new \DateTime('+1 days'),
                'firstName' => $user->getFullName(),
                'url' => $url,
                'user' => $user

            ]);

            $this->mailer->send('Rejoindre PaletteEvent', $htmlContents, $user->getEmail());
        }
    }

    public function generateUrlToken($user)
    {
        if(!$user) {
            return null;
        }
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $this->router->generate('app_user_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

    }
}