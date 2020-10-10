<?php

  namespace Database\Factories;

  use App\Models\Account;
  use App\Models\Bank;
  use Illuminate\Database\Eloquent\Factories\Factory;

  class AccountFactory extends Factory
  {
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      return [
        'bank_id' => function () {
          $bank = Bank::factory()->create();
          return $bank->id;
        },
        'type' => $this->faker->randomElement(['corriente', 'ahorro']),
        'number' => $this->faker->bankAccountNumber(),
        'is_operator' => $this->faker->boolean(),
      ];
    }

    public function american()
    {
      return $this->state([
        'bank_id' => function () {
          $bank = Bank::factory()->states('american')->create();
          return $bank->id;
        },
        'type' => $this->faker->randomElement(['corriente', 'ahorro', null]),
        'number' => $this->faker->bankAccountNumber(),
        'is_operator' => $this->faker->boolean(),
      ]);
    }

    public function chilean()
    {
      return $this->state([
        'bank_id' => function () {
          $bank = Bank::factory()->chilean()->create();
          return $bank->id;
        },
        'type' => $this->faker->randomElement(['corriente', 'ahorro']),
        'number' => $this->faker->bankAccountNumber(),
        'is_operator' => $this->faker->boolean(),
      ]);
    }

    public function venezuelan()
    {
      return $this->state([
        'bank_id' => function () {
          $bank = Bank::factory()->venezuelan()->create();
          return $bank->id;
        },
        'type' => $this->faker->randomElement(['corriente', 'ahorro']),
        'number' => $this->faker->bankAccountNumber(),
        'is_operator' => $this->faker->boolean(),
      ]);
    }

  }