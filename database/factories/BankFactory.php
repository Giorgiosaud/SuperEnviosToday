<?php

use Faker\Generator as Faker;

$factory->define(App\Bank::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['Orinoco', 'Caroni', 'Banesco', 'Provincial', 'Santander', 'Banco Estado'])
        
    ];
});
