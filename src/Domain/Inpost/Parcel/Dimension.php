<?php

namespace Domain\Inpost\Parcel;

/**
 * Parcel dimension. Inpost allows only mm as unit.
 */
class Dimension
{
    public float $length;
    public float $width;
    public float $height;
    public string $unit;

    /**
     * @param float $length Value in millimeters
     * @param float $width Value in millimeters
     * @param float $height Value in millimeters
     */
    public function __construct(
        float $length,
        float $width,
        float $height
    )
    {
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->unit = 'mm';  // only `mm` is allowed
    }
}