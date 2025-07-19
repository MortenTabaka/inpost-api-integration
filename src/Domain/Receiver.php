<?php

namespace Domain;

class Receiver
{
    /*
     * {"id":26590330,"street":"Polki","building_number":"1B","line1":null,"line2":null,"city":"Warszawa","post_code":"02-826","country_code":"PL"},"invoice_address":{"id":26590331,"street":"Polki","building_number":"1B","line1":null,"line2":null,"city":"Warszawa","post_code":"02-826","country_code":"PL"},"contact_person":null,"created_at":"2025-07-19T11:29:29.099+02:00","updated_at":"2025-07-19T11:29:29.347+02:00"}
     */
    public ?string $company_name;
    public ?string $firstName;
    public ?string $lastName;
    public ?string $email;
    public ?string $phone;

    public Address $address;

    public function __construct(
        ?string $company_name,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $phone,
        Address $address
    ) {
        $this->company_name = $company_name;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
    }
}