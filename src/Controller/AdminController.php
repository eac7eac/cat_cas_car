<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(
        CommentRepository $commentRepository,
        TagRepository $tagRepository,
        ArticleRepository $articleRepository
    ): Response
    {
        $comments = $commentRepository->findAll();
        $tags = $tagRepository->findAll();
        $articles = $articleRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'comments' => $comments,
            'tags' => $tags,
            'articles' => $articles,
        ]);
    }
}
