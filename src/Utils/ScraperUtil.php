<?php

namespace App\Utils;

use Symfony\Component\Panther\DomCrawler\Crawler;

class ScraperUtil
{
    public static function extractData(Crawler $crawler, $country = '')
    {
        $currentPageData = [];

        $crawler->filter('.super-card')->each(function ($card) use ($country, &$currentPageData) {
            $promoter     = $card->filter('h6')->text();
            $projectName  = $card->filter('h5')->text();
            $description  = $card->filter('p')->text();
            $imageStyle   = $card->filter('.thumbnail > .bg')->attr('style');
            $imageUrl     = RegexUtil::extractUrl($imageStyle);

            $currentPageData[] = [$projectName, $imageUrl, $promoter, $description, $country];
        });

        return $currentPageData;
    }

    public static function getLastPageNumber($crawler)
    {
        $total = $crawler->filter('ul.pagination > li')->count();

        $beforeLastLi = $crawler->filter('ul.pagination > li')->eq($total - 2);

        return (int) $beforeLastLi->text();
    }
}
