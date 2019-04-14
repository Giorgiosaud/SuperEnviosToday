<?php

    use App\Currency;
    use Faker\Generator as Faker;

    if (isset($factory)) {
        $factory->define(Currency::class, function (Faker $faker) {
            return [
                'name' => $faker->name,
                'identificator' => $faker->userName,
                'sign' => $faker->slug,
            ];
        });
    }
