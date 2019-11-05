<?php

declare(strict_types=1);

namespace Bloatless\MonoPile;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\MissingExtensionException;
use Monolog\Logger;
use Monolog\Formatter\FormatterInterface;

class PileHandler extends AbstractProcessingHandler
{
    protected const API_URL = 'https://pile.geekservice.de/api/v1/log';

    /**
     * @var string $apiUrl
     */
    protected $apiUrl;

    /**
     * @var string $apiKey
     */
    protected $apiKey;

    /**
     * @param string $apiKey
     * @param string $apiUrl
     * @param string|int $level The minimum logging level to trigger this handler
     * @param bool $bubble Whether or not messages that are handled should bubble up the stack.
     *
     * @throws MissingExtensionException If the curl extension is missing
     */
    public function __construct(string $apiKey, string $apiUrl = '', $level = Logger::DEBUG, bool $bubble = true)
    {
        if (!extension_loaded('curl')) {
            throw new MissingExtensionException('The curl extension is needed to use the PileHandler');
        }

        $this->apiUrl = (!empty($apiUrl)) ? $apiUrl : self::API_URL;
        $this->apiKey = $apiKey;

        parent::__construct($level, $bubble);
    }

    /**
     * Writes a log-record to the Pile API.
     *
     * @param array $record
     */
    protected function write(array $record): void
    {
        $data = json_decode($record['formatted'], true);
        $this->send($data);
    }

    /**
     * Sends a request to the Pile API.
     *
     * @param array $data
     */
    protected function send(array $data): void
    {
        $requestData = json_encode([
            'data' => [
                'type' => 'log',
                'attributes' => $data,
            ],
        ]);


        $headers = [
            'Content-Type: application/json',
            'X-API-Key: ' . $this->apiKey,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        $server_output = curl_exec($ch);
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new PileFormatter('not_provided');
    }
}
