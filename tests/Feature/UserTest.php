<?php

    namespace Tests\Feature;

    use App\Currency;
    use App\Events\RegisteredOperator;
    use App\User;
    use Illuminate\Support\Facades\Event;
    use Tests\TestCase;
    use Illuminate\Foundation\Testing\RefreshDatabase;


    /**
     * Class UserTest
     * @package Tests\Feature
     * @property User $user
     */
    class UserTest extends TestCase
    {
        use RefreshDatabase;

        private $user;

        /**
         *
         */
        protected function createUser()
        {
            $this->user = factory(User::class)->create();
            $this->user->refresh();
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aUsersAPIWORKS()
        {
            factory(User::class, 10)->create();
            $this->getJson('/api/users')->assertStatus(401);
            $this->actingAsCoordinator();
            $response = $this->get('/api/users')->assertStatus(200);
            $response->assertJsonCount(11, $key = 'data');

        }

        /**
         * @test
         */
        public function rolesAPIWorks()
        {
            $this->actingAsCoordinator();
            $response = $this->getJson('/api/roles');
            $response->assertJsonCount(5);
        }

        /**
         * @test
         */
        public function anVenezuelanOperatorListIsShown()
        {
            $users = factory(User::class, 20)->create();
            /** @var User $users */
            foreach ($users as $user) {
                $user->toogleRole('venezuelan_operator');
            }
            $this->actingAsCoordinator();
            $response = $this->get('/api/operadores-venezuela');
            $response->assertJsonCount(20);

        }

        /**
         * @test
         */
        public function CoordinatorAndChileanOperatorsCanSeeVenezuelanOperators()
        {
            $this->actingAsCoordinator();
            $users = factory(User::class, 30)->create();
            /** @var User $users */
            foreach ($users as $user) {
                $user->setRole('venezuelan_operator');
            }
            $this->getJson(route('venezuelan_operators_api'))
                ->assertJsonCount(30);

        }

        /**
         * @test
         */
        public function whenOperatorIsRegisteredThrowEventRegisteredOperator()
        {
            $this->actingAsCoordinator();
            Event::fake();
            $this->postJson('api/registerMember', [
                "address" => "avenida",
                "email" => "jorgelsaud@gmail.com",
                "email_confirmation" => "jorgelsaud@gmail.com",
                "idn" => "263215982",
                "idn_type" => "CI",
                "last_name" => "bruces",
                "name" => "Dea",
                "password" => "123123123",
                "password_confirmation" => "123123123",
                "phone" => "123123123",
                "roles" => ["venezuelan_operator"],
            ]);
            Event::assertDispatched(RegisteredOperator::class);
        }

        /**
         * @test
         */
        public function aClientUserIsPromotedAsCoordinatorAndHaveCashAccountsInAllCurrencies()
        {
            factory(Currency::class, 4)->create();
            $this->createUser();
            $this->user->refresh();
            $this->assertTrue($this->user->hasRole('client'));
            $this->assertFalse($this->user->hasRole('coordinator'));
            $this->user->setRole('coordinator');
            $this->user->refresh();
            $this->user->update(['name' => 'coordinato2r']);
            $this->user->refresh();
            $this->assertEquals($this->user->name, 'coordinato2r');
            $this->assertTrue($this->user->hasRole('coordinator'));
            $this->assertCount(4, $this->user->accounts);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function onlyACoordinatorOrForeignOperatorCanAskForclientData()
        {
            $this->createUser();
            $query="api/user_data?idn=".$this->user->idn."&idn_type=".$this->user->idn_type;
            $this->getJson($query)
                ->assertStatus(401);
            $this->actingAsCoordinator();
            $this->getJson($query)
            ->assertStatus(200);
            $this->actingAsForeignOperator();
            $this->getJson($query)
            ->assertStatus(200);
            $this->actingAsVenezuelanOperator();
            $this->getJson($query)
            ->assertStatus(403);
            $this->actingAsClient();
            $this->getJson($query)
            ->assertStatus(403);
            $this->actingAsReceiver();
            $this->getJson($query)
            ->assertStatus(403);
        }
    }

