<?php

namespace Domain;

class Address
{
    public string $street;
    public string $buildingNumber;
    public string $city;
    public string $postCode;
    public string $countryCode;

    public function __construct(
        string $street,
        string $buildingNumber,
        string $city,
        string $postCode,
        string $countryCode
    ) {
        $this->street = $street;
        $this->buildingNumber = $buildingNumber;
        $this->city = $city;
        $this->postCode = $postCode;
        $this->countryCode = $countryCode;
    }
}