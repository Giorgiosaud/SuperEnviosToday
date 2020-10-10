<?php

  namespace Database\Factories;

  use App\Models\Bank;
  use App\Models\Currency;
  use Illuminate\Database\Eloquent\Factories\Factory;

  class BankFactory extends Factory
  {
    protected $model = Bank::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      return [
        'name' => $this->faker->randomElement([
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
          $currency = Currency::factory()->create();
          return $currency->id;
        },
      ];
    }

    public function venezuelan()
    {
      return $this->state([
        'name' => $this->faker->randomElement(['Orinoco', 'Caroni', 'Banesco', 'Provincial', 'Santander']),
        'currency_id' => function () {
          $currency = Currency::whereIdentificator('BsS')->first();
          if (!$currency) {
            $currency = Currency::factory()->create([
              'identifier' => 'BsS'
            ]);
          }
          return $currency->id;
        },

      ]);
    }

    public function chilean()
    {

      return $this->state(function (array $attributes) {
          return [
              'name' => $this->faker->randomElement(['Santander', 'Banco Estado', 'Itau', 'Banco de Chile', 'BCI']),
              'currency_id' => function () {
                  $currency = Currency::whereIdentifier('CLP')->first();
                  if (!$currency) {
                      $currency = Currency::factory()->create([
                          'identifier' => 'CLP'
                      ]);
                  }
                  return $currency->id;
              },
          ];
      });
    }

    public function american()
    {
      return $this->state([
        'name' => $this->faker->randomElement(['Bank of America', 'Chase', 'Banco de la Florida', 'Santander International', 'BCI Miami']),
        'currency_id' => function () {
          $currency = Currency::whereIdentificator('USD')->first();
          if (!$currency) {
            $currency = Currency::factory()->create([
              'identifier' => 'USD'
            ]);
          }
          return $currency->id;
        },
      ]);
    }

  }
