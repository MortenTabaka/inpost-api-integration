<?php

namespace Application;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Creates shipments with Inpost API
 */
class InpostShipmentCreator
{
    private string $token;

    private Client $inpostClient;

    private string $logDir = __DIR__ . '/../../var/logs';


    public function __construct(
        string $token,
        string $baseInpostUri
    )
    {
        $this->token = $token;

        $this->inpostClient = new Client([
            'base_uri' => $baseInpostUri
        ]);
    }

    /**
     * @return void
     */
    public function createShipment(): void
    {
//        try {
//            $response = $this->getCreatedShipments();
//            $this->logSuccess($response);
//        } catch (\Exception | GuzzleException $e) {
//            $this->logError($e);
//        }
    }

    /**
     * Calls Inpost API to get created shipments
     *
     * @return void
     */
    public function getCreatedShipments(): void
    {
        try {
            $response = $this->inpostClient->request(
                'GET',
                '/v1/shipments/ID_Shipments',
                [
                    'headers' => $this->getAuthHeaders()
                ]
            );

            $this->logSuccess($response);
        } catch (Exception|GuzzleException $e) {
            $this->logError($e);
        }
    }

    /**
     * @return string[] Returns headers array with authorization token
     */
    private function getAuthHeaders(): array
    {
        return [
            'Authorization' => "Bearer $this->token"
        ];
    }

    /**
     * Saves success message to txt file in var/logs
     *
     * @param $response
     * @return void
     */
    private function logSuccess($response): void
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
     * Saves error to txt file in var/logs
     *
     * @param $error
     * @return void
     */
    private function logError($error): void
    {
        echo $error->getMessage();

        [$today, $nowFormatted] = $this->getDatesForLogs();

        // save to txt file
        file_put_contents(
            "{$this->logDir}/$today-error.txt",
            "[$nowFormatted] " . $error->getMessage() . "\n",
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