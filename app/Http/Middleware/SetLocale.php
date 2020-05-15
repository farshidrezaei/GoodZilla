<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale=$request->header('locale');
        if (in_array($locale, ['en', 'fa'])) app()->setLocale($locale);
        else app()->setLocale('en');

        return $next($request);
    }
}
