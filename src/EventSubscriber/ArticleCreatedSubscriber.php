<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Events\ArticleCreatedEvent;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticleCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Mailer $mailer, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    /**
     * @param ArticleCreatedEvent $event
     * @return void
     */
    public function onArticleCreated(ArticleCreatedEvent $event): void
    {
        $usersAdminRole = $this->userRepository->findUsersByRole('ROLE_ADMIN');

        foreach ($usersAdminRole as $userAdminRole) {

            /** @var User[] $userAdmin */
            $userAdmin = $this->userRepository->findBy(['id' => $userAdminRole['u_id']]);

            $this->mailer->sendArticleCreatedLetter($userAdmin[0], $event);
        }
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            ArticleCreatedEvent::class => 'onArticleCreated'
        ];
    }
}