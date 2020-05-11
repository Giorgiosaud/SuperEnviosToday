<?php

/** @var Factory $factory */

use App\Currency;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Currency::class, function (Faker $faker) {
    return [
        'name'          => $faker->name,
        'identifier' => $faker->userName,
        'sign'          => $faker->slug,
    ];
});
