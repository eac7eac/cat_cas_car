<?php

namespace App\Homework;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleProvider extends AbstractController
{
    public $articlesArray = [
        "0" => [
            'title' => 'Wobble revolutionary like a futile ferengi.',
            'slug' => 'article00001',
            'image' => '1',],
        "1" => [
            'title' => 'Mankind at the homeworld was the rumour of beauty, commanded to a brave klingon.',
            'slug' => 'article00002',
            'image' => '2'],
        "2" => [
            'title' => 'Pattern at the planet was the metamorphosis of wind, captured to a ugly kahless.',
            'slug' => 'article00003',
            'image' => '3'
        ]];

    public function aricles()
    {
        return $this->json($this->articlesArray);
    }

    public function article()
    {
        return $this->json($this->articlesArray[rand(0, 2)]);
    }
}