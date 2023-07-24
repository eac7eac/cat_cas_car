<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WeeklyNewsletterCommand extends Command
{
    protected static $defaultName = 'app:weekly-newsletter';
    protected static $defaultDescription = 'Еженедельная рассылка статей';

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        UserRepository $userRepository,
        ArticleRepository $articleRepository,
        Mailer $mailer
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
        $this->mailer = $mailer;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var User[] $users */
        $users = $this->userRepository->findBy(['subscribeToNewsletter' => true]);

        /** @var Article[] $articles */
        $articles = $this->articleRepository->findAllPublishedLastWeek();

        if (!count($articles)) {
            $io->warning('За последнюю неделю никто не публиковал статьи');
            return 0;
        }

        $io->progressStart(count($users));

        foreach ($users as $user) {
            $this->mailer->sendWeeklyNewsletter($user, $articles);

            $io->progressAdvance();
        }

        $io->progressFinish();

        return 0;
    }
}
