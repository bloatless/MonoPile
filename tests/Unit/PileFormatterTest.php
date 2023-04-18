<?php

namespace Bloatless\MonoPile\Tests\Unit;

use Bloatless\MonoPile\PileFormatter;
use Monolog\Level;
use Monolog\LogRecord;
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
        $record = $this->getRecord(Level::Debug, 'test', ['foo' => 'bar']);
        $expectedAttributes = $this->provideExptectedAttributes($record);

        $formatter = new PileFormatter('testsuite');
        $formattedRecordString = $formatter->format($record);
        $formattedRecord = json_decode($formattedRecordString, true);

        $this->assertArrayHasKey('data', $formattedRecord);
        $this->assertArrayHasKey('attributes', $formattedRecord['data']);
        $this->assertEquals('log', $formattedRecord['data']['type']);
        $this->assertEquals($expectedAttributes, $formattedRecord['data']['attributes']);
    }

    protected function provideExptectedAttributes(LogRecord $record): array
    {
        $expectedAttributes = $record->toArray();
        $expectedAttributes['source'] = 'testsuite';
        if (isset($expectedAttributes['datetime']) && ($expectedAttributes['datetime'] instanceof \DateTimeInterface)) {
            $datetimeString = $expectedAttributes['datetime']->format('Y-m-d H:i:s');
            $expectedAttributes['datetime'] = $datetimeString;
        }

        return $expectedAttributes;
    }
}
