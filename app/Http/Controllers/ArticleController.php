<?php

namespace App\Http\Controllers;

use App\Article;
use App\Comment;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return LengthAwarePaginator|JsonResponse
     */
    public function index( Request $request )
    {
        $articles = Article::withCount( [ 'comments', 'likes' ] );

        $articles->when( $request->expect, static function ( Builder $query, $expect ) {
            return $query->whereNotIn( 'slug', Arr::flatten( [ $expect ] ) );
        } );

        $articles->when( $request->orderBy, static function ( Builder $query, $orderBy ) {
            switch ( $orderBy ) {
                case 'latest':
                    return $query->latest();
                    break;
                case 'popular':
                    return $query->orderByDesc( 'likes_count' );
                    break;
                case 'visit':
                case 'oldest':
                    return $query->oldest();
                    break;
            }
        } );

        switch ( $request->type ) {
            case 'suggested':
            case 'popular':
            case 'latest':
                break;
            case 'pagination':
                return response()->json( $articles->paginate( $request->take ) );
                break;
        }

        $articles = $articles->take( $request->take )->get();

        return response()->json( $articles );
    }

    /**
     * Display the specified resource.
     *
     * @param $slug
     *
     * @return JsonResponse|Response
     */
    public function show( $slug )
    {
        $item = Article::with( [
            'comments' => static function ( $comment ) {
                $comment->with( [ 'user', 'children.user' ] );
            },
            'user',
        ] )->whereSlug( $slug )->first();
        return response()->json( $item );
    }

    /**
     * like the specified resource.
     *
     * @param $id
     *
     * @return JsonResponse|void
     */
    public function toggleLike( $id )
    {
        $article = Article::findOrFail( $id );
        try {
            $status = $article->likes()->toggle( auth()->user()->id );
            $result = count( $status[ 'attached' ] ) ? true : false;

            return response()->json( [ 'result' => $result ] );
        }
        catch ( Exception $e ) {
            return response()->json( $e, 500 );

        }
    }


    /**
     * like the specified resource.
     *
     * @param $id
     *
     * @return JsonResponse|void
     */
    public function toggleBookmark( $id )
    {
        $article = Article::findOrFail( $id );
        try {
            $status = $article->bookmarks()->toggle( auth()->user()->id );
            $result = count( $status[ 'attached' ] ) ? true : false;

            return response()->json( [ 'result' => $result ] );
        }
        catch ( Exception $e ) {
            return response()->json( $e, 500 );

        }
    }

    /**
     * save comment for the specified resource.
     *
     * @param Request $request
     * @param         $id
     *
     * @return JsonResponse|void
     */
    public function comment( Request $request, $id )
    {
        $article = Article::findOrFail( $id );
        try {
            $article->comments()->create( [
                'user_id' => auth()->user()->id,
                'body' => $request->body,
            ] );
            return response()->json( [ 'message' => 'comment sent.' ] );
        }
        catch ( Exception $e ) {
            return response()->json( $e, 500 );
        }
    }


}
