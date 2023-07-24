<?php

namespace App\Controller\Homework;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleVoteController extends AbstractController
{
    /**
     * @Route("/articles/{slug}/vote/{type<up|down>}", methods={"POST"}, name="app_article_vote")
     */
    public function vote(Article $article, $type, EntityManagerInterface $em)
    {
        if ($type == 'up'){
            $article->voteUp();
        } else {
            $article->voteDown();
        }

        $em->flush();

        return $this->json(['votes' => $article->getVoteCount()]);
    }
}
