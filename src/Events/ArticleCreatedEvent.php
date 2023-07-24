<?php

namespace App\Events;

use App\Entity\Article;
use Symfony\Contracts\EventDispatcher\Event;

class ArticleCreatedEvent extends Event
{

    /**
     * @var Article
     */
    private $article;

    public function __construct(Article $article)
    {

        $this->article = $article;
    }

    /**
     * @return Article
     */
    public function getArticle(): Article
    {
        return $this->article;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->article->getTitle();
    }

    /**
     * @return object
     */
    public function getAuthor(): object
    {
        return $this->article->getAuthor();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->article->getDescription();
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->article->getSlug();
    }
}