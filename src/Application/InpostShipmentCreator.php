<?php

namespace Application;

use DateTime;
use Domain\Inpost\Insurance;
use Domain\Inpost\Parcel;
use Domain\Inpost\Receiver;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Creates shipments with Inpost API
 */
class InpostShipmentCreator
{
    private string $token;

    private string $organizationId;

    private Client $inpostClient;

    private string $logDir = __DIR__ . '/../../var/logs';


    public function __construct(
        string $token,
        string $organizationId,
        string $baseInpostUri
    )
    {
        $this->token = $token;

        $this->organizationId = $organizationId;

        $this->inpostClient = new Client([
            'base_uri' => $baseInpostUri
        ]);
    }

    /**
     * @param Receiver $receiver
     * @param Parcel[] $parcels
     * @param Insurance $insurance
     * @param string $service
     * @param string[] $additionalServices
     * @param string $reference
     * @param string $comments
     * @return void
     */
    public function createShipment(
        Receiver $receiver,
        array $parcels,
        Insurance $insurance,
        string $service,
        array $additionalServices,
        string $reference = '',
        string $comments = ''
    ): void
    {
        try {
            $response = $this->inpostClient->post(
                "/v1/organizations/{$this->organizationId}/shipments",
                [
                    'headers' => ['Authorization' => "Bearer $this->token", 'Content-Type' => 'application/json'],
                    'data-raw' => [
                        'receiver' => $this->buildReceiver($receiver),
                        'parcels' => [
                            $this->buildParcels($parcels)
                        ],
                        'insurance' => [
                            'amount' => $insurance->amount,
                            'currency' => $insurance->currency
                        ],
                        'service' => $service,
                        'additional_services' => $additionalServices,
                        'reference' => $reference,
                        'comments' => $comments
                    ]
                ]
            );
            $this->logSuccess($response);
        } catch (\Exception | GuzzleException $e) {
            $this->logError( $e);
        }
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
                    'headers' => ['Authorization' => "Bearer $this->token"]
                ]
            );

            $this->logSuccess($response);
        } catch (Exception|GuzzleException $e) {
            $this->logError($e);
        }
    }

    /**
     * Saves success message to txt file in var/logs
     *
     * @param ResponseInterface $response
     * @return void
     */
    private function logSuccess(ResponseInterface $response): void
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

    /**
     * @param Receiver $receiver
     * @return array
     */
    private function buildReceiver(Receiver $receiver): array
    {
        return [
            'company_name' => $receiver->companyName,
            'first_name' => $receiver->firstName,
            'last_name' => $receiver->lastName,
            'email' => $receiver->email,
            'phone' => $receiver->phone,
            'address' => [
                'street' => $receiver->address->street,
                'building_number' => $receiver->address->buildingNumber,
                'city' => $receiver->address->city,
                'post_code' => $receiver->address->postCode,
                'country_code' => $receiver->address->countryCode
            ]
        ];
    }

    /**
     * @param Parcel[] $parcels
     * @return array
     */
    private function buildParcels(array $parcels): array
    {
        $parcelObjects = [];

        foreach ($parcels as $parcel) {
            $parcelObjects[] = [
                'id' => $parcel->id,
                'dimensions' => [
                    'length' => $parcel->dimension->length,
                    'width' => $parcel->dimension->width,
                    'height' => $parcel->dimension->height,
                    'unit' => $parcel->dimension->unit
                ],
                'weight' => [
                    'amount' => $parcel->weight->amount,
                    'unit' => $parcel->weight->unit
                ],
                'is_non_standard' => $parcel->isNonStandard
            ];
        }

        return $parcelObjects;
    }
}