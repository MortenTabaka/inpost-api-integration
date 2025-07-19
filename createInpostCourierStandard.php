<?php

require __DIR__ . '/vendor/autoload.php';

use Application\InpostShipmentCreator;

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

$shipmentsHandler->createShipment();
