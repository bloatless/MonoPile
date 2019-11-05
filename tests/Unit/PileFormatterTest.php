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
        $formatter = new PileFormatter('testsuite');
        $record = $this->getRecord(Logger::DEBUG, 'test', ['foo' => 'bar']);
        $encodedRecord = $formatter->format($record);
        $decodedRecord = json_decode($encodedRecord, true);
        $expected = $record;
        $expected['source'] = 'testsuite';
        if (isset($expected['datetime']) && ($expected['datetime'] instanceof \DateTimeInterface)) {
            $datetimeString = $expected['datetime']->format('Y-m-d H:i:s');
            $expected['datetime'] = $datetimeString;
        }

        $this->assertEquals($expected, $decodedRecord);
    }
}
