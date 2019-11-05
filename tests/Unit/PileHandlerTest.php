<?php

namespace Bloatless\MonoPile\Tests\Unit;

use Bloatless\MonoPile\PileHandler;
use Bloatless\MonoPile\Tests\Fixtures\PileHandlerMock;
use Monolog\Logger;
use Monolog\Test\TestCase;

class PileHandlerTest extends TestCase
{
    public function testCanBeInitialized()
    {
        $handler = new PileHandler('123123123');
        $this->assertInstanceOf(PileHandler::class, $handler);
    }

    public function testHandleWithBubblingAllowed()
    {
        $handler = new PileHandlerMock('123');
        $record = $this->getRecord(Logger::DEBUG);
        $result = $handler->handle($record);
        $this->assertFalse($result);
    }

    public function testHandleWithBubblingNotAllowed()
    {
        $handler = new PileHandlerMock('123', '', Logger::DEBUG, false);
        $record = $this->getRecord(Logger::DEBUG);
        $result = $handler->handle($record);
        $this->assertTrue($result);
    }
}
