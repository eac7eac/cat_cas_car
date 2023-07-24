<?php

namespace App\Controller;

use App\Entity\Article;
use App\Service\ApiLogger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/v1/article_content", name="app_api")
     */
    public function index(
        Security $security,
        ApiLogger $apiLogger
    ): Response
    {

        $userRoles = $security->getUser()->getRoles();

        if ((in_array('ROLE_API', $userRoles)) == false) {
            $apiLogger->apiWarning();
        }

        $this->denyAccessUnlessGranted('ROLE_API');

        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/v1/articles/{id}", name="app_admin_articles_edit")
     * @IsGranted("API_MANAGE", subject="article")
     */
    public function articleInfo(Article $article)
    {
        return $this->json([
            'title' => $article->getTitle(),
            'slug' => $article->getSlug(),
            'description' => $article->getDescription(),
            'body' => $article->getBody(),
            'keywords' => $article->getKeywords(),
            'vote_count' => $article->getVoteCount(),
            'like_count' => $article->getLikeCount(),
            'image_filename' => $article->getImageFilename(),
            'published_at' => $article->getPublishedAt(),
            'created_at' => $article->getCreatedAt(),
            'updated_at' => $article->getUpdatedAt(),
            ]);
    }
}
