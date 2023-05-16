<?php

namespace App\Manager;

use App\Entity\Event;
use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class UserManager
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EmailVerifier $emailVerifier;
    private CustomMailer $mailer;
    private Environment $twig;
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EmailVerifier $emailVerifier
     * @param CustomMailer $mailer
     * @param Environment $twig
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, EmailVerifier $emailVerifier, CustomMailer $mailer, Environment $twig, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->emailVerifier = $emailVerifier;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
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

    public function sendAnimatorInvitationsEmail(Event $event)
    {
        // if event has status new, send email to all animators
        if ($event->getStatus() === Event::STATUS_NEW) {
            $animators = $event->getAnimators();

            $url = $this->urlGenerator->generate('app_event_show', ['id' => $event->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            foreach ($animators as $animator) {
                $htmlContents = $this->twig->render('mail/event_animator_invitation.html.twig', [
                    'event' => $event,
                    'animator' => $animator,
                    'url' => $url
                ]);

                $this->mailer->send("Invitation d'animation d'un évènement - PaletteEvents", $htmlContents, $animator->getEmail());
            }
        }
    }
}