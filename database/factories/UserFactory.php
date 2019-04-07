<?php

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


    /** @global \Faker\Generator $factory */

    $factory->define(App\User::class, function (Faker $faker) {
        return [
            'name' => $faker->name,
            'last_name' => $faker->lastName,
            'email' => $faker->email,
            'idn' => $faker->unique()->numberBetween(1000000, 30000000),
            'idn_type' => $faker->randomElement(['DNI', 'RUT', 'CI', 'PASSPORT', 'RIF']),
            'email_verified_at' => now(),
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
            'address' => $faker->address(),
            'phone' => $faker->e164PhoneNumber(),
            'remember_token' => Str::random(10),
        ];
    });
