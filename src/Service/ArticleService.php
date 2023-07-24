<?php

namespace App\Service;

use App\Entity\Article;
use App\Events\ArticleCreatedEvent;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArticleService
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EventDispatcherInterface $dispatcher, UserRepository $userRepository)
    {
        $this->dispatcher = $dispatcher;
        $this->userRepository = $userRepository;
    }

    public function articleCreate(Article $article)
    {
        $usersRoleAdmin = $this->userRepository->findUsersByRole('ROLE_ADMIN');
        $articleAuthor = $article->getAuthor();

        foreach ($usersRoleAdmin as $userRoleAdmin) {
            if (!array_search($articleAuthor->getEmail(), $userRoleAdmin)) {
                return;
            }
        }

        $event = new ArticleCreatedEvent($article);
        $this->dispatcher->dispatch($event);
    }
}