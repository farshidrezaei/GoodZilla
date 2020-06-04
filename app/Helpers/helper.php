<?php

use App\Services\Responder;

if ( !function_exists( 'responder' ) ) {
    /**
     * Return a new response from the application.
     *
     * @return Responder
     */
    function responder()
    {
        return App::make( Responder::class );
    }
}
