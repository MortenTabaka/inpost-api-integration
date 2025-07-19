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
        string $building_number,
        string $city,
        string $post_code,
        string $country_code
    ) {
        $this->street = $street;
        $this->buildingNumber = $building_number;
        $this->city = $city;
        $this->postCode = $post_code;
        $this->countryCode = $country_code;
    }
}