<?php

namespace Application;

use Domain\Inpost\COD;
use Domain\Inpost\InpostCourierServices;
use Domain\Inpost\Insurance;
use Domain\Inpost\Parcel\Parcel;
use Domain\Inpost\Participant;
use Domain\Inpost\SendingMethods;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Creates shipments with Inpost API
 */
class InpostCourierShipmentCreator
{
    private string $token;

    private string $organizationId;

    private Client $inpostClient;

    private InpostHandleResponse $inpostHandleResponse;


    /**
     * @param string $token Inpost API token
     * @param string $organizationId Inpost organization ID
     * @param string $baseInpostUri Inpost API base URI
     */
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
     * Create Inpost courier shipment
     *
     * @param Participant|null $sender If no data is provided, the organization data for which the shipment is created will be used by default
     * @param Participant $receiver
     * @param Parcel[] $parcels
     * @param Insurance $insurance required for courier shipments
     * @param string $service name of courier service
     * @param string[] $additionalServices
     * @param string $reference
     * @param string $comments
     * @return void
     */
    public function createShipment(
        ?Participant $sender,
        Participant $receiver,
        array $parcels,
        ?Insurance $insurance,
        ?COD $cod,
        string $service,
        array $additionalServices,
        string $sendingMethod,
        string $reference = '',
        string $comments = ''
    ): void
    {
        try {
            $this->validateReceiver($receiver);

            $this->validateSendingMethod($sendingMethod, $service);

            $body = [
                'receiver' => $this->buildParticipantData($receiver),
                'parcels' => $this->buildParcels($parcels),
                'service' => $service,
                'additional_services' => $additionalServices,
                'custom_attributes' => ['sending_method' => $sendingMethod],
                'reference' => $reference,
                'comments' => $comments,
            ];

            if ($sender) {
                $body['sender'] = $this->buildParticipantData($sender);
            }

            if ($insurance) {
                $body['insurance'] = $this->buildInsurance($insurance);
            }

            if ($cod) {
                if (!$insurance) {
                    throw new \RuntimeException('Insurance is required when cash collection amount is provided');
                }

                $body['cod'] = $this->buildCOD($cod);
            }

            $response = $this->inpostClient->post(
                "/v1/organizations/{$this->organizationId}/shipments",
                [
                    'headers' => ['Authorization' => "Bearer $this->token", 'Content-Type' => 'application/json'],
                    'json' => $body
                ]
            );

            $this->inpostHandleResponse->logSuccess($response);
        } catch (\Exception | GuzzleException | \RuntimeException $e) {
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
     * Create participant data for receiver or sender
     *
     * @param Participant $receiver
     * @return array
     */
    private function buildParticipantData(Participant $receiver): array
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
     * In case of a courier service offer (inpost_courier_c2c included),
     * at least receiver.phone_number, receiver.company_name and/or receiver.first_name and receiver.last_name and address
     * object should be provided.
     *
     * @param Participant $receiver
     * @return void
     */
    private function validateReceiver(Participant $receiver): void
    {
        if (
            empty($receiver->phone) ||
            (
                empty($receiver->companyName) &&
                (
                    empty($receiver->firstName) &&
                    empty($receiver->lastName)
                )
            )
        ) {
            throw new \RuntimeException('Receiver data is missing');
        }
    }

    /**
     * Validate sending method with chosen service.
     * @see https://dokumentacja-inpost.atlassian.net/wiki/spaces/PL/pages/11731047/Spos+b+nadania
     *
     * @param string $sendingMethod
     * @param string $service
     * @return void
     */
    private function validateSendingMethod(string $sendingMethod, string $service): void
    {
        if (
            $sendingMethod === SendingMethods::PARCEL_LOCKER
            && in_array($service, [
                InpostCourierServices::INPOST_COURIER_STANDARD,
                InpostCourierServices::INPOST_COURIER_EXPRESS_1000,
                InpostCourierServices::INPOST_COURIER_EXPRESS_1200,
                InpostCourierServices::INPOST_COURIER_EXPRESS_1700,
            ], true)
        ) {
            throw new \RuntimeException('Parcel locker is not available for this service: ' . $service);
        }
    }

    /**
     * Build cash collection amount object
     *
     * @param COD $cod
     * @return array
     */
    private function buildCOD(COD $cod): array
    {
        return [
            'amount' => $cod->amount,
            'currency' => $cod->currency
        ];
    }
}