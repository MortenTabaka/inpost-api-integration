<?php

namespace Domain;

class Parcel
{
    public string $id;

    public Dimension $dimension;

    public Weight $weight;

    public bool $isNonStandard;

    public function __construct(
        string $id,
        Dimension $dimension,
        Weight $weight,
        bool $isNonStandard
    )
    {
        $this->id = $id;
        $this->dimension = $dimension;
        $this->weight = $weight;
        $this->isNonStandard = $isNonStandard;
    }
}