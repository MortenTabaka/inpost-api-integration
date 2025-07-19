<?php

namespace Domain;

class Weight
{
    public float $amount;

    public string $unit;

    public function __construct(float $amount, string $unit)
    {
        $this->amount = $amount;
        $this->unit = $unit;
    }
}