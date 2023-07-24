<?php

namespace App\Controller;

use App\Entity\Article;
use App\Homework\ArticleContentProvider;
use App\Homework\ArticleProvider;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Service\SlackClient;
use Nexy\Slack\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(
        Environment $twig,
        ArticleProvider $articleProvider,
        $slackUrl,
        ArticleRepository $repository,
        CommentRepository $commentRepository
    )
    {
        $articles = $repository->findLatestPublished();
//        $comments = $commentRepository->numberLastComments(3);

        return $this->render("articles/homepage.html.twig", [
            dump($articleProvider->aricles()),
            'articles' => $articles,
//            'comments' => $comments
        ]);
    }

    /**
     * @Route("/articles/{slug}", name="app_article_show")
     */
    public function show(
        Article $article,
        ArticleProvider $articleProvider,
        SlackClient $slackClient,
        ArticleContentProvider $articleContentProvider
    )
    {
        if ($article->getSlug() == 'slack') {
            $slackClient->send('Привет, это важное уведомление!', '');
        }

        $wordsArticleContentProvider = ['ПРИВЕТ!', 'balalaika', 'вариативность', 'коммуникабельность', 'John Smith', 'bananas', 'Excepteur', 'symfony', 'qwerty@mail.com', 'Акрон'];

        if (rand(1, 3) != 1) {
            $articleContentProvider = $articleContentProvider->get(5, $wordsArticleContentProvider[random_int(0, 9)], rand(5, 10));
        } else {
            $articleContentProvider = $articleContentProvider->get(5);
        }

        return $this->render('articles/show.html.twig', [
            'article' => $article,
            dump($articleProvider->article()),
            'articleContentProvider' => $articleContentProvider,
        ]);
    }
}