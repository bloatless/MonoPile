<?php

namespace Bloatless\MonoPile\Tests\Unit;

use Bloatless\MonoPile\PileFormatter;
use Monolog\Logger;
use Monolog\Test\TestCase;

class PileFormatterTest extends TestCase
{
    public function testCanBeInitialized()
    {
        $formatter = new PileFormatter('testsuite');
        $this->assertInstanceOf(PileFormatter::class, $formatter);
    }

    public function testFormat()
    {
        $record = $this->getRecord(Logger::DEBUG, 'test', ['foo' => 'bar']);
        $expectedAttributes = $this->provideExptectedAttributes($record);

        $formatter = new PileFormatter('testsuite');
        $formattedRecordString = $formatter->format($record);
        $formattedRecord = json_decode($formattedRecordString, true);

        $this->assertArrayHasKey('data', $formattedRecord);
        $this->assertArrayHasKey('attributes', $formattedRecord['data']);
        $this->assertEquals('log', $formattedRecord['data']['type']);
        $this->assertEquals($expectedAttributes, $formattedRecord['data']['attributes']);
    }

    protected function provideExptectedAttributes(array $record): array
    {
        $expectedAttributes = $record;
        $expectedAttributes['source'] = 'testsuite';
        if (isset($expectedAttributes['datetime']) && ($expectedAttributes['datetime'] instanceof \DateTimeInterface)) {
            $datetimeString = $expectedAttributes['datetime']->format('Y-m-d H:i:s');
            $expectedAttributes['datetime'] = $datetimeString;
        }

        return $expectedAttributes;
    }
}
