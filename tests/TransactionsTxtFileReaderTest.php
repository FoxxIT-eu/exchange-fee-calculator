<?php

namespace App\Tests;

use App\Util\TransactionsTxtFileReader;
use PHPUnit\Framework\TestCase;
use App\Dto\TransactionDataDto;

class TransactionsTxtFileReaderTest extends TestCase
{
    private $testFilePath;

    protected function setUp(): void
    {
        $this->testFilePath = __DIR__ . '/test_transactions.txt';

        $sampleData = <<<EOD
{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
{"bin":"45417360","amount":"10000.00","currency":"JPY"}
{"bin":"41417360","amount":"130.00","currency":"USD"}
{"bin":"4745030","amount":"2000.00","currency":"GBP"}
EOD;
        file_put_contents($this->testFilePath, $sampleData);
    }

    protected function tearDown(): void
    {
        unlink($this->testFilePath);
    }

    public function testFileReadingAndParsing()
    {
        $reader = new TransactionsTxtFileReader($this->testFilePath);
        $this->assertTrue($reader->haveRows());
        $firstRow = $reader->getNextRow();
        $this->assertInstanceOf(TransactionDataDto::class, $firstRow);
        $this->assertEquals('45717360', $firstRow->getBin());
        $this->assertEquals(100.00, $firstRow->getAmount());
        $this->assertEquals('EUR', $firstRow->getCurrency());
    }

    public function testNonExistentFile()
    {
        $this->expectException(\RuntimeException::class);
        new TransactionsTxtFileReader('nonexistentfile.txt');
    }

    public function testIterationOverRows()
    {
        $reader = new TransactionsTxtFileReader($this->testFilePath);
        $count = 0;
        while ($reader->haveRows()) {
            $row = $reader->getNextRow();
            $this->assertInstanceOf(TransactionDataDto::class, $row);
            $count++;
        }
        $this->assertEquals(5, $count);
    }

    public function testMalformedData()
    {
        $malformedData = '{"bin":"45717360","amount":"100.00","currency":"EUR"}' . PHP_EOL .
            'malformed row' . PHP_EOL .
            '{"bin":"516793","amount":"50.00","currency":"USD"}';
        file_put_contents($this->testFilePath, $malformedData);
        $reader = new TransactionsTxtFileReader($this->testFilePath);

        $firstRow = $reader->getNextRow();
        $this->assertInstanceOf(TransactionDataDto::class, $firstRow);
        $this->assertEquals('45717360', $firstRow->getBin());

        $secondRow = $reader->getNextRow();
        $this->assertEquals('516793', $secondRow->getBin());
    }

    public function testEmptyFile()
    {
        file_put_contents($this->testFilePath, '');
        $reader = new TransactionsTxtFileReader($this->testFilePath);
        $this->assertFalse($reader->haveRows());
    }

    public function testPartiallyEmptyLines()
    {
        $dataWithEmptyLines = '{"bin":"45717360","amount":"100.00","currency":"EUR"}' . PHP_EOL . PHP_EOL .
            '{"bin":"516793","amount":"50.00","currency":"USD"}';
        file_put_contents($this->testFilePath, $dataWithEmptyLines);
        $reader = new TransactionsTxtFileReader($this->testFilePath);

        $this->assertTrue($reader->haveRows());
        $firstRow = $reader->getNextRow();
        $this->assertEquals('45717360', $firstRow->getBin());

        $this->assertTrue($reader->haveRows());
        $secondRow = $reader->getNextRow();
        $this->assertEquals('516793', $secondRow->getBin());
    }

    public function testInvalidJson()
    {
        $invalidJsonData = '{"bin":"45717360","amount":"100.00","currency":"EUR"}' . PHP_EOL .
            '{"this is not valid json"}' . PHP_EOL .
            '{"bin":"516793","amount":"50.00","currency":"USD"}';
        file_put_contents($this->testFilePath, $invalidJsonData);
        $reader = new TransactionsTxtFileReader($this->testFilePath);

        $firstRow = $reader->getNextRow();
        $this->assertEquals('45717360', $firstRow->getBin());
        $secondRow = $reader->getNextRow();
        $this->assertEquals('516793', $secondRow->getBin());
    }
}
