<?php

namespace App\Security;

use App\Entity\User;
use App\Manager\CustomMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Twig\Environment;

class EmailVerifier
{
    private $verifyEmailHelper;
    private CustomMailer $mailer;
    private $entityManager;
    private Environment $twig;

    public function __construct(VerifyEmailHelperInterface $helper, CustomMailer $mailer, EntityManagerInterface $manager, Environment $twig)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
        $this->entityManager = $manager;
        $this->twig = $twig;
    }

    public function sendEmailConfirmation(UserInterface|User $user): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $htmlContents = $this->twig->render('registration/confirmation_email.html.twig', [
            'signedUrl' => $signatureComponents->getSignedUrl(),
            'userName' => $user->getFullName(),
            'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
            'expiresAtMessageData' => $signatureComponents->getExpirationMessageData()
        ]);

        $this->mailer->send("Confirmation de compte email", $htmlContents, $user->getEmail());
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
