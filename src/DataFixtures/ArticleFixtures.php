<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use App\Homework\ArticleContentProvider;
use App\Service\FileUploader;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

class ArticleFixtures extends BaseFixtures implements DependentFixtureInterface
{
    private static $articleTitles = [
        'Есть ли жизнь после девятой жизни?',
        'Когда в машинах поставят лоток?',
        'В погоне за красной точкой',
        'В чем смысл жизни сосисок',
        'Вы рыбов продаете?',
        'Наташ, мы все уронили!',
    ];

    private static $articleImages = [
        'car1.jpg',
        'car2.jpg',
        'car3.jpeg',
    ];

    private static $wordsArticleContentProvider = [
        'ПРИВЕТ!',
        'balalaika',
        'вариативность',
        'коммуникабельность',
        'John Smith',
        'bananas',
        'Excepteur',
        'symfony',
        'qwerty@mail.com',
        'Акрон',
    ];

    /**
     * @var ArticleContentProvider
     */
    private $articleContentProvider;
    /**
     * @var FileUploader
     */
    private $articleFileUploader;

    public function __construct(ArticleContentProvider $articleContentProvider, FileUploader $articleFileUploader)
    {
        $this->articleContentProvider = $articleContentProvider;
        $this->articleFileUploader = $articleFileUploader;
    }

    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(Article::class, 25, function (Article $article) use ($manager) {
            $article
                ->setTitle($this->faker->randomElement(self::$articleTitles))
                ->setBody('Lorem Lorem ipsum **красная точка** dolor sit amet, consectetur adipiscing elit, sed
do eiusmod tempor incididunt [Сметанка](/) ut labore et dolore magna aliqua.
' . ($this->faker->boolean(70) ?
                        $this->articleContentProvider->get($this->faker->numberBetween(2, 10), $this->faker->randomElement(self::$wordsArticleContentProvider), $this->faker->numberBetween(5, 10)) :
                        $this->articleContentProvider->get($this->faker->numberBetween(2, 10)))
                );
            if ($this->faker->boolean(60)) {
                $article->setPublishedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            }

            $fileName = $this->faker->randomElement(self::$articleImages);

            $article
                ->setAuthor($this->getRandomReference(User::class))
                ->setDescription('Краткое описание')
                ->setLikeCount($this->faker->numberBetween(0, 10))
                ->setVoteCount($this->faker->numberBetween(0, 200))
                ->setImageFilename(
                    $this->articleFileUploader->uploadFile(new File(dirname(dirname(__DIR__)) . '/public/images/' . $fileName, $fileName))
                )
            ;

            /** @var Tag[] $tags */
            $tags = [];
            for ($i = 0; $i < 5; $i++) {
                $tags[] = $this->getRandomReference(Tag::class);
            }

            foreach ($tags as $tag) {
                $article->addTag($tag);
            }

        });
    }

    public function getDependencies()
    {
        return [
            TagFixtures::class,
            UserFixtures::class,
        ];
    }

}
