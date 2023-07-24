<?php

namespace App\Homework;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleWordsFilter extends AbstractController
{
    public function filter(string $string, array $words = [])
    {
        $stringExplode = explode(' ', $string);
        $resultStringExplode = [];

        foreach ($stringExplode as $word) {
            $check = false;
            foreach ($words as $wordFiltered) {
                if ( !(strpos( mb_strtolower($word), mb_strtolower($wordFiltered)) === false) ) {
                    $check = true;
                }
            }
            if ( !$check ) {
                $resultStringExplode[] = $word;
            }
        }

        return implode(' ', $resultStringExplode);
    }
}