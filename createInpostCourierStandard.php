<?php

require __DIR__ . '/vendor/autoload.php';

use Application\InpostShipmentCreator;
use Domain\Inpost\Address;
use Domain\Inpost\Dimension;
use Domain\Inpost\InpostCourierServicesEnum;
use Domain\Inpost\Parcel;
use Domain\Inpost\Receiver;
use Domain\Inpost\Weight;

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

$shipmentsHandler = new InpostShipmentCreator(
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

$receiver = new Receiver(
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

$shipmentsHandler->createShipment(
    $receiver,
    $parcels,
    InpostCourierServicesEnum::INPOST_COURIER_STANDARD
);
