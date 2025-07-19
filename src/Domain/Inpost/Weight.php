<?php

namespace Domain\Inpost;

/**
 * Parcel weight. Inpost allows only kg as unit.
 */
class Weight
{
    public float $amount;

    public string $unit;

    public function __construct(
        float $amount
    )
    {
        $this->amount = $amount;
        $this->unit = 'kg';  // only `kg` is allowed
    }
}