<?php

namespace Application;

use DateTime;
use Domain\Inpost\Insurance;
use Domain\Inpost\Parcel\Parcel;
use Domain\Inpost\Receiver;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Creates shipments with Inpost API
 */
class InpostShipmentCreator
{
    private string $token;

    private string $organizationId;

    private Client $inpostClient;

    private InpostHandleResponse $inpostHandleResponse;


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

        $this->inpostHandleResponse = new InpostHandleResponse();
    }

    /**
     * Create Inpost Courier shipment
     *
     * @param Receiver $receiver
     * @param Parcel[] $parcels
     * @param Insurance $insurance  // required for
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
                    'json' => [
                        'receiver' => $this->buildReceiver($receiver),
                        'parcels' => $this->buildParcels($parcels),
                        'insurance' => $this->buildInsurance($insurance),
                        'service' => $service,
                        'additional_services' => $additionalServices,
                        'reference' => $reference,
                        'comments' => $comments
                    ]
                ]
            );
            $this->inpostHandleResponse->logSuccess($response);
        } catch (\Exception | GuzzleException $e) {
            $this->inpostHandleResponse->logError( $e);
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

            $this->inpostHandleResponse->logSuccess($response);
        } catch (Exception|GuzzleException $e) {
            $this->inpostHandleResponse->logError($e);
        }
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
     * Create parcel data for shipment
     *
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

    /**
     * Create insurance object for shipment. Required for courier shipments.
     *
     * @param Insurance $insurance
     * @return array
     */
    private function buildInsurance(Insurance $insurance): array
    {
        return [
            'amount' => $insurance->amount,
            'currency' => $insurance->currency
        ];
    }

    /**
     * @return string
     */
    public function getLogDir(): string
    {
        return $this->logDir;
    }
}