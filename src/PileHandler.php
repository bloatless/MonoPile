<?php

declare(strict_types=1);

namespace Bloatless\MonoPile;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\MissingExtensionException;
use Monolog\Logger;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

class PileHandler extends AbstractProcessingHandler
{
    /**
     * @var string $apiUrl
     */
    protected $apiUrl;

    /**
     * @var string $apiKey
     */
    protected $apiKey;

    /**
     * @param string $apiUrl
     * @param string $apiKey
     * @param string|int $level The minimum logging level to trigger this handler
     * @param bool $bubble Whether messages that are handled should bubble up the stack.
     *
     * @throws MissingExtensionException If the curl extension is missing
     */
    public function __construct(string $apiUrl, string $apiKey, $level = Logger::DEBUG, bool $bubble = true)
    {
        if (!extension_loaded('curl')) {
            throw new MissingExtensionException('The curl extension is needed to use the PileHandler');
        }
        if (empty($apiUrl)) {
            throw new \InvalidArgumentException('API-Url can not be empty.');
        }
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('API-Key can not be empty.');
        }

        $this->setApiUrl($apiUrl);
        $this->setApiKey($apiKey);

        parent::__construct($level, $bubble);
    }

    /**
     * Returns the API Url.
     *
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * Sets the API Url.
     *
     * @param string $apiUrl
     */
    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * Returns the API key.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Sets the API key.
     *
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Writes a log-record to the Pile API.
     *
     * @param array $record
     */
    protected function write(LogRecord $record): void
    {
        if (empty($record['formatted'])) {
            return;
        }

        $this->send($record['formatted']);
    }

    /**
     * Sends a request to the Pile API.
     *
     * @param string $data
     */
    protected function send(string $data): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getApiUrl());
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getRequestHeaders());
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        $server_output = curl_exec($ch);
    }

    protected function getRequestHeaders(): array
    {
        return [
            'Content-Type: application/json',
            'X-API-Key: ' . $this->getApiKey(),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new PileFormatter('not_provided');
    }
}
