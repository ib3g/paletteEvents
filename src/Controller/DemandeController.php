<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\User;
use App\Form\DemandeType;
use App\Manager\CustomMailer;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

#[Route('/admin/demande')]
class DemandeController extends BaseController
{
    #[Route('/', name: 'app_demande_index', methods: ['GET'])]
    public function index(DemandeRepository $demandeRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('demande/index.html.twig', [
            'demandes' => $demandeRepository->allDemandeByEventOwner($user),
        ]);
    }

    #[Route('/new', name: 'app_demande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DemandeRepository $demandeRepository): Response
    {
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demandeRepository->save($demande, true);

            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande/new.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_show', methods: ['GET'])]
    public function show(Demande $demande): Response
    {
        return $this->render('demande/show.html.twig', [
            'demande' => $demande,
        ]);
    }

    #[Route('/{id}/changeStatus', name: 'app_demande_switch_status', methods: ['GET', 'POST'])]
    public function switchStatus(Request $request, Demande $demande,
                                 DemandeRepository $demandeRepository, CustomMailer $mailer,
                                 Environment $twig, UrlGeneratorInterface $urlGenerator,EntityManagerInterface $manager): Response
    {
        $status = $request->get('status');
        if ($demande->getStatus() == Demande::STATUS_PENDING) {
            $demande->setStatus($status);
            $demandeRepository->save($demande, true);
            $event= $demande->getEvent();
            $animators= $event->getAnimators()->getValues();
            $newAnimator= $demande->getUser();
            if(!in_array($newAnimator, $animators)){
                $event->addAnimator($newAnimator);
                $manager->persist($event);
                $manager->flush();
            }
            $url = $urlGenerator->generate('app_event_show', ['id' => $demande->getEvent()->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            $htmlContents = $twig->render('mail/damande_status_update.html.twig', [
                'demande' => $demande,
                'url' => $url
            ]);

            // send email to notify user
            $mailer->send('Votre demande a été mise à jour', $htmlContents, $demande->getUser()->getEmail());
            $this->addSuccessFlash('Demande traitée avec succès et un email a été envoyé à l\'utilisateur');
        }

        return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
    }

}
