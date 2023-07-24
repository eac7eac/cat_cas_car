<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserDeactivateCommand extends Command
{
    protected static $defaultName = 'app:user:deactivate';
    protected static $defaultDescription = 'Деактивация/активация пользователя';
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('id', InputArgument::REQUIRED, 'ID пользователя')
            ->addOption('reverse', null, InputOption::VALUE_NONE, 'Опция активации пользователя')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $userID = (int)$input->getArgument('id');
        $userActivate = $input->getOption('reverse');

        $userFind = $this->userRepository->findOneBy(['id' => $userID]);

        if (! $userActivate && $userFind != NULL) {
            $userFind->setIsActive(false);
            $this->em->persist($userFind);
        } elseif ($userActivate && $userFind != NULL) {
            $userFind->setIsActive(true);
            $this->em->persist($userFind);
        } else {
            echo 'Пользователя с таким ID не существует';
        }

        $this->em->flush();

        return 0;
    }
}
