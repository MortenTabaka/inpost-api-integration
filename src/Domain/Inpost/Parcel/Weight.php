<?php

namespace Domain\Inpost\Parcel;

/**
 * Parcel weight. Inpost allows only kg as unit.
 */
class Weight
{
    public float $amount;

    public string $unit;

    /**
     * @param float $amount Amount in kilograms
     */
    public function __construct(
        float $amount
    )
    {
        $this->amount = $amount;
        $this->unit = 'kg';  // only `kg` is allowed
    }
}