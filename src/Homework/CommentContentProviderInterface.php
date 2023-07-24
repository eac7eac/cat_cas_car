<?php

namespace App\Homework;

interface CommentContentProviderInterface
{
    /**
     * @param string|null $word
     * @param int $wordsCount
     *
     * @return string
     */
    public function get(string $word = null, int $wordsCount = 0): string;
}