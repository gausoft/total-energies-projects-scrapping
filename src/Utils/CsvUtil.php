<?php

namespace App\Utils;

define('ROOT_DIR', dirname(dirname(dirname(__FILE__))));

class CsvUtil
{
    private const HEADER = ['Projet', 'Image', 'Promoteur', 'Description', 'Pays'];

    public static function addCsvHeader(string $filename = 'data.csv', array $header = self::HEADER)
    {
        $csvFile = fopen(ROOT_DIR . "/data/$filename", 'w');
        fputcsv($csvFile, $header);
        fclose($csvFile);
    }

    public static function saveDataToCsvFile(array $pageData, string $filename = 'data.csv')
    {
        $csvFile = fopen(ROOT_DIR . "/data/$filename", 'a+');

        foreach ($pageData as $data) {
            foreach ($data as $line) {
                fputcsv($csvFile, $line);
            }
        }

        fclose($csvFile);
    }
}
