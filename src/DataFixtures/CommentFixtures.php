<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Homework\CommentContentProvider;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends BaseFixtures implements DependentFixtureInterface
{
    /**
     * @var CommentContentProvider
     */
    private $commentContentProvider;

    public function __construct(CommentContentProvider $commentContentProvider)
    {
        $this->commentContentProvider = $commentContentProvider;
    }

    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(Comment::class, 100, function (Comment $comment) use ($manager) {
            $comment
                ->setAuthorName($this->faker->name)
                ->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 day'))
                ->setArticle($this->getRandomReference(Article::class))
            ;

            if (($this->faker->boolean(70))) {
                $comment->setContent($this->commentContentProvider->get('ПРИВЕТИЩЕ!', $this->faker->numberBetween(2, 5)));
            } else {
                $comment->setContent($this->commentContentProvider->get());
            }

            if ($this->faker->boolean) {
                $comment->setDeletedAt($this->faker->dateTimeThisMonth);
            }
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ArticleFixtures::class,
        ];
    }
}
