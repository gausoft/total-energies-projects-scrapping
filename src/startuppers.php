<?php

$rootDir = dirname(dirname(__FILE__));

require $rootDir . '/vendor/autoload.php'; // Composer's autoloader

use Symfony\Component\Panther\Client;

$client = Client::createFirefoxClient();

$currentPage = 1;
$lastPage = 2;

$url = "https://startupper.totalenergies.com/fr/juries/latC_MEsLWQo_ndWzJ-OJg?query=&order=alphabetical&scope=all";

try {

    $client->request('GET', $url . "&p=$currentPage");

    $crawler = $client->waitForVisibility('#pagination-container');

    echo $crawler->filter('.slogan-challenge > h1')->text() . "\n";

    $lastPage = getLastPageNumber($crawler);

    $csvFile = fopen($rootDir . '/data/startuppers.csv', 'w');
    fputcsv($csvFile, ['Projet', 'Image', 'Promoteur', 'Description']); //CSV header

    while ($currentPage <= $lastPage) {
        print "Scrapping page : $currentPage  \n";

        $crawler->filter('.super-card')->each(function ($node) use ($csvFile) {
            $nodeStyle = $node->filter('.bg')->attr('style');

            $urlPattern = "(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?";
            preg_match("/$urlPattern/i", $nodeStyle, $match); //Extract image url from element style

            if (empty($match)) {
                preg_match('/team-avatar-\d+.jpg/', $nodeStyle, $match);
                $logoUrl = "https://startupper.totalenergies.com/" . $match[0];
            } else {
                $logoUrl = $match[0];
            }

            $promoter = $node->filter('h6')->text();
            $projectName = $node->filter('h5')->text();
            $description = $node->filter('p')->text();

            fputcsv($csvFile, [$projectName, $logoUrl, $promoter, $description]);
        });

        $currentPage++;

        $client->request('GET', $url . "&p=$currentPage");

        $crawler = $client->waitForVisibility('#pagination-container');
    }

    fclose($csvFile);

    echo "Scrapping finished successfully! \n";
} catch (Exception $e) {
    echo $e->getMessage();
} finally {
    $client->quit();
}

function getLastPageNumber($crawler)
{
    $total = $crawler->filter('ul.pagination > li')->count();

    $beforeLastLi = $crawler->filter('ul.pagination > li')->eq($total - 2);

    return (int) $beforeLastLi->text();
}
