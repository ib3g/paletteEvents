<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Repository\CommentRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends BaseController
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


    #[Route('/admin/comments/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        $event = $comment->getEvent();
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $comment->setDeletedAt(new \DateTimeImmutable());
            $commentRepository->save($comment, true);
            $this->addSuccessFlash('Le commentaire a bien été supprimé');
        }

        return $this->redirectToRoute('admin_event_comment', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/comments/{id}/republier', name: 'app_comment_republier', methods: ['GET'])]
    public function republier(Comment $comment, CommentRepository $commentRepository): Response
    {
        $event = $comment->getEvent();
        $comment->setDeletedAt(null);
        $commentRepository->save($comment, true);

        $this->addSuccessFlash('Le commentaire a bien été republié');

        return $this->redirectToRoute('admin_event_comment', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
    }

}