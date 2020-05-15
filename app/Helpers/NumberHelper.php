<?php


namespace App\Helpers;


class NumberHelper
{
    public function verificationCodeGenerator()
    {
        // Available alpha characters
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // generate a pin based on 2 * 7 digits + a random character
        $pin = mt_rand(100, 999)
            . $characters[rand(0, strlen($characters) - 1)]
            . $characters[rand(0, strlen($characters) - 1)]
            . $characters[rand(0, strlen($characters) - 1)];
        // shuffle the result
        return str_shuffle($pin);
    }
}
