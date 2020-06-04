<?php

namespace App\Http\Controllers\Auth;

use App;
use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailCheckRequest;
use App\Http\Requests\Auth\VerificationCodeCheckRequest;
use App\Services\Responder;
use App\User;
use App\Http\Resources\User as UserResource;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;


class AuthController extends Controller
{
    use App\Traits\AuthTrait;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware( 'auth:api', [
            'except' => [
                'verify',
                'resendVerificationCode',
                'checkEmail',
                'checkVerificationCode',
            ],
        ] );
    }

    /**
     * @param EmailCheckRequest $request
     *
     * @return array
     * @throws Exception
     */
    public function checkEmail( EmailCheckRequest $request ): array
    {

        $user = $this->getOrCreateUserByEmail( $request->email );

        $this->generateVerificationCode( $user );

        $user->sendVerificationCodeEmail();


        responder()->message( trans( 'messages.check_email' ) )
            ->body( [
                'verification_code' => env( 'APP_DEBUG' )
                    ? $user->verification_code->code
                    : null,
            ] )
            ->json()
            ->send();
    }


    /**
     * Get the authenticated User.
     *
     * @param
     *
     * @return void
     */
    public function user(): void
    {
        $user = new UserResource(
            auth()->user()
                ->load( [
                    'roles:id,role_id,name,fa_name',
                    'roles.permissions:id,role_id,name,fa_name',
                    'wallet',
                ] ) );

        responder()->body( [ 'user' => $user ] )->json()->send();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param
     *
     * @return void
     */
    public function logout(): void
    {
        auth()->logout();
        responder()->message( 'Successfully logged out' )->json()->send();
    }

    /**
     * Refresh a token.
     *
     * @param
     *
     * @return void
     */
    public function refresh(): void
    {
        responder()->message( 'شما با موفقیت وارد سایت شدید.' )
            ->body( [
                'access_token' => auth()->refresh(),
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ] )
            ->json()
            ->send();

    }

    /**
     * Get the token array structure.
     *
     * @param EmailCheckRequest $request
     *
     * @return void
     * @throws Exception
     */
    public function resendVerificationCode( EmailCheckRequest $request ): void
    {

        $user = $this->getUser( $request );
        $this->abortIfVerificationCodeHasNotBeenExpired( $user );
        $this->generateVerificationCode( $user );
        $user->save();
        $user->sendVerificationCodeEmail();
        responder()->message( app()->getLocale() === 'fa' ? 'ایمیلی حاوی کد تایید جدید، مجددا برای شما ارسال شد.' : 'New verification code has sent to your email.' )
            ->json()
            ->send();

    }

    /**
     * @param VerificationCodeCheckRequest $request
     *
     * @return void
     */
    public function checkVerificationCode( VerificationCodeCheckRequest $request ): void
    {
        $user = $this->getUser( $request );

        $this->checkVerificationCodeAndLogin( $request, $user );
    }


}
