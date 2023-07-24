<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class AdminStatisticReportCommand extends Command
{
    protected static $defaultName = 'app:admin-statistic-report';
    protected static $defaultDescription = 'Отчёт администратору';

    private const DELIMITER = ';';

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(UserRepository $userRepository, ArticleRepository $articleRepository, MailerInterface $mailer)
    {
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
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::REQUIRED, 'Email получателя')
            ->addOption('date-from', null, InputOption::VALUE_NONE, 'Дата начала периода')
            ->addOption('date-to', null, InputOption::VALUE_NONE, 'Дата окончания периода')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $emailToSend = $input->getArgument('email');
        $dateFrom = $input->getOption('date-from') ? $input->getOption('date-from') : '-1 week';
        $dateTo = $input->getOption('date-to') ? $input->getOption('date-to') : 'today';

        /** @var User[] $usersAdmin */
        $usersAdmin = $this->userRepository->findUsersByRole('ROLE_ADMIN');
        $usersCount = $this->userRepository->usersCount();

        $articlesTotalCountForPeriod = $this->articleRepository->totalCountForPeriod($dateFrom, $dateTo);
        $articlesPublishedCountForPeriod = $this->articleRepository->publishedCountForPeriod($dateFrom, $dateTo);

        $fp = fopen('report-to-administrator.csv', 'w');

        $header = implode(self::DELIMITER, ['Период', 'Всего пользователей', 'Статей создано за период', 'Статей опубликовано за период']);
        $period = date('Y-m-d', strtotime($dateFrom)) . ' - ' . date('Y-m-d', strtotime($dateTo));
        $reportData = implode(self::DELIMITER, [$period, $usersCount, $articlesTotalCountForPeriod, $articlesPublishedCountForPeriod]);
        $csv = $header . PHP_EOL . $reportData;
        fwrite($fp, $csv);

        fclose($fp);

        $email = new TemplatedEmail();
        foreach ($usersAdmin as $userAdmin) {
            $email
                ->from(new Address($userAdmin['u_email'], $userAdmin['u_firstName']))
                ->to(new Address($emailToSend))
                ->text('Отчет от Администратора')
                ->attach(file_get_contents('report-to-administrator.csv'), 'admin-report.csv');

            $this->mailer->send($email);
        }

        return 0;
    }
}
