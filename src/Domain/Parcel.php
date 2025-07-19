<?php

namespace Domain;

class Parcel
{
    public string $id;

    public Dimension $dimension;

    public Weight $weight;

    public bool $isNonStandard;
}