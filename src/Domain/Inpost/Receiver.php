<?php

namespace Domain\Inpost;

class Receiver
{
    public ?string $companyName;
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
        $this->companyName = $company_name;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
    }
}