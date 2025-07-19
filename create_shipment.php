<?php

require __DIR__ . '/vendor/autoload.php';

use Application\InpostCourierShipmentCreator;
use Domain\Inpost\Address;
use Domain\Inpost\CourierStandardAdditionalServices;
use Domain\Inpost\InpostCourierServices;
use Domain\Inpost\Insurance;
use Domain\Inpost\Parcel\Dimension;
use Domain\Inpost\Parcel\Parcel;
use Domain\Inpost\Parcel\Weight;
use Domain\Inpost\Participant;
use Domain\Inpost\SendingMethods;

$token = getenv('INPOST_TOKEN');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = $_ENV['INPOST_API_TOKEN'] ?? null;

$baseInpostUri = $_ENV['INPOST_API_URL'] ?? null;

$organization = $_ENV['INPOST_ORGANIZATION_ID'] ?? null;

if (
    !$token
    || !$baseInpostUri
    || !$organization
) {
    echo "INPOST_API_TOKEN or INPOST_API_URL or INPOST_ORGANIZATION_ID is missing in .env file.\n";
    exit(1);
}

$shipmentsHandler = new InpostCourierShipmentCreator(
    $token,
    $organization,
    $baseInpostUri
);

$address = new Address(
    'Słowackiego',
    '1',
    'Warszawa',
    '00-718',
    'PL'
);

$receiver = new Participant(
    '',
    'Adam',
    'Kowalski',
    'adam.kowalski@example.com',
    '+48123456789',
    $address
);

$parcels = [
    new Parcel(
        '1',
        new Dimension(10, 10, 10),
        new Weight(1),
        false
    )
];

$insurance = new Insurance(100, 'PLN');

$shipmentsHandler->createShipment(
    null,
    $receiver,
    $parcels,
    $insurance,
    null,
    InpostCourierServices::INPOST_COURIER_STANDARD,
    [CourierStandardAdditionalServices::EMAIL],
    SendingMethods::DISPATCH_ORDER
);
