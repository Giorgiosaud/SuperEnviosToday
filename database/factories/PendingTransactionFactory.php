<?php

  namespace Database\Factories;

  use App\Models\Account;
  use App\Models\PendingTransaction;
  use App\Models\Transaction;
  use App\Models\User;
  use Illuminate\Database\Eloquent\Factories\Factory;

  class PendingTransactionFactory extends Factory
  {
    protected $model = PendingTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      return [
        'client_id' => function () {
          return User::factory()->create()->id;
        },
        'operator_account_id' => function () {
          return Account::factory()->create()->id;
        },
        'receiver_account_id' => function () {
          return Account::factory()->create()->id;
        },
        'venezuelan_operator_account_id' => function () {
          return Account::factory()->create()->id;
        },
        'rate' => $this->faker->randomFloat(4, 4, 5),
        'amount' => $this->faker->numberBetween(0, 1000000),
        'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        'operator_id' => function () {
          return User::factory()->create()->id;
        },
        'receiver_id' => function () {
          return User::factory()->create()->id;
        },
          'venezuelan_operator_id' => function () {
              return User::factory()->create()->id;
          },
        'transaction_number' => function () {
          return Transaction::factory()->create()->id;
        }
      ];
    }
  }
