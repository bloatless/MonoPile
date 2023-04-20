<?php

namespace Bloatless\MonoPile\Tests\Unit;

use Bloatless\MonoPile\PileFormatter;
use Bloatless\MonoPile\PileHandler;
use Bloatless\MonoPile\Tests\Fixtures\PileHandlerMock;
use Monolog\Level;
use Monolog\Test\TestCase;

class PileHandlerTest extends TestCase
{
    protected $apiUrl = '';

    protected $apiKey = '';

    protected function setUp(): void
    {
        $this->apiUrl = 'http://foobar.net';
        $this->apiKey = '123123';
    }

    public function testCanBeInitialized()
    {
        $handler = new PileHandler($this->apiUrl, $this->apiKey);
        $this->assertInstanceOf(PileHandler::class, $handler);
        $this->assertEquals($this->apiKey, $handler->getApiKey());
        $this->assertEquals($this->apiUrl, $handler->getApiUrl());
    }

    public function testInitWithoutApiUrl()
    {
        $this->expectException('InvalidArgumentException');
        $handler = new PileHandler('', $this->apiKey);
    }

    public function testInitWithoutApiKey()
    {
        $this->expectException('InvalidArgumentException');
        $handler = new PileHandler($this->apiUrl, '');
    }

    public function testHandleWithBubblingAllowed()
    {
        $handler = new PileHandlerMock($this->apiUrl, $this->apiKey);
        $record = $this->getRecord(Level::Debug);
        $result = $handler->handle($record);
        $this->assertFalse($result);

        $dataSend = $handler->getDataSend();
        $this->assertEquals('log', $dataSend['data']['data']['type']);
        $this->assertEquals('test', $dataSend['data']['data']['attributes']['message']);
        $this->assertEquals(100, $dataSend['data']['data']['attributes']['level']);
        $this->assertEquals('not_provided', $dataSend['data']['data']['attributes']['source']);
        $this->assertEquals('Content-Type: application/json', $dataSend['headers'][0]);
        $this->assertEquals(sprintf('X-API-Key: %s', $this->apiKey), $dataSend['headers'][1]);
    }

    public function testHandleWithBubblingNotAllowed()
    {
        $handler = new PileHandlerMock($this->apiUrl, $this->apiKey, Level::Debug, false);
        $record = $this->getRecord(Level::Debug);
        $result = $handler->handle($record);
        $this->assertTrue($result);
    }

    public function testHandleWithSourceProvided()
    {
        $handler = new PileHandlerMock($this->apiUrl, $this->apiKey);
        $handler->setFormatter((new PileFormatter('testsuite')));
        $record = $this->getRecord(Level::Debug);
        $handler->handle($record);
        $dataSend = $handler->getDataSend();
        $this->assertEquals('testsuite', $dataSend['data']['data']['attributes']['source']);
    }
}
