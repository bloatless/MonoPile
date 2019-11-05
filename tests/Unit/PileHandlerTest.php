<?php

namespace Bloatless\MonoPile\Tests\Unit;

use Bloatless\MonoPile\PileFormatter;
use Bloatless\MonoPile\PileHandler;
use Bloatless\MonoPile\Tests\Fixtures\PileHandlerMock;
use Monolog\Logger;
use Monolog\Test\TestCase;

class PileHandlerTest extends TestCase
{
    public function testCanBeInitialized()
    {
        $apiUrlDefault = 'https://pile.geekservice.de/api/v1/log';
        $apiUrlCustom = 'http://foobar.net';
        $apiKey = '31337';

        // test with default API url
        $handler = new PileHandler($apiKey);
        $this->assertInstanceOf(PileHandler::class, $handler);
        $this->assertEquals($apiKey, $handler->getApiKey());
        $this->assertEquals($apiUrlDefault, $handler->getApiUrl());

        // test with custom API url
        $handler = new PileHandler($apiKey, $apiUrlCustom);
        $this->assertEquals($apiUrlCustom, $handler->getApiUrl());
    }

    public function testHandleWithBubblingAllowed()
    {
        $handler = new PileHandlerMock('123');
        $record = $this->getRecord(Logger::DEBUG);
        $result = $handler->handle($record);
        $this->assertFalse($result);

        $dataSend = $handler->getDataSend();
        $this->assertEquals('log', $dataSend['data']['data']['type']);
        $this->assertEquals('test', $dataSend['data']['data']['attributes']['message']);
        $this->assertEquals(100, $dataSend['data']['data']['attributes']['level']);
        $this->assertEquals('not_provided', $dataSend['data']['data']['attributes']['source']);
        $this->assertEquals('Content-Type: application/json', $dataSend['headers'][0]);
        $this->assertEquals('X-API-Key: 123', $dataSend['headers'][1]);
    }

    public function testHandleWithBubblingNotAllowed()
    {
        $handler = new PileHandlerMock('123', '', Logger::DEBUG, false);
        $record = $this->getRecord(Logger::DEBUG);
        $result = $handler->handle($record);
        $this->assertTrue($result);
    }

    public function testHandleWithSourceProvided()
    {
        $handler = new PileHandlerMock('123');
        $handler->setFormatter((new PileFormatter('testsuite')));
        $record = $this->getRecord(Logger::DEBUG);
        $handler->handle($record);
        $dataSend = $handler->getDataSend();
        $this->assertEquals('testsuite', $dataSend['data']['data']['attributes']['source']);
    }
}
