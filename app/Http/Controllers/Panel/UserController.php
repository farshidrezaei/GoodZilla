<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Collection;

class UserController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function update(Request $request)
    {
        return $request->toArray();
        $user = auth()->user();
        $user->name = 'abbas';
        $user->save();
        return $user;
    }

    public function bookmarks()
    {
        $bookmarks = auth()->user()->bookmarks->groupBy('pivot.bookmarkable_type');
        return response()->json($bookmarks);
    }

}
