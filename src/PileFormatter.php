<?php

declare(strict_types=1);

namespace Bloatless\MonoPile;

use Monolog\Formatter\JsonFormatter;

class PileFormatter extends JsonFormatter
{
    /**
     * @var string $source A keyword identifying the log-source (e.g. project name).
     */
    protected $source;

    public function __construct(string $source, int $batchMode = self::BATCH_MODE_JSON, bool $appendNewline = true)
    {
        $this->source = $source;
        parent::__construct($batchMode, $appendNewline);
    }

    /**
     * Formats a record so it matches the Pile API requirements.
     *
     * @param array $record
     * @return string
     */
    public function format(array $record): string
    {
        // convert datetime
        if (isset($record['datetime']) && ($record['datetime'] instanceof \DateTimeInterface)) {
            $datetimeString = $record['datetime']->format('Y-m-d H:i:s');
            $record['datetime'] = $datetimeString;
        }

        // add source
        $record['source'] = $this->source;

        // wrap record in json-api compatible format
        $formatted = [
            'data' => [
                'type' => 'log',
                'attributes' => $record,
            ],
        ];

        return parent::format($formatted);
    }
}
