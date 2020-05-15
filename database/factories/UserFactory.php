<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'username' => $faker->unique()->userName,
        'gender' => $faker->randomElement(['male', 'female', 'unknown']),
        'avatar' => 'lorempic/' . mt_rand(1, 10).'.jpg',
        'mobile' => $faker->phoneNumber,
        'profession' => $faker->jobTitle,
        'birthday' => \Carbon\Carbon::create(
            mt_rand(1990, 2020),
            mt_rand(1, 12),
            mt_rand(1, 30))->format('Y/m/d'),
        'biography' => $faker->paragraph,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'remember_token' => Str::random(10),
    ];
});
