<?php

namespace App\Service;

use App\Entity\User;
use Closure;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     * @return void
     */
    public function sendWelcomeMail(User $user): void
    {
        $this->send('email/welcome.html.twig', 'Добро пожаловать на Spill-Coffee-On-The-Keyboard', $user);
    }

    /**
     * @param User $user
     * @param array $articles
     * @return void
     */
    public function sendWeeklyNewsletter(User $user, array $articles): void
    {
        $this->send(
            'email/newsletter.html.twig',
            'Еженедельная рассылка Spill-Coffee-On-The-Keyboard',
            $user,
            function (TemplatedEmail $email) use ($articles) {
                $email
                    ->context([
                        'articles' => $articles,
                    ])
                    ->attach('Опубликовано статей на сайте: ' . count($articles), 'report_' . date('Y-m-d') . '.txt')
                ;
            }
        );
    }

    public function sendArticleCreatedLetter(User $user, object $article): void
    {
        $this->send(
            'email/article_created.html.twig',
            'Оповещение о создании статьи на Spill-Coffee-On-The-Keyboard',
            $user,
            function (TemplatedEmail $email) use ($article) {
                $email->context([
                    'article' => $article,
                    '']);
            }
        );
    }

    /**
     * @param string $template
     * @param string $subject
     * @param User $user
     * @param Closure|null $callback
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    private function send(string $template, string $subject, User $user, Closure $callback = null): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@symfony.skillbox', 'Spill-Coffee-On-The-Keyboard'))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->htmlTemplate($template)
            ->subject($subject)
        ;
        
        if ($callback) {
            $callback($email);
        }

        $this->mailer->send($email);
    }
}
