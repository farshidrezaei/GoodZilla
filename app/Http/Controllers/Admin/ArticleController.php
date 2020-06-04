<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return Article[]|LengthAwarePaginator|Collection
     */
    public function index( Request $request )
    {
        $pagination = $request->items_per_page === -1 ? 0 : $request->items_per_page;

        return Article::query()->with( 'user' )->paginate( $pagination );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param ArticleRequest $request
     *
     * @return Article|array|Model
     */
    public function store( ArticleRequest $request )
    {
        return Article::create( $request->all() );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return JsonResponse|Response
     */
    public function show( $id )
    {
        $item = Article::with( [
            'comments' => static function ( MorphMany $comment ) {
                $comment->with( [ 'user', 'children.user' ] );
            },
            'user',
        ] )->findOrFail( $id );
        return response()->json( $item );
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return void
     */
    public function update( Request $request, $id ): void
    {
        //
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function delete( Request $request ): JsonResponse
    {
        Article::destroy( $request->ids );
        return response()->json( 'items has been Deleted' );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function destroy( Request $request ): JsonResponse
    {
        Article::destroy( $request->ids );
        return response()->json( 'items has been Deleted' );
    }
}
