<?php

namespace App\Http\Controllers;

use App\Article;
use App\Comment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|JsonResponse
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $expect = $request->expect;
        $take = $request->take;
        $orderBy = $request->orderBy;
        $articles = Article::query()->withCount('comments','likes');
        $articles->when($expect, function ($query, $expect) {
            return $query->whereNotIn('slug', Arr::flatten([$expect]));
        });
        $articles->when($orderBy, function ($query, $orderBy) {
            switch ($orderBy) {
                case 'latest':
                    return $query->latest();
                    break;
                case 'popular':
                    return $query->orderByDesc('likes_count');
                    break;
                case 'visit':
                case 'oldest':
                    return $query->oldest();
                    break;
            }
        });

        switch ($type) {
            case 'suggested':
            case 'popular':
            case 'latest':
                $articles = $articles->take($take);
                break;
            case 'pagination':
                return response()->json($articles->paginate($take));
                break;
        }

        $articles = $articles->get();

        return response()->json($articles);
    }

    /**
     * Display the specified resource.
     *
     * @param $slug
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function show($slug)
    {
        $item = Article::with([
            'comments' => function ($comment) {
                $comment->with(['user', 'children.user']);
            },
            'user'
        ])->whereSlug($slug)->first();
        return response()->json($item);
    }

    /**
     * like the specified resource.
     *
     * @param $id
     * @return JsonResponse|void
     */
    public function toggleLike($id)
    {
        $article = Article::findOrFail($id);
        try {
            $status = $article->likes()->toggle(auth()->user()->id);
            $result = count($status['attached']) ? true : false;

            return response()->json(['result' => $result]);
        } catch (Exception $e) {
            return response()->json($e, 500);

        }
    }


    /**
     * like the specified resource.
     *
     * @param $id
     * @return JsonResponse|void
     */
    public function toggleBookmark($id)
    {
        $article = Article::findOrFail($id);
        try {
            $status = $article->bookmarks()->toggle(auth()->user()->id);
            $result = count($status['attached']) ? true : false;

            return response()->json(['result' => $result]);
        } catch (Exception $e) {
            return response()->json($e, 500);

        }
    }

    /**
     * save comment for the specified resource.
     *
     * @param $id
     * @return JsonResponse|void
     */
    public function comment(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        try {
            $article->comments()->create([
                'user_id' => auth()->user()->id,
                'body' => $request->body
            ]);
            return response()->json(['message'=>'comment sent.']);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }


}
