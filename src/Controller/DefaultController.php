<?php

/*
 * This file is part of the PaletteEvent project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Developed by Monarkit
 *
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\Newsletter;
use App\Repository\ContactRepository;
use App\Repository\EventRepository;
use App\Repository\NewsletterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EventRepository $eventRepository){
        // call findLastEvents from EventRepository
        $events = $eventRepository->findLastEvents(4);
        $categories = $this->getDoctrine()->getRepository(Category::class)->findCategories(6);

        return $this->render('home.html.twig',[
            'events' => $events,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(){
        return $this->render('admin.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(){
        return $this->render('contact/form.html.twig');
    }
    /**
     * @Route("/saveContact", name="saveContact",methods={"POST"})
     */
    public function saveContact(Request $request,ContactRepository $contactRepository){
        $contact = new Contact();
        $author=$this->getUser();
        if(!$author){
            $author = $contactRepository->findOneBy(['email' => $request->request->get('email')]);
        }
        $contact->setAuthor($author);
        $contact->setSubject($request->request->get('subject'));
        $contact->setMessage($request->request->get('message'));
        $contact->setCreatedAt(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($contact);
        $em->flush();
        // add flash message
        $this->addFlash('success', 'Votre message a bien été envoyé');
        return $this->redirectToRoute('contact');
    }


    /**
     * @Route("/naewsletter", name="naewsletter",methods={"POST"})
     */
    public function naewsletter(Request $request,NewsletterRepository $newsletterRepository){
        $emailExist = $newsletterRepository->findOneBy(['email' => $request->request->get('email')]);
        if($emailExist){
            $this->addFlash('danger', 'Vous êtes déjà inscrit à la newsletter');
            return $this->redirectToRoute('home');
        }else {
            $newsletter = new Newsletter();
            $newsletter->setEmail($request->request->get('email'));
            $newsletter->setStatus(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsletter);
            $em->flush();
            $this->addFlash('success', 'Vous êtes bien inscrit à la newsletter');
            return $this->redirectToRoute('home');
        }
    }
}
