<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends BaseFixtures
{
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(Tag::class, 50, function (Tag $tag) {
            $tag
                ->setName($this->faker->realText(15))
                ->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 day'))
            ;

            if ($this->faker->boolean) {
                $tag->setDeletedAt($this->faker->dateTimeThisMonth);
            }
        });

        $manager->flush();
    }
}
