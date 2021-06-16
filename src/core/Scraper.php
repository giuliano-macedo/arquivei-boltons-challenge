<?php
require_once 'Parser.php';
require_once dirname(__FILE__) . '/../models/Nfe.php';
final class Scraper
{
    private const BASE_URL = "https://sandbox-api.arquivei.com.br";
    private const LIMIT = 50;
    function __construct()
    {
        $this->apiId = $_ENV["API_ID"];
        $this->apiKey = $_ENV["API_KEY"];

        if (!$this->apiId || !$this->apiKey) {
            throw new ErrorException("Missing API_ID or API_KEY environment variables");
        }
    }

    public function scrape(Closure $onScrape): void
    /*
        Scrapes arquivei's api and returns each data response to the $onScrape closure.

        @param Closure $onScrape Callback to each data response, passing as argument an array of Nfe's objects.
    */
    {
        $url = Scraper::BASE_URL . '/v1/nfe/received?limit=' . Scraper::LIMIT;
        do {
            $r = Requests::get($url, array(
                'content-type' => 'application/json',
                'x-api-id' => $this->apiId,
                'x-api-key' => $this->apiKey,
            ));
            $data = json_decode($r->body);
            if ($data->status->code != 200) {
                throw new ErrorException("Error in request (" . $data->status->code . ") '" . $data->status->message . "'");
            }
            $url = $data->page->next;
            $nfes = array_map(function ($item) {
                return new Nfe($item->access_key, Parser::parseNfe($item->xml));
            }, $data->data);
            if (empty($nfes)) break;
            $onScrape($nfes);
        } while (count($nfes) == Scraper::LIMIT);
    }
}
