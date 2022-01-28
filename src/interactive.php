<?php

$rootDir = dirname(dirname(__FILE__));

require $rootDir . '/vendor/autoload.php'; // Composer's autoloader

use App\Utils\ScraperUtil;
use Symfony\Component\Panther\Client;

$startedAt = microtime(true);

$client = Client::createFirefoxClient();

$currentPage = 1;
$lastPage = 1;

$url = "https://startupper.totalenergies.com/fr/juries/latC_MEsLWQo_ndWzJ-OJg/participations/1370/vote?order=alphabetical&scope=all";

try {
    
    $client->request('GET', $url);

    $client->getCrawler()->filter('#teal-consent-prompt-submit')->click(); //Close cookie modal

    $crawler = $client->waitForVisibility('.right-project');

    $paginationSelector = '.media-left.media-middle.navigation-project > .media-middle';

    $progress = $crawler->filter($paginationSelector)->eq(1)->text();//Ex. 1/60
    $lastPage = explode('/', $progress)[1];

    $csvFile = fopen($rootDir . '/data/startups.csv', 'w');
    fputcsv($csvFile, ['Projet', 'Image', 'Promoteur', 'Description']); //CSV header

    print "Scrapping page : $currentPage  \n";

    $data = ScraperUtil::extractData($crawler);
    
    fputcsv($csvFile, $data);
    
    while ($currentPage <= $lastPage) {       

        $currentPage++;
 
        // Next page
        $crawler->filter('.right-project')->first()->click();
        sleep(10); //Wait for ajax/XHR request
        $crawler = $client->refreshCrawler();
        sleep(5);
        print "Scrapping page : $currentPage  \n";

        $data = ScraperUtil::extractData($crawler);
    
        fputcsv($csvFile, $data);
    }
    
    fclose($csvFile);

} catch (Exception $e) {
    echo $e->getMessage() . "\n";
} finally {
    $client->quit();
}

$endedAt = microtime(true);

print "Time elapsed : " . ($endedAt - $startedAt) . " seconds \n";