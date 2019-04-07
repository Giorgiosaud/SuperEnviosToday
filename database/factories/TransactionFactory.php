<?php

    use App\Account;
    use App\Transaction;
    use App\User;
    use Faker\Generator as Faker;

    if (isset($factory)) {
        $factory->define(
            App\Transaction::class,
            /**
             * @param Faker $faker
             * @return array
             */ function (Faker $faker) {
            return [
            'status'=>$faker->randomElement(['pending', 'assigned', 'in_progress', 'executed', 'confirmed', 'terminated']),
            'type'=>$faker->randomElement(['income', 'outcome', 'cancelled', 'pqc']),
            'url_attachment'=>$faker->imageUrl(),
            'rate'=>$faker->numberBetween(0,50000),
            'amount' => $faker->numberBetween(0, 1000000),
                    'to_account_id' => function ()
            {
                $account = factory(Account::class)->create();

                return $account->id;
            },
                'related_transaction_id'=>$faker->randomElement([null, function () {
                return factory(Transaction::class)->create()->id;
            }]),
                    'account_id' => $faker->randomElement([function () {
                $account = factory(Account::class)->create();
                return $account->id;
            }, null]),
                    'user_id' => function ()
            {
                $user = factory(User::class)->create();
                return $user->id;
            },
                ];
            }
        );
    }
