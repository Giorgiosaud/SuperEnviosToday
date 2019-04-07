<?php

    namespace Tests\Feature;

    use App\Currency;
    use Tests\TestCase;
    use Illuminate\Foundation\Testing\RefreshDatabase;


    /**
     * Class UserTest
     * @package Tests\Feature
     */
    class CurrenciesTest extends TestCase
    {
        use RefreshDatabase;


        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function onlyCoordinatorCanGetCurrencies()
        {
            $this->getJson(route('currencies'))
                ->assertStatus(401);
            $this->actingAsChileanOperator();
            $this->getJson(route('currencies'))
                ->assertStatus(403);
            $this->actingAsVenezuelanOperator();
            $this->getJson(route('currencies'))
                ->assertStatus(403);
            $this->actingAsClient();
            $this->getJson(route('currencies'))
                ->assertStatus(403);
            $this->actingAsCoordinator();
            $this->getJson(route('currencies'))
                ->assertStatus(200);
        }

        /**
         * @test
         */
        public function aCoordinatorCanStoreAndSeeCurrencies()
        {
            factory(Currency::class)->create();
            factory(Currency::class)->create(['identificator' => 'Bs']);
            $this->actingAsCoordinator();
            $this->getJson(route('currencies'))
                ->assertJsonCount(2);
            $this->getJson(route('foreign_currencies'))
                ->assertJsonCount(1);
            $curr = factory(Currency::class)->make();
            $this->postJson(route('create_currency'), $curr->toArray());
            $this->getJson(route('currencies'))
            ->assertJsonCount(3);
            $this->getJson(route('foreign_currencies'))
              ->assertJsonCount(2);
        }
    }

