<?php

namespace AlexanderA2\PhpDatasheet\Helper;

class StringHelper
{
    static public function toReadable($string): string
    {
        // remove everything, except letters, numbers and spaces
        $string = preg_replace('/[^a-zA-Z\d\s]/', ' ', $string);
        // 'SuperEXTRAString' -> 'Super EXTRA String'
        $string = preg_replace('/[A-Z]([A-Z](?![a-z]))*/', ' $0', $string);
        // remove multiply spaces and trim
        $string = trim(preg_replace('/\s{1,}/', ' ', $string));
        // lowercase everything, uppercase only first letter
        $string = ucfirst(mb_strtolower($string));

        return $string;
    }
}