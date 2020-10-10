<?php

  namespace Database\Factories;

  use App\Models\Account;
  use App\Models\Transaction;
  use App\Models\User;
  use Illuminate\Database\Eloquent\Factories\Factory;

  class TransactionFactory extends Factory
  {
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      return [
        'account_id' => function () {
            $account = Account::factory()->create();
            return $account->id;
        },
        'client_id' => $this->faker->randomElement([function () {
            return User::factory()->create()->id;
        }, null]),
        'operator_id' => function () {
            return User::factory()->create()->id;
        },
        'track_number' => $this->faker->randomElement([null, 0]),
        'bank_reference' => $this->faker->numberBetween(1000, 1000000),
        'amount' => $this->faker->numberBetween(0, 1000000),
        'status' => $this->faker->randomElement(['executed', 'pending', 'in-progress']),
    ];
    }
  }

