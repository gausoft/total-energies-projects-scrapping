<?php

use Symfony\Component\Panther\Client;

require __DIR__ . '/vendor/autoload.php'; // Composer's autoloader

$client = Client::createFirefoxClient();

$currentPage = 1;
$lastPage = 2;

$url = "https://startupper.totalenergies.com/en/juries/latC_MEsLWQo_ndWzJ-OJg?query=&order=alphabetical&scope=all";

$csvFile = fopen('startuppers.csv', 'w');

fputcsv($csvFile, ['Projet', 'Image', 'Initiateur', 'Description']); //header

try {

    $client->request('GET', $url . "&p=$currentPage"); // Yes, this website is 100% written in JavaScript

    $crawler = $client->waitForVisibility('#pagination-container');

    echo $crawler->filter('.slogan-challenge > h1')->text() . "\n";

    if ($currentPage == 1) {
        $lastPage = getLastPageNumber($crawler);
    }

    while ($currentPage <= $lastPage) {
        print "Scrapping page : $currentPage  \n";

        $crawler->filter('.super-card')->each(function ($node) use ($csvFile) {
            $nodeStyle = $node->filter('.bg')->attr('style');

            preg_match('/(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', $nodeStyle, $match);

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

    echo "Scripping finished successfully!";
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
