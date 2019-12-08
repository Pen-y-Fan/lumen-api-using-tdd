<?php

use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});


$factory->define(App\Product::class, function (Faker\Generator $faker) {

    $name = $faker->sentence(3);

    return [
        'name'  => $name,
        'slug'  => Str::slug($name),
        'price' => random_int(10, 100),
    ];
});
