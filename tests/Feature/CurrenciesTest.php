<?php

namespace Tests\Feature;

use App\Account;
use App\Bank;
use App\Currency;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UserTest.
 */
    class CurrenciesTest extends TestCase
    {
        use RefreshDatabase;

        /**
         * A basic test example.
         *
         * @test
         *
         * @return void
         */
        public function onlyCoordinatorCanGetCurrencies()
        {
            $this->getJson(route('currencies'))
                ->assertStatus(401);
            $this->actingAsForeignOperator();
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

        /**
         * @test
         */
        public function whenCurrencyIsCreatedABankWithSameCurrencyIsAlsoCreated()
        {
            $this->withoutExceptionHandling();
            $currency = factory(Currency::class)->create();
            $this->assertEquals($currency->fresh(), Bank::first()->currency);
        }

        /**
         * @test
         */
        public function allForeignAndCoordinatorsHaveACashAccountInAveryCurrencyOnCreated()
        {
            $users = factory(User::class, 10)->create();
            $users[0]->setRole('coordinator');
            $users[1]->setRole('foreign_operator');
            $users[2]->setRole('foreign_operator');
            factory(Currency::class)->create();

            $users = User::all();
            foreach ($users as $user) {
                if ($user->hasRole('coordinator') || $user->hasRole('foreign_operator')) {
                    $this->assertInstanceOf(Account::class, $user->accounts[0]);
                    $this->assertEquals($user->accounts[0]->bank->id, Bank::first()->id);
                }
            }
        }
    }
