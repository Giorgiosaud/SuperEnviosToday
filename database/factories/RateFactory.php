<?php

  namespace Database\Factories;

  use App\Models\Currency;
  use App\Models\Rate;
  use Carbon\Carbon;
  use Illuminate\Database\Eloquent\Factories\Factory;

  class RateFactory extends Factory
  {
  protected $model = Rate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
       return [
        'currency_id' => function () {
            $currency = Currency::factory()->create();

            return $currency->id;
        },
        'since'  => Carbon::now()->subDays(rand(1, 365))->subSeconds(rand(1, 86400)),
        'amount' => $this->faker->randomFloat(4, 4, 5),
    ];
    }
  }
