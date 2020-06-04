<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{


    public function update( Request $request ): ?array
    {
        return $request->toArray();
        $user = auth()->user();
        $user->name = 'abbas';
        $user->save();

        responder()->body( $user )
            ->json()
            ->send();
    }

    public function bookmarks(): void
    {
        $bookmarks = auth()->user()->bookmarks->groupBy( 'pivot.bookmarkable_type' );
        responder()->message( 'User Bookmarks' )
            ->body( [ 'bookmarks' => $bookmarks ] )
            ->json()
            ->send();
    }

}
