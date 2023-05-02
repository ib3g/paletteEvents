<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Prix;
use App\Form\PrixType;
use App\Repository\PrixRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrixController extends BaseController
{
    #[Route('/admin/event/{id}/prix', name: 'app_prix_index', methods: ['GET'])]
    public function index(Event $event, PrixRepository $prixRepository): Response
    {
        $prices = $prixRepository->findBy(['event' => $event]);

        return $this->render('prix/index.html.twig', [
            'prices' => $prices,
            'event' => $event,
        ]);
    }

    #[Route('/admin/prix/{id}/new', name: 'app_prix_new', methods: ['GET', 'POST'])]
    public function new(Event $event, Request $request, PrixRepository $prixRepository): Response
    {
        $prix = new Prix();
        $form = $this->createForm(PrixType::class, $prix);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->addPrix($prix);
            $prixRepository->save($prix, true);

            return $this->redirectToRoute('app_prix_index', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('prix/new.html.twig', [
            'event' => $event,
            'prix' => $prix,
            'form' => $form,
        ]);
    }

    #[Route('/admin/prix/{id}', name: 'app_prix_show', methods: ['GET'])]
    public function show(Prix $prix): Response
    {
        return $this->render('prix/show.html.twig', [
            'prix' => $prix,
        ]);
    }

    #[Route('/admin/prix/{id}/edit', name: 'app_prix_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prix $prix, PrixRepository $prixRepository): Response
    {
        $form = $this->createForm(PrixType::class, $prix);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prixRepository->save($prix, true);

            return $this->redirectToRoute('app_prix_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('prix/edit.html.twig', [
            'event' => $prix->getEvent(),
            'prix' => $prix,
            'form' => $form,
        ]);
    }

    #[Route('/admin/prix/{id}', name: 'app_prix_delete', methods: ['POST'])]
    public function delete(Request $request, Prix $prix, PrixRepository $prixRepository): Response
    {
        $event = $prix->getEvent();

        if (count($prix->getTickets())) {
            $this->addErrorFlash('Impossible de supprimer un prix qui a déjà des tickets vendus');
            return $this->redirectToRoute('app_prix_index', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$prix->getId(), $request->request->get('_token'))) {
            $prixRepository->remove($prix, true);
        }

        return $this->redirectToRoute('app_prix_index', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
    }
}
