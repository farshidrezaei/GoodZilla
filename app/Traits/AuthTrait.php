<?php


namespace App\Traits;


use App\Helpers\NumberHelper;
use App\Http\Requests\Auth\EmailCheckRequest;
use App\Http\Requests\Auth\VerificationCodeCheckRequest;
use App\Services\Responder;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait AuthTrait
{
    protected function getOrCreateUserByEmail( $email )
    {
        return User::where( 'email', $email )
            ->firstOr( static function () use ( $email ) {
                $user = User::create( [ 'email' => $email ] );
                $user->timestamps = false;
                $user->assignRole( 'simple' );
                return $user;
            } );
    }

    /**
     * @param $user
     *
     * @throws \Exception
     */
    protected function generateVerificationCode( User $user ): void
    {
        $user->verification_code()
            ->updateOrCreate( [ 'user_id' => $user->id ], [
                'code' => ( new NumberHelper )->verificationCodeGenerator(),
                'expired_at' => now()->addMinutes( 2 ),
            ] );
    }


    /**
     * @param VerificationCodeCheckRequest|EmailCheckRequest $request
     *
     * @return User|Builder|Model
     */
    protected function getUser( $request )
    {
        return User::whereEmail( $request->email )->firstOrFail();
    }


    protected function checkVerificationCodeAndLogin( VerificationCodeCheckRequest $request, $user ): void
    {
        if ( $this->verificationCodeExpired( $user ) ) {
            responder()->message( app()->getLocale() === 'fa' ? 'کد تایید منقضی شده است.' : 'Verification code has expired.' )
                ->status( 401 )
                ->json()
                ->send();
        }

        if ( $this->isValidVerificationCode( $request, $user ) ) {
            $this->verifyUserEmail( $user );
            $this->handleUserLogin( $user );
        }
        responder()->message( app()->getLocale() === 'fa' ? 'کد تایید اشتباه است.' : 'Verification code is invalid.' )
            ->status( 401 )
            ->json()
            ->send();

    }

    /**
     * @param $user
     *
     * @return bool
     */
    protected function verificationCodeExpired( $user ): bool
    {
        return $user->verification_code->expired_at < now();
    }

    /**
     * @param VerificationCodeCheckRequest $request
     * @param                              $user
     *
     * @return bool
     */
    protected function isValidVerificationCode( VerificationCodeCheckRequest $request, $user ): bool
    {
        return mb_strtoupper( $request->verification_code ) === $user->verification_code->code;
    }

    /**
     * @param $user
     */
    protected function verifyUserEmail( User $user ): void
    {
        $user->timestamps = false;
        $user->email_verified_at = now();
        $user->save();
    }

    /**
     * @param           $user
     */
    protected function handleUserLogin( $user ): void
    {

        if ( !$token = auth()->login( $user ) ) {
            responder()->message( app()->getLocale() === 'fa' ? 'خطا' : 'Error' )
                ->status( 500 )
                ->json()
                ->send();
        }


        responder()->message( 'شما با موفقیت وارد سایت شدید.' )
            ->body( [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ] )
            ->status( 200 )
            ->json()
            ->send();
    }

    /**
     * @param           $user
     */
    protected function abortIfVerificationCodeHasNotBeenExpired( $user ): void
    {

        if ( $user->verification_code->expired_at > now() ) {
            responder()->message( app()->getLocale() === 'fa' ? 'کد قبلی هنوز منقضی نشده است.' : 'Last Verification Code has not been expired.' )
                ->status( 403 )
                ->json()
                ->send();
        }
    }

}
