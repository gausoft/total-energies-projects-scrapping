<?php

$rootDir = dirname(dirname(__FILE__));

require $rootDir . '/vendor/autoload.php'; // Composer's autoloader

use App\Utils\CsvUtil;
use Symfony\Component\Panther\Client;
use App\Utils\ScraperUtil;

$client = Client::createFirefoxClient();

$currentPage = 1;
$lastPage = 1;
$data = [];

$url = "https://startupper.totalenergies.com/fr/juries/latC_MEsLWQo_ndWzJ-OJg?query=&order=alphabetical&scope=all";

try {

    $client->request('GET', $url . "&p=$currentPage");

    $crawler = $client->waitForVisibility('#pagination-container');

    echo $crawler->filter('.slogan-challenge > h1')->text() . "\n";

    $lastPage = ScraperUtil::getLastPageNumber($crawler);

    while ($currentPage <= $lastPage) {
        print "Scrapping page : $currentPage  \n";

        $data[] = ScraperUtil::extractData($crawler, 'Togo');

        $currentPage++;

        $client->request('GET', $url . "&p=$currentPage");

        $crawler = $client->waitForVisibility('#pagination-container');
    }

    CsvUtil::saveDataToCsvFile($data, 'startuppers.csv');

    echo "Scrapping finished successfully! \n";
    echo "Total of : " . count($data) . " lines \n";
} catch (Exception $e) {
    echo $e->getMessage();
} finally {
    $client->quit();
}