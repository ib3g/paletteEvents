<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResettingFormType;
use App\Manager\ResettingManager;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResettingController extends BaseController
{
    private  ResettingManager $resettingManager;
    private EntityManagerInterface $entityManager;
    private UserManager $userManager;


    public function __construct(ResettingManager $resettingManager, EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $this->resettingManager = $resettingManager;
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
    }

    #[Route('/request', name: 'app_user_request', methods: ['GET','POST'])]

    public function request(): Response
    {

        return $this->render('resetting/request.html.twig');
    }

    #[Route('/send-email', name: 'app_user_send_reset_email', methods: ['GET','POST'])]

    public function sendResetEmail(Request $request,UserRepository $userRepository): Response
    {
        $username = $request->request->get('username');
         $user= $userRepository->findOneBy(['email' => $username]);
        if(empty($user))
        {
          $this->addErrorFlash('Email n\'existe pas !');
            return $this->redirectToRoute('app_user_request');
        }
        $this->resettingManager->resetPassword($username);

        return new RedirectResponse($this->generateUrl('app_user_check_email', ['username' => $username]));

    }

    #[Route('/checking', name: 'app_user_check_email', methods: ['GET','POST'])]

    public function checkResetEmail(Request $request): Response
    {
        $username = $request->query->get('username');

        if (empty($username)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('app_user_request'));
        }
        return $this->render('resetting/check_email.html.twig');

    }

    #[Route('/reset', name: 'app_user_reset', methods: ['GET','POST'])]

    public function reset(Request $request): Response
    {
        $token = $request->query->get('token');
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (null === $user || $user->getConfirmationToken() === null || $token !== $user->getConfirmationToken() ) {
            $this->addErrorFlash('Token invalide ou expiré !');
            return new RedirectResponse($this->generateUrl('app_login'));
        }
        $form = $this->createForm(ResettingFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->userManager->updatePassword($user, $user->getPassword());
            $this->addSuccessFlash();
            return $this->redirectToRoute('app_login');

        }

        return $this->render('resetting/reset.html.twig', [
            'form' => $form->createView(),
            'token' => $token
        ]);

    }
}
