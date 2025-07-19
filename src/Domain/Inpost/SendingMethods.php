<?php

namespace Domain\Inpost;

enum SendingMethods
{
    /**
     * Nadanie w automacie Paczkomat
     */
    public const PARCEL_LOCKER = 'parcel_locker';

    /**
     * Nadanie w POK
     */
    public const POK = 'pok';

    /**
     * Nadanie w POP
     */
    public const POP = 'pop';

    /**
     * Nadanie w POK
     */
    public const COURIER_POK = 'courier_pok';

    /**
     * Nadanie w Oddziale
     */
    public const BRANCH = 'branch';

    /**
     * Odbiór przez Kuriera
     */
    public const DISPATCH_ORDER = 'dispatch_order';

    /**
     * Nadanie w dowolnym punkcie
     */
    public const ANY_POINT = 'any_point';
}
