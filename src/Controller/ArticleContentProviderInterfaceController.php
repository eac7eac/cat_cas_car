<?php

namespace App\Controller;

use App\Homework\ArticleContentProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleContentProviderInterfaceController extends AbstractController
{
    /**
     * @Route("/api/v1/article_content", name="app_article_content")
     */
    public function articleContent(Request $request, ArticleContentProviderInterface $articleContentProvider)
    {
        $paragraphs = (int)$request->get('paragraphs');
        $word       = $request->get('word');
        $wordsCount = (int)$request->get('wordsCount');

        $articleContent = $articleContentProvider->get($paragraphs, $word, $wordsCount);

        return $this->render('articles/content.html.twig', [
            'articleContent' => $articleContent
        ]);
    }
}