<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return User[]|LengthAwarePaginator|Collection
     */
    public function index( Request $request )
    {
        $pagination = $request->items_per_page === -1 ? 0 : $request->items_per_page;

        return User::query()->with( 'roles.permissions' )->paginate( $pagination );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return void
     */
    public function store( Request $request ): void
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show( $id ): JsonResponse
    {
        $item = User::with( 'roles.permissions' )->findOrFail( $id );
        return response()->json( $item );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function update( Request $request, $id ): ?Response
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
        User::destroy( $request->ids );
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
        User::destroy( $request->ids );
        return response()->json( 'items has been Deleted' );
    }
}
