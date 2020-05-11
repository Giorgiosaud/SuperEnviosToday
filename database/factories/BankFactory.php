<?php

/** @var Factory $factory */

use App\Bank;
use App\Currency;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;


$factory->state(Bank::class, 'venezuelan', function ($faker) {
    return [
        'name' => $faker->randomElement(['Orinoco', 'Caroni', 'Banesco', 'Provincial', 'Santander']),
        'currency_id' => function () {
            $currency = Currency::whereIdentificator('BsS')->first();
            if (!$currency) {
                $currency = factory(Currency::class)->create([
                    'identifier' => 'BsS'
                ]);
            }
            return $currency->id;
        },
    ];
});
$factory->state(Bank::class, 'chilean', function ($faker) {
    return [
        'name' => $faker->randomElement(['Santander', 'Banco Estado', 'Itau', 'Banco de Chile', 'BCI']),
        'currency_id' => function () {
            $currency = Currency::whereIdentificator('CLP')->first();
            if (!$currency) {
                $currency = factory(Currency::class)->create([
                    'identifier' => 'CLP'
                ]);
            }
            return $currency->id;
        },
    ];
});

$factory->state(Bank::class, 'american', function ($faker) {
    return [
        'name' => $faker->randomElement(['Bank of America', 'Chase', 'Banco de la Florida', 'Santander International', 'BCI Miami']),
        'currency_id' => function () {
            $currency = Currency::whereIdentificator('USD')->first();
            if (!$currency) {
                $currency = factory(Currency::class)->create([
                    'identifier' => 'USD'
                ]);
            }
            return $currency->id;
        },

    ];
});

$factory->define(Bank::class, function (Faker $faker) {
    return [
        'name' => $faker->
        randomElement([
            'Orinoco',
            'Caroni',
            'Banesco',
            'Provincial',
            'Santander',
            'Bank of America',
            'Chase',
            'Banco de la Florida',
            'Santander International',
            'BCI Miami', 'Santander',
            'Banco Estado',
            'Itau',
            'Banco de Chile',
            'BCI'
        ]),

        'currency_id' => function () {
            $currency = factory(Currency::class)->create();
            return $currency->id;
        },
    ];
});
