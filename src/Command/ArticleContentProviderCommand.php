<?php

namespace App\Command;

use App\Homework\ArticleContentProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArticleContentProviderCommand extends Command
{
    protected static $defaultName = 'app:article:content_provider';
    protected static $defaultDescription = 'Вывод результата работы сервиса `App\Homework\ArticleContentProviderInterface`';

    private $articleContentProvider;

    /**
     * ArticleContentProviderCommand constructor
     * @param ArticleContentProviderInterface $articleContentProvider
     */
    public function __construct(ArticleContentProviderInterface $articleContentProvider)
    {
        $this->articleContentProvider = $articleContentProvider;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('paragraphs', InputArgument::REQUIRED, 'Количество параграфов')
            ->addArgument('word', InputArgument::OPTIONAL, 'Вставляемое слово')
            ->addArgument('wordsCount', InputArgument::OPTIONAL, 'Количество вставляемых слов')
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
        $paragraphs = (int)$input->getArgument('paragraphs');
        $word = $input->getArgument('word');
        $wordsCount = (int)$input->getArgument('wordsCount');

        $output->writeln($this->articleContentProvider->get($paragraphs, $word, $wordsCount));

        return 0;
    }
}
