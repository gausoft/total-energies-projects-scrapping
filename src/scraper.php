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
    print "**************** $country Data : ***************************\n";
    $data = extractProjectsByCountry($key, $country);
    CsvUtil::saveDataToCsvFile($data, $filename);
    print "**************** $country END : ****************************\n";
}

function extractProjectsByCountry(string $key, $country)
{
    $client = Client::createFirefoxClient();

    $currentPage = 1;
    $lastPage = 2;
    $scrappedData = [];

    $queryString = 'query=&order=alphabetical&scope=all';
    $url = "https://startupper.totalenergies.com/fr/juries/$key?$queryString";

    try {

        $client->request('GET', $url . "&p=$currentPage");

        $crawler = $client->waitForVisibility('#pagination-container', 60);

        echo $crawler->filter('.slogan-challenge > h1')->text() . "\n";

        $lastPage = ScraperUtil::getLastPageNumber($crawler);

        while ($currentPage <= $lastPage) {
            print "Scrapping page : $currentPage  \n";

            $scrappedData[] = ScraperUtil::extractData($crawler, $country);

            $currentPage++;

            $client->request('GET', $url . "&p=$currentPage");

            $crawler = $client->waitForVisibility('#pagination-container', 60);
        }
        
        print "$country statartups count : " . countCountryStartups($scrappedData) . "\n"; 
    } catch (\Exception $e) {
        echo "Exception : " . $e->getMessage() . "\n";
        return [];
    } finally {
        $client->quit();
        return $scrappedData;
    }
}

function countCountryStartups(array $startupsData)
{
    $total = 0;

    foreach ($startupsData as $data) {
        foreach ($data as $line) {
            $total++;
        }
    }

    return $total;
}