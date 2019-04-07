<?php

    namespace Tests\Feature;

    use App\Currency;
    use App\Rate;
    use Carbon\Carbon;
    use Tests\TestCase;
    use Illuminate\Foundation\Testing\RefreshDatabase;

    /**
     * Class UserTest
     * @package Tests\Feature
     */
    class RatesTest extends TestCase
    {
        use RefreshDatabase;

        /**
         * @test
         */
        public function anyoneCanSeLastRate(){
            factory(Rate::class)->create(['since'=>Carbon::yesterday()]);
            $rate2=factory(Rate::class)->create(['amount'=>'199','since'=>Carbon::now()]);
            $this->getJson(route('last_rate',$rate2->currency_id))
                ->assertJson($rate2->toArray());
        }
        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aNonCordinatorCantSeeRates()
        {
            $this->actingAsClient();
            $this->get(route('rates'))
                ->assertStatus(403);
            $this->actingAsChileanOperator();
            $this->get(route('rates'))
                ->assertStatus(403);
            $this->actingAsVenezuelanOperator();
            $this->get(route('rates'))
                ->assertStatus(403);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aClientCantAddRates()
        {
            $this->actingAsClient();
            $rate = factory(Rate::class)->make(['currency' => factory(Currency::class)->create()->toArray()]);
            $this->post(route('create_rate'), $rate->toArray())
                ->assertStatus(403);
            $this->actingAsVenezuelanOperator();
            $this->post(route('create_rate'), $rate->toArray())
                ->assertStatus(403);
            $this->actingAsChileanOperator();
            $this->post(route('create_rate'), $rate->toArray())
                ->assertStatus(403);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aCoordinatorCanAddRates()
        {
            $this->actingAsCoordinator();
            $response = $this->get(route('rates'));
            $response->assertJsonCount(0, $key = 'data');
            $rate = factory(Rate::class)->make(['currency' => factory(Currency::class)->create()->toArray()]);
            $this->post(route('create_rate'), $rate->toArray());
            $response = $this->getJson(route('rates')); //$response->fresh()->dump();
            $response->assertJsonCount(1, $key = 'data');
        }

    }

