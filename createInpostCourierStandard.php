<?php

require __DIR__ . '/vendor/autoload.php';

use Application\InpostShipmentCreator;

$token = getenv('INPOST_TOKEN');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = $_ENV['INPOST_API_TOKEN'] ?? null;

if ($token) {
    echo "Found INPOST_API_TOKEN.\n";
} else {
    echo "INPOST_API_TOKEN is not set in environment variables.\n";
    exit(1);
}

$baseInpostUri = $_ENV['INPOST_API_URL'] ?? null;

if ($baseInpostUri) {
    echo "Found INPOST_API_URL.\n";
} else {
    echo "INPOST_API_URL is not set in environment variables.\n";
    exit(1);
}

$shipmentsHandler = new InpostShipmentCreator(
    $token,
    $baseInpostUri
);

$shipmentsHandler->getCreatedShipments();