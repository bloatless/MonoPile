<?php

namespace Bloatless\MonoPile\Tests\Fixtures;

use Bloatless\MonoPile\PileHandler;

class PileHandlerMock extends PileHandler
{
    protected $dataSend = [];

    public function getDataSend(): array
    {
        return $this->dataSend;
    }

    // we do not want to send actual API requests
    protected function send(string $data): void
    {
        $this->dataSend = [
            'data' => json_decode($data, true),
            'headers' => $this->getRequestHeaders(),
        ];
    }
}
