<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Article;
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {
    $usersIds = \App\User::all()->pluck('id')->toArray();
    return [
        'user_id' => $usersIds[mt_rand(0, count($usersIds) - 1)],
        'title' => $faker->sentence(2),
        'slug' => $faker->unique()->sentence(2),
        'body' => $faker->text(mt_rand(150, 300)),
        'published_at' => now()->subDays(2),
        'image' => 'lorempic/' . mt_rand(1, 10).'.jpg',
    ];
});
