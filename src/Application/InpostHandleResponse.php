<?php

namespace Application;

use DateTime;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class InpostHandleResponse
{
    private string $logDir = __DIR__ . '/../../var/logs';

    /**
     * Saves error to txt file in var/logs
     *
     * @param $error
     * @return void
     */
    public function logError($error): void
    {
        [$today, $nowFormatted] = $this->getDatesForLogs();

        if ($error instanceof RequestException && $error->hasResponse()) {
            $response = $error->getResponse();
            $body = $response?->getBody()->getContents();
            $message = "[{$nowFormatted}] HTTP {$response?->getStatusCode()} Error: {$body}";
        } else {
            $message = "[{$nowFormatted}] Exception: {$error->getMessage()}";
        }

        echo $message . PHP_EOL;

        // save to txt file
        file_put_contents(
            "{$this->logDir}/$today-error.txt",
            "[$nowFormatted] " . $error->getMessage() . "\n",
            FILE_APPEND
        );
    }

    /**
     * Saves success message to txt file in var/logs
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function logSuccess(ResponseInterface $response): void
    {
        echo $response->getBody()->getContents();

        [$today, $nowFormatted] = $this->getDatesForLogs();

        // save to txt file
        file_put_contents(
            "{$this->logDir}/$today-success.txt",
            "[$nowFormatted] " . $response->getBody()->getContents() . "\n",
            FILE_APPEND
        );
    }

    /**
     * @return array Returns array with today's date and current date and time
     */
    private function getDatesForLogs(): array
    {
        $now = new DateTime();
        $today = $now->format('d-m-Y');
        $nowFormatted = $now->format('Y-m-d H:i:s.v');
        return array($today, $nowFormatted);
    }
}