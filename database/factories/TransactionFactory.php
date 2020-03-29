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
 *
 * @return array
 */ function (Faker $faker) {
    return [
                'client_id'=> $faker->randomElement([function () {
                    return factory(User::class)->create()->id;
                }, null]),
                'from_account_id' => $faker->randomElement([function () {
                    $account = factory(Account::class)->create();
                    return $account->id;
                }, null]),
                'to_account_id' => function () {
                    $account = factory(Account::class)->create();
                    return $account->id;
                },
                'related_transaction_id' => $faker->randomElement([null, 0]),
                'transaction_number'=>$faker->numberBetween(1000,1000000),
                'amount' => $faker->numberBetween(0, 1000000),
                'status' => $faker->randomElement(['pending', 'assigned', 'in_progress', 'executed', 'confirmed', 'terminated']),
                'type'   => $faker->randomElement(['income', 'outcome', 'cancelled', 'pqc']),
                'from_user_id'=>function () {
                    return factory(User::class)->create()->id;
                },
                'to_user_id'=>function () {
                    return factory(User::class)->create()->id;
                }
            ];
}
        );
    }
