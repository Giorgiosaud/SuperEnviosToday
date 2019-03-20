<?php

    use Faker\Generator as Faker;

    $factory->define(
        App\Transaction::class,
        function (Faker $faker) {
            return [
                'amount' => $faker->numberBetween(0, 1000000),
                'to_account_id' => function () {
                    $account = factory(\App\Account::class)->create();

                    return $account->id;
                },
                'from_account_id' => $faker->randomElement([function () {
                    $account = factory(\App\Account::class)->create();

                    return $account->id;
                }, null]),
                'from_client_id' => function () {
                    $user = factory(\App\User::class)->create();
                    return $user->id;
                },
                'to_receiver_id' => function () {
                    $user = factory(\App\User::class)->create();
                    return $user->id;
                },
                'chilean_operator' => function () {
                    $user = factory(\App\User::class)->create();
                    return $user->id;
                }
            ];
        }
    );
