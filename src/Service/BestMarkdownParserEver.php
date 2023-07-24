<?php

namespace App\Service;

use Demontpx\ParsedownBundle\Parsedown;

class BestMarkdownParserEver extends Parsedown
{
    public function text($text)
    {
        return 'Я лучший парсер <b>markdown</b>';
    }
}