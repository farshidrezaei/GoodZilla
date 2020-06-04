<?php

namespace App\Providers;

use App\Services\Responder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use App\Article;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Relation::morphMap( [
                                'articles' => Article::class,
                            ] );


        $this->app->singleton( 'responder', static function () {
            return new Responder();
        } );

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
