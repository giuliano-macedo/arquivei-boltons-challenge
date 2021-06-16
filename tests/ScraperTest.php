<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Mock requests
$NO_REQUESTS = 0;
class Requests
{
    static function get()
    {
        global $NO_REQUESTS;
        $dummyXml = file_get_contents(dirname(__FILE__) . "/dummyNfe.txt");
        $data = match ($NO_REQUESTS) {
            0 => array_fill(0, 50, array('xml' => $dummyXml, 'access_key' => 42)),
            default => array()
        };
        $NO_REQUESTS++;
        $body = json_encode(array(
            'status' => array(
                'code' => 200,
                'message' => 'ok'
            ),
            'data' => $data,
            'page' => array(
                'next' => ''
            )
        ));
        return (object)array('body' => $body);
    }
}


final class ScraperTest extends TestCase
{
    public function setUp(): void
    {
        $_ENV["API_ID"] = "mock";
        $_ENV["API_KEY"] = "mock";
    }

    public function testParseExampleNfeDocument(): void
    {
        $nfes = array();
        $scraper = new Scraper();
        $scraper->scrape(function (array $data) use (&$nfes) {
            $this->assertNotEmpty($data);
            array_push($nfes, ...$data);
        });
        $this->assertEquals(count($nfes), 50);
        foreach ($nfes as $nfe) {
            $this->assertEquals($nfe->getAccessKey(), 42);
        }
    }
}
