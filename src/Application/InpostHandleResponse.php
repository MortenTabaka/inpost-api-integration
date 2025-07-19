<?php

namespace Application;

use DateTime;
use Domain\Inpost\StatusCodes;
use GuzzleHttp\Exception\RequestException;
use JsonException;
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

            if (!$response) {
                return;
            }

            $message = $this->processApiError($response, $nowFormatted);
        } else {
            $message = "[{$nowFormatted}] Exception: {$error->getMessage()}";
        }

        echo $message . PHP_EOL;

        // save to txt file
        file_put_contents(
            "{$this->logDir}/$today-error.txt",
            "[$nowFormatted] " . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * Saves success message to txt file in var/logs and echos it
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function logSuccess(ResponseInterface $response): void
    {
        $contents = $response->getBody()->getContents();

        [$today, $nowFormatted] = $this->getDatesForLogs();

        // save to txt file
        file_put_contents(
            "{$this->logDir}/$today-success.txt",
            "[$nowFormatted] " . $response->getBody()->getContents() . PHP_EOL,
            FILE_APPEND
        );

        echo $contents;

        try {
            $contentsDecoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

            if ($contentsDecoded['status'] === strtolower(StatusCodes::CREATED->name)) {
                echo "Shipment created successfully" . PHP_EOL;
            }
        } catch (JsonException $e) {
            $this->logError($e);
            return;
        }
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

    /**
     * Processes API error and saves it to txt file
     *
     * @param ResponseInterface $response
     * @param mixed $nowFormatted
     * @return string
     */
    private function processApiError(ResponseInterface $response, mixed $nowFormatted): string
    {
        $statusCode = $response->getStatusCode();

        $body = $response->getBody();

        $contents = $body->getContents();

        try {
            $contentsDecoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

            $errorMessage = $contentsDecoded['error'] ?? null;

            $message = match ($errorMessage) {
                "missing_trucker_id" => "[missing_trucker_id] Wymagane jest posiadanie realnej umowy z Inpostem" . PHP_EOL,
                "no_carriers" => "[no_carriers] The organization has no carriers contracted" . PHP_EOL,
                "carrier_unavailable" => "[carrier_unavailable] The organization has no carriers contracted providing the requested service" . PHP_EOL,
                default => "Error: $contents" . PHP_EOL,
            };
        } catch (JsonException $e) {
            $this->logError($e);
            $message = "Error: $contents" . PHP_EOL;
        }

        return "[$nowFormatted] [HTTP $statusCode] " . $message;
    }
}