<?php

    namespace Tests\Feature;

    use App\Account;
    use App\Role;
    use App\User;
    use Tests\TestCase;
    use Illuminate\Foundation\Testing\RefreshDatabase;

    /**
     * Class UserTest
     * @package Tests\Feature
     */
    class TransactionsTest extends TestCase
    {
        use RefreshDatabase;


        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aCoordinatorCanAddFundsToVenezuelanOperator()
        {
            $this->actingAsCoordinator();
            $venezuelan_operator=factory(User::class)->create();
            $venezuelan_operator->setRole('venezuelan_operator');
            $venezuelan_operator->fresh();
            $venezuelan_account=factory(Account::class)->create(['user_id'=>$venezuelan_operator->id,'is_operator_account'=>true,'number'=>'123123']);
            $reponse=$this->postJson('api/transaction-to-venezuelan-operator',["amount"=>"10000000000",'to_account_id'=>$venezuelan_account->id]);
            $venezuelan_account->refresh();
           $this->assertEquals( 1000000, $venezuelan_account->balance);

        }
    }

