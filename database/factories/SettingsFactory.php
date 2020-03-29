<?php

    use App\Setting;
    use Faker\Generator as Faker;

    if (isset($factory)) {
        $factory->define(Setting::class, function (Faker $faker) {
            return [
                'key'  => $faker->name,
                'value'=> $faker->name,

                //
            ];
        });
    }
