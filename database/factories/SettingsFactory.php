<?php

  namespace Database\Factories;

  use App\Models\Setting;
  use Illuminate\Database\Eloquent\Factories\Factory;

  class SettingsFactory extends Factory
  {
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      return [
        'key' => $this->faker->name,
        'value' => $this->faker->name,

        //
      ];
    }
  }