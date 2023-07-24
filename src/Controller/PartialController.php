<?php

namespace App\Controller;

use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PartialController extends AbstractController
{
    /**
     * @var CommentService
     */
    private $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * @return Response
     */
    public function lastComments(): Response
    {
        $comments = $this->commentService->getLastComments();

        return $this->render('partial/last_comments.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function findLastComments()
    {
        $comments = $this->commentService->findLastComments(3);

        return $this->json($comments);
    }
}
