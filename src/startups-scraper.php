<?php

namespace App;

$rootDir = dirname(dirname(__FILE__));

require $rootDir . '/vendor/autoload.php'; // Composer's autoloader

use Symfony\Component\Panther\Client;
use App\Utils\CsvUtil;
use App\Utils\ScraperUtil;

$countries = [
    'latC_MEsLWQo_ndWzJ-OJg' => 'Togo',
    'zpoPYHc4ilSbXdrC0UOYNA' => 'Sénégal',
    '2Z6qAlbiMPxEwtQnOcojog' => 'Nigeria',
    'auGDr4zQUEDlZApzNf6VVw' => 'Guinée',
    'Dbpf2fD3SDQQ_pr-vQYbAg' => 'Ghana',
    '5Cg28wa4hgcODBcDk6mGrg' => 'Côte d\'Ivoire'
];

$filename = 'west-african-strartups.csv';

CsvUtil::addCsvHeader($filename);

foreach ($countries as $key => $country) {
    extractProjectsByCountry($key, $country, $filename);
}

function extractProjectsByCountry(string $key, string $country, string $filename)
{
    $client = Client::createFirefoxClient();

    $currentPage = 1;
    $lastPage = 1;
    $total = 0;
    $data = [];

    $queryString = 'query=&order=alphabetical&scope=all';
    $url = "https://startupper.totalenergies.com/fr/juries/$key?$queryString";

    try {
        $client->request('GET', $url . "&p=$currentPage");

        $crawler = $client->waitForVisibility('#pagination-container');

        echo $crawler->filter('.slogan-challenge > h1')->text() . "\n";

        $lastPage = ScraperUtil::getLastPageNumber($crawler);

        while ($currentPage <= $lastPage) {
            print "Scrapping page : $currentPage  \n";

            $data = ScraperUtil::extractData($crawler, $country);

            CsvUtil::saveDataToCsvFile($data, $filename);

            $currentPage++;

            $client->request('GET', $url . "&p=$currentPage");

            $crawler = $client->waitForVisibility('#pagination-container');

            $total += count($data);
        }

        print "$country statartups count : $total\n\n";
    } catch (\Exception $e) {
        echo $url . "\n";
        echo "Exception : " . $e->getMessage() . "\n";
    } finally {
        $client->quit();
    }
}