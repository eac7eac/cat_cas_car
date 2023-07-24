<?php

namespace App\Homework;

class PasteWords
{
    public function paste(string $text, string $word, int $wordsCount = 1): string
    {
        $contentExplode = explode(' ', $text);
        $randomIndexes = array_rand($contentExplode, $wordsCount);

        foreach ($randomIndexes as $randomIndex) {
            array_splice($contentExplode, $randomIndex, 0, $word);
        }

        $textsContent = implode(' ', $contentExplode);

        return $textsContent;
    }
}