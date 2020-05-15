<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Comment;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    $usersIds = \App\User::all()->pluck('id')->toArray();
    return [
        'body' => $faker->realText(mt_rand(50, 100)),
        'user_id' => $usersIds[mt_rand(0, count($usersIds)-1)],
    ];
});
