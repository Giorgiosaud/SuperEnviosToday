<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Rate::class, function (Faker $faker) {
    return [
        'currency_id' => function () {
            $currency = factory(\App\Currency::class)->create();

            return $currency->id;
        },
        'since' => Carbon::now()->subDays(rand(1,365))->subSeconds(rand(1,86400)),
        'amount' => $faker->numberBetween(3000000,60000000),
    ];
});
