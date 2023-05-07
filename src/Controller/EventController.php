<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Role;
use App\Form\EventType;
use App\Manager\CustomMailer;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\PrixRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/admin/event', name: 'admin_events_index', methods: ['GET'])]
    public function adminIndex(EventRepository $eventRepository): Response
    {
        $user = $this->getUser();
        $events = $this->isGranted(Role::ROLE_ADMIN) ? $eventRepository->findAll() : $eventRepository->allEventsByOwner($user, 100);
        return $this->render('event/admin/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event', name: 'events_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/admin/event/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setOwner($this->getUser());
            $eventRepository->save($event, true);

            return $this->redirectToRoute('admin_events_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/admin/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/event/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event,EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findEventsWithSharedCategories($event,3);
        $eventsSameOwner = $eventRepository->findEventsWithSameOwner($event,3);
        return $this->render('event/show.html.twig', [
            'event' => $event,
            'moreEvents' => $events,
            'eventsSameOwner' => $eventsSameOwner,
        ]);
    }

    #[Route('/admin/event/{id}', name: 'admin_event_show', methods: ['GET'])]
    public function adminShow(Event $event,EventRepository $eventRepository): Response
    {
        return $this->render('event/admin/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/admin/event/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->save($event, true);

            return $this->redirectToRoute('admin_events_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/admin/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/admin/event/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $eventRepository->remove($event, true);
        }

        return $this->redirectToRoute('admin_events_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/event/{categoryName}/category', name: 'similar_events_category', methods: ['GET'])]
    public function similarEventsByCategory($categoryName,Request $request, EventRepository $eventRepository,CategoryRepository $categoryRepository): Response
    {
        $category=$categoryRepository->findOneBy(["name"=>$categoryName]);
        $events=$eventRepository->findEventsWithCategory($category->getId(), 20);
        return $this->render('event/index.html.twig', [
            'events' => $events,
            'categoryName' => $category,
        ]);
    }
    #[Route('/event/{tagName}/tag', name: 'similar_events_tag', methods: ['GET'])]
    function similarEventsByTag($tagName,Request $request, EventRepository $eventRepository,TagRepository $tagRepository): Response
     {
         $tag=$tagRepository->findOneBy(["name"=>$tagName]);
         $events = $eventRepository->findEventsWithTag($tagName, 20);
         return $this->render('event/index.html.twig', [
             'events' => $events,
             'tagName' => $tagName,
         ]);
     }
    #[Route('/event/{ownerName}/list', name: 'events_same_owner', methods: ['GET'])]
    function eventsWithSameOwner($ownerName,Request $request, EventRepository $eventRepository,UserRepository $userRepository): Response
    {
        $owner=$userRepository->findOneBy(['fullName'=>$ownerName]);
        $events = $eventRepository->allEventsByOwner($owner, 20);
        return $this->render('event/index.html.twig', [
            'events' => $events,
            'owner' => $owner
        ]);
    }
    #[Route('/event/{animatorName}/animateur', name: 'events_same_animator', methods: ['GET'])]
    function eventsWithSameAnimator($animatorName, EventRepository $eventRepository,UserRepository $userRepository): Response
    {
        $animator=$userRepository->findOneBy(['fullName'=>$animatorName]);
        $events = $eventRepository->findEventsWithAnimator($animator, 20);
        return $this->render('event/index.html.twig', [
            'events' => $events,
            'animator' => $animator
        ]);
    }
    #[Route('/{event}/{type}/paiement', name: 'event.paiement', methods: ['POST'])]
    function eventPaiement(Request $request,EventRepository $eventRepository,PrixRepository $prixRepository,UserRepository $userRepository,StripeService $stripeService): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();
        $eventId = $request->get('event');
        $type = $request->get('type');
        $event=$eventRepository->find($eventId);
        if($event){
            $price=$prixRepository->findOneBy(['type'=>$type,'event'=>$event]);
            if ($price){
                $somme=$price->getSomme();
                $session->set('price_id', $price->getId());
                $mode = 'payment';

                if(!$user){
                    return $this->json([
                        'unlogged' => true,
                        'price_id' => $price->getId(),
                    ]);
                }
                $checkout = $stripeService->createCheckout($user, $price->getStripePriceId(), $mode);
                return $this->json([
                    'session_id' => $checkout->id,
                ]);
            }

        }
    }

    /**
     * @Route("/event/stripe-payment-succedeed/{priceId}", name="event.stripe.payment-succeeded", methods={"GET"})
     */
    public function paymentSucceeded(Request $request, StripeService $stripeService, CustomMailer $mailer, $priceId,UserRepository $userRepository,PrixRepository $prixRepository,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $session_id = $request->get('session_id');
        $session = $stripeService->getSession($session_id);
        $price=$prixRepository->findOneBy(["stripe_price_id"=>$priceId]);
        $event=$price->getEvent();
        if (!$session) {
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }
        $paymentIntent = $stripeService->getPaymentIntent($session->payment_intent);

        if (!$paymentIntent) return $this->redirectToRoute("app_event_show", ['id' => $price->getEvent()->getId()]);

        $charge = $stripeService->getChargeByPaymentIntent($paymentIntent->id);

        $invoice = $stripeService->getLastInvoice($user);

        $html = $this->renderView('mail/event/event_payment_succeeded.html.twig', [
            'user' => $user,
            'event' => $price->getEvent(),
            'price' => $price,
            'url_docs' => $this->getParameter('app_url') . '/account/confidential-documents',
            'url_dashboard' => $this->getParameter('app_url') . '/account',
            'receipt_url' => $charge ? $charge->receipt_url : null,
            'invoice_pdf' => $invoice ?$invoice->invoice_pdf : null,
        ]);

            $price->setPlaceRestantes($price->getPlaceRestantes()-1);
            $entityManager->persist($price);
            $entityManager->flush();

        $mailer->send(
            "Merci pour votre achat",
            $html,
            $user->getEmail()
        );
        return $this->render('event/payment-succeeded.html.twig', [
            'receipt_url' => $charge ? $charge->receipt_url : null,
            'invoice_pdf' => $invoice ? $invoice->invoice_pdf : null,
        ]);
    }
}
