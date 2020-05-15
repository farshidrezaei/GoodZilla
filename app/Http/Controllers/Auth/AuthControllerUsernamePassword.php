<?php

namespace App\Http\Controllers\Auth;

use App;
use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
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
        $this->middleware('auth:api', ['except' => ['login', 'register','verify','resend']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     * @param $locale
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {

        $credentials = $request->only(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'error' => app()->getLocale() === 'fa'
                    ? 'ایمیل یا کلمه عبور اشتباه است.'
                    : 'Email or password is not correct.'
            ], 401);
        }
        if (auth()->user()->email_verified_at !== NULL) return $this->respondWithToken($token);
        else return response()->json([
            'error' => app()->getLocale() === 'fa'
                ? 'ایمیل شما هنوز تایید نشده است. برای تایید ایمیل خود، از پیامی که از طرف سایت برای شما ارسال شده است اقدام نمایید.'
                : 'Email is not verified. please verify your email.'
        ], 401);
    }


    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_code' => (new NumberHelper)->verificationCodeGenerator()
        ]);
        $user->assignRole('simple');
        $user->sendApiEmailVerificationNotification();
        return response()->json(
            app()->getLocale() === 'fa'
                ? 'حساب شما با موفقیت ایجاد شد. جهت تایید ایمیل خود، از طریق پیام ارسال شده به ایمیل وارد شده اقدام نمایید.'
                : 'Please confirm yourself by clicking on verify user button sent to you on your email',
            200);

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
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('User already have verified email!', 422);
        }
        $request->user()->sendEmailVerificationNotification();
        return response()->json('The notification has been resubmitted');
    }


    public function verify(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->email_verified_at = Carbon::now();
        $user->save();
        return response()->json('The email has been Verified.');

    }
}
