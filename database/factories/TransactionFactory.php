<?php

    use App\Account;
    use App\Attachment;
    use App\Transaction;
    use Faker\Generator as Faker;

    if (isset($factory)) {
        $factory->define(
            App\Transaction::class,
            /**
             * @param Faker $faker
             * @return array
             */ function (Faker $faker) {
            return [
                'from_account_id' => $faker->randomElement([function () {
                    $account = factory(Account::class)->create();
                    return $account->id;
                }, null]),
                'to_account_id' => function () {
                    $account = factory(Account::class)->create();
                    return $account->id;
                },
                'related_transaction_id' => $faker->randomElement([null, function () {
                    return factory(Transaction::class)->create()->id;
                }]),
                'amount' => $faker->numberBetween(0, 1000000),
                'attachment_id' => $faker->randomElement([null, function () {
                    return factory(Attachment::class)->create()->id;
                }]),
                'status' => $faker->randomElement(['pending', 'assigned', 'in_progress', 'executed', 'confirmed', 'terminated']),
                'type' => $faker->randomElement(['income', 'outcome', 'cancelled', 'pqc']),
            ];
        }
        );
    }
