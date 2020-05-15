<?php

namespace App\Http\Controllers\Auth;

use App;
use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Rules\VerificationCode;
use App\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => [
                'verify',
                'resend',
                'checkEmail',
                'checkVerificationCode',
            ]]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::whereEmail($request->email)->first();
        if (!$user) {
            $user = new User();
            $user->email = $request->email;
            $user->save();
            $user->timestamps = false;
            $user->assignRole('simple');
        }
        $user->verification_code = (new NumberHelper)->verificationCodeGenerator();
        $user->timestamps = false;
        $user->save();
        $user->sendApiEmailVerificationNotification();
        return response()->json(
            app()->getLocale() === 'fa'
                ? 'جهت تایید ایمیل خود، از طریق کد تایید ارسالی به ایمیل وارد شده اقدام نمایید.'
                : 'Please confirm your email by inserting verification code sent to you on your email',
            200);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => ['required', new VerificationCode]
        ]);



        $user = User::whereEmail($request->email)->first();
        if (!$user) {
            abort(404);
        }

        if (mb_strtoupper($request->verification_code) === $user->verification_code) {
            $user->timestamps = false;
            $user->email_verified_at = Carbon::now();
            $user->save();
            if (!$token = auth()->login($user)) {
                return response()->json([
                    'error' => app()->getLocale() === 'fa'
                        ? 'خطا'
                        : 'Error'
                ], 500);
            }
             return $this->respondWithToken($token);
        }
        return response()->json([
            'error' => app()->getLocale() === 'fa'
                ? 'کد تایید اشتباه است.'
                : 'Verification code is invalid.'
        ], 401);

    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function user()
    {
        return response()->json(['user' => auth()->user()]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::whereEmail($request->email)->first();
        if (!$user) {
            abort(404);
        }

        $user->verification_code = (new NumberHelper)->verificationCodeGenerator();
        $user->save();
        $user->sendApiEmailVerificationNotification();
        return response()->json(
            app()->getLocale() === 'fa'
                ? 'ایمیلی حاوی کد تایید جدید، مجددا برای شما ارسال شد.'
                : 'New verification code has sent to your email.',
            200);

    }


    public function verify(Request $request)
    {

    }
}
