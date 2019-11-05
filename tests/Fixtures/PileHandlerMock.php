<?php

namespace Bloatless\MonoPile\Tests\Fixtures;

use Bloatless\MonoPile\PileHandler;

class PileHandlerMock extends PileHandler
{
    // we do not want to send actual API requests
    protected function send(array $data): void
    {
        return;
    }
}
