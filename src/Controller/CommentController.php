<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/add-comment', name: 'add-comment', methods: ['POST'])]

    public function addComment(Request $request)
    {
        $user=$this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login');
        }
        // get form data
        $message = $request->request->get('message');
        $event_id = $request->request->get('event_id');
        $note = $request->request->get('note');
        $event=$this->getDoctrine()->getRepository(Event::class)->find($event_id);

        $comment = new Comment();
        $comment->setMessage($message);
        $comment->setNote($note);
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setUser($user);
        $comment->setEvent($event);
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();
        return $this->redirectToRoute('app_event_show', ['id' => $event_id]);
    }
}