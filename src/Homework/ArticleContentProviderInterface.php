<?php

namespace App\Homework;

interface ArticleContentProviderInterface
{
    /**
     * @param int $paragraphs
     * @param string|null $word
     * @param int $wordsCount
     *
     * @return string
     */
    public function get(int $paragraphs, string $word = null, int $wordsCount = 0);
}