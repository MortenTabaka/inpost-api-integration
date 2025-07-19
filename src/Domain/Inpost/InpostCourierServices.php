<?php

namespace Domain\Inpost;

enum InpostCourierServices
{
    /**
     * Przesyłka kurierska standardowa
     */
    public const string INPOST_COURIER_STANDARD = 'inpost_courier_standard';

    /**
     * Przesyłka kurierska z doręczeniem do 10:00
     */
    public const string INPOST_COURIER_EXPRESS_1000 = 'inpost_courier_express_1000';

    /**
     * Przesyłka kurierska z doręczeniem do 12:00
     */
    public const string INPOST_COURIER_EXPRESS_1200 = 'inpost_courier_express_1200';

    /**
     * Przesyłka kurierska z doręczeniem do 17:00
     */
    public const string INPOST_COURIER_EXPRESS_1700 = 'inpost_courier_express_1700';

    /**
     * Przesyłka kurierska Paleta Standard
     */
    public const string INPOST_COURIER_PALETTE = 'inpost_courier_palette';

    /**
     * Przesyłka kurierska eSmartMix z elektronicznym obiegiem dokumentów i potwierdzeniem danych odbiorcy
     */
    public const string INPOST_COURIER_ALCOHOL = 'inpost_courier_alcohol';
}
