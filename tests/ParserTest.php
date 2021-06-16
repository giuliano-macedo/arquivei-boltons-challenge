<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase {
    public function testParseExampleNfeDocument(): void {
        $exampleDocumentB64 = file_get_contents(dirname(__FILE__)."/dummyNfe.txt");
        $expectedValue = "1348.00";
        $this->assertEquals(Parser::parseNfe($exampleDocumentB64),$expectedValue);
    }

}