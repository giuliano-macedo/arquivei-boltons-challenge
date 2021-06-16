<?php
require_once 'vendor/autoload.php';
require_once 'core/Scraper.php';
require_once 'core/Database.php';

$db = new Database();
$scraper = new Scraper();
$scraper->scrape(function ($nfes) use (&$db) {
    $db->replaceNfes($nfes);
    echo "Successfuly inserted/replaced " . count($nfes) . " nfes!\n";
});
