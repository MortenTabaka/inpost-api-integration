<?php

namespace Domain;

class Dimension
{
    public float $length;
    public float $width;
    public float $height;
    public string $unit;

    public function __construct(
        float $length,
        float $width,
        float $height,
        string $unit
    )
    {
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->unit = $unit;
    }
}