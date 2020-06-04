<?php


namespace App\Helpers;


use Exception;

class NumberHelper
{
    public function verificationCodeGenerator(): ?string
    {
        // Available alpha characters
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        try {
            $pin = random_int( 100, 999 )
                . $characters[ random_int( 0, strlen( $characters ) - 1 ) ]
                . $characters[ random_int( 0, strlen( $characters ) - 1 ) ]
                . $characters[ random_int( 0, strlen( $characters ) - 1 ) ];
            return str_shuffle( $pin );
        }
        catch ( Exception $e ) {
            throw $e;
        }
        // shuffle the result

    }
}
