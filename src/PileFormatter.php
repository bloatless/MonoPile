<?php

declare(strict_types=1);

namespace Bloatless\MonoPile;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

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
     * Formats a record, so it matches the Pile API requirements.
     *
     * @param LogRecord $record
     * @return string
     */
    public function format(LogRecord $record): string
    {
        // convert datetime
        $formattedRecord = $record->toArray();
        if (isset($formattedRecord['datetime']) && ($formattedRecord['datetime'] instanceof \DateTimeInterface)) {
            $datetimeString = $formattedRecord['datetime']->format('Y-m-d H:i:s');
            $formattedRecord['datetime'] = $datetimeString;
        }

        // add source
        $formattedRecord['source'] = $this->source;

        // wrap record in json-api compatible format
        return json_encode([
            'data' => [
                'type' => 'log',
                'attributes' => $formattedRecord,
            ],
        ]);
    }
}
