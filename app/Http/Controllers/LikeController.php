<?php

namespace App\Http\Controllers;

use App\Like;
use Doctrine\DBAL\Driver\Mysqli\MysqliException;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    protected $like;

    public function __construct(Like $like)
    {
        $this->like = $like;
    }

    public function store(Request $request)
    {
        try {
            $this->like->query()->create([
                'user_id' => 3,
                'likeable_id' => $request->id,
                'likeable_type' => $request->type
            ]);

            return response()->json('liked');
        } catch (\Exception $exception) {
            return response()->json($exception);

        }

    }

    public function destroy(Request $request,$id)
    {
        try {
            $this
                ->like
                ->query()
                ->whereUserId(3)
                ->whereLikeableId($id)
                ->whereLikeableType($request->type)
                ->delete();
            return response()->json('disliked');

        } catch (\Exception $exception) {
            return response()->json($exception);

        }
    }
}
