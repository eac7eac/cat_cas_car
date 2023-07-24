<?php

namespace App\Homework;

use Faker\Factory;

class CommentContentProvider implements CommentContentProviderInterface
{
    public function get(string $word = null, int $wordsCount = 0): string
    {
        $faker = Factory::create();

        if (! $word) {
            return $faker->paragraph;
        } else {
            $textContent = new PasteWords();

            return $textContent->paste($faker->paragraph, $word, $wordsCount);
        }
    }
}