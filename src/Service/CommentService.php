<?php

namespace App\Service;

use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CommentService
{
    /**
     * @var CommentRepository
     */
    private $commentRepository;
    /**
     * @var HttpKernelInterface
     */
    private $httpKernel;

    public function __construct(CommentRepository $commentRepository, HttpKernelInterface $httpKernel)
    {
        $this->commentRepository = $commentRepository;
        $this->httpKernel = $httpKernel;
    }

    public function getLastComments()
    {
        $request = new Request();

        $request->attributes->set('_controller', 'App\\Controller\\PartialController::findLastComments');

        /** @var JsonResponse $response */
        $response = $this->httpKernel->handle($request, HttpKernelInterface::SUB_REQUEST);

        $comments = [];
        if ($content = $response->getContent()){
            $comments = json_decode($content, true);
        }

        return $comments;
    }

    /**
     * @param int $numberComments
     * @return array
     */
    public function findLastComments(int $numberComments): array
    {
        $comments = $this->commentRepository->numberLastComments($numberComments);

        $commentsResult = [];

        foreach ($comments as $comment) {
            $commentsResult[] = ['comments' => $comment->getContent(), 'author' => $comment->getAuthorName()];
        }

        return $commentsResult;
    }
}