<?php

    namespace Tests\Feature;

    use App\Account;
    use App\Attachment;
    use App\PendingTransaction;
    use App\Rate;
    use App\Setting;
    use App\Transaction;
    use App\User;
    use Carbon\Carbon;
    use Tests\TestCase;
    use Illuminate\Foundation\Testing\RefreshDatabase;

    /**
     * Class UserTest
     * @package Tests\Feature
     * @property Account $venezuelan_account
     */
    class TransactionsTest extends TestCase
    {
        use RefreshDatabase;
        protected $venezuelan_operator;
        protected $venezuelan_account;

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aCoordinatorCanAddFundsToVenezuelanOperator()
        {
            $this->actingAsCoordinator();
            $this->addVenezuelanOperatorAndAccount();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "1000000", 'to_account_id' => $this->venezuelan_account->id]);
            $this->venezuelan_account->refresh();
            $this->assertEquals(1000000, $this->venezuelan_account->balance);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aForgeinOperatorCantAddFundsToVenezuelanOperator()
        {

            $this->actingAsForeignOperator();
            $this->addVenezuelanOperatorAndAccount();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "10000000000", 'to_account_id' => $this->venezuelan_account->id])
                ->assertStatus(403);
            $this->venezuelan_account->refresh();
            $this->assertEquals(0, $this->venezuelan_account->balance);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aVenezuelanOperatorCantAddFundsToVenezuelanOperator()
        {
            $this->actingAsVenezuelanOperator();
            $this->addVenezuelanOperatorAndAccount();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "10000000000", 'to_account_id' => $this->venezuelan_account->id])
                ->assertStatus(403);
            $this->venezuelan_account->refresh();
            $this->assertEquals(0, $this->venezuelan_account->balance);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aClientOperatorCantAddFundsToVenezuelanOperator()
        {
            $this->actingAsClient();
            $this->addVenezuelanOperatorAndAccount();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "10000000000", 'to_account_id' => $this->venezuelan_account->id])
                ->assertStatus(403);
            $this->venezuelan_account->refresh();
            $this->assertEquals(0, $this->venezuelan_account->balance);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aReceiverOperatorCantAddFundsToVenezuelanOperator()
        {
            $this->actingAsReceiver();
            $this->addVenezuelanOperatorAndAccount();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "10000000000", 'to_account_id' => $this->venezuelan_account->id])
                ->assertStatus(403);
            $this->venezuelan_account->refresh();
            $this->assertEquals(0, $this->venezuelan_account->balance);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aGuestOperatorCantAddFundsToVenezuelanOperator()
        {
            $this->addVenezuelanOperatorAndAccount();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "10000000000", 'to_account_id' => $this->venezuelan_account->id])
                ->assertStatus(401);
            $this->venezuelan_account->refresh();
            $this->assertEquals(0, $this->venezuelan_account->balance);
        }

        protected function addVenezuelanOperatorAndAccount(): void
        {
            $this->venezuelan_operator = factory(User::class)->create();
            $this->venezuelan_operator->setRole('venezuelan_operator');
            $this->venezuelan_operator->fresh();
            $this->venezuelan_account = factory(Account::class)->create(['user_id' => $this->venezuelan_operator->id, 'is_operator_account' => true, 'number' => '123123']);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function onlyACoordinatorOrForeignOperatorCanMakeATransaction(): void
        {
            factory(Setting::class)->create(['key'=>'venezuelanBankTax','value'=>"2"]);
            $transaction = $this->defineTransactionData();
            $this->postJson(route('save_transaction'), $transaction)
                ->assertStatus(401);
            $this->actingAsReceiver();
            $this->postJson(route('save_transaction'), $transaction)
                ->assertStatus(403);
            $this->actingAsClient();
            $this->postJson(route('save_transaction'), $transaction)
                ->assertStatus(403);
            $this->actingAsVenezuelanOperator();
            $this->postJson(route('save_transaction'), $transaction)
                ->assertStatus(403);
            $this->actingAsCoordinator();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "10000000000", 'to_account_id' =>$transaction['venezuelan_operator_account_id']]);
            $this->postJson(route('save_transaction'), $transaction)
                ->assertStatus(201);
            $this->actingAsForeignOperator();
            $this->postJson(route('save_transaction'), $transaction)
                ->assertStatus(201);


        }

        /**
         * @test
         */
        public function aTransactionCanHaveManyTtransactionAttachments()
        {
            $transaction = $this->defineTransactionData();
            $attachment=factory(Attachment::class)->create();
            $transaction['received_transaction_attachment_ids']=[$attachment->id];
        //    $transaction['client_id']=factory(User::class)->create()->id;
            $this->actingAsCoordinator();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "10000000000", 'to_account_id' =>$transaction['venezuelan_operator_account_id']]);
            $this->postJson(route('save_transaction'), $transaction)
                ->assertStatus(201);
            $transaction=Transaction::whereClientId($transaction['client_id'])->first();
            $this->assertEquals($attachment->id,$transaction->attachments->first()->id);
        }

        /**
         * @test
         */
        public function whenAnAllowedActorMakeANormalTransactionTwoTransactionsAreGenerated(): void
        {
            $transaction = $this->defineTransactionData();
            $this->actingAsCoordinator();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "1000000", 'to_account_id' =>$transaction['venezuelan_operator_account_id']]);

            $this->actingAsForeignOperator();
            $this->postJson(route('save_transaction'), $transaction)
                ->assertStatus(201);
            $transactions = Transaction::all();
            $this->assertCount(4, $transactions);
            $this->assertEquals('confirmed', $transactions[1]->status);
            $this->assertEquals('assigned', $transactions[2]->status);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function aCoordinatorCanMakeATransactionWithCustomExchangeRate(): void
        {
            $transaction = $this->defineTransactionData(false);
            $this->actingAsCoordinator();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "100000000000", 'to_account_id' =>$transaction['venezuelan_operator_account_id']]);
            $this->postJson(route('save_transaction'), $transaction)
                ->assertStatus(201);
            $transactions = Transaction::all();
            $this->assertCount(4, $transactions);
            $this->assertEquals('confirmed', $transactions[1]->status);
            $this->assertEquals('assigned', $transactions[2]->status);
            $this->assertEquals(20000, $transactions[2]->amount);
        }

        /**
         * A basic test example.
         * @test
         * @return void
         */
        public function ifAForeignOperatorCanMAbeATransactionWithCustomExchangeRateButItWillBeCreatedAsPendingUntilACoordinatorApprove()
        {
            $transaction = $this->defineTransactionData(false);
            $this->actingAsForeignOperator();
            $this->postJson(route('add_money_to_venezuela'), ["amount" => "10000000000", 'to_account_id' =>$transaction['venezuelan_operator_account_id']]);
            $this->postJson(route('save_transaction'), $transaction)
                ->assertStatus(201);
            $this->assertCount(1, PendingTransaction::all());

        }
        public function ifATransactionIsAssignedToVenezuelanAccountOutOfFundsItWillThrowAnError(){

        }

        /**
         * @param bool $normal
         * @return array
         */
        protected function defineTransactionData($normal = true): array
        {
            $client = factory(User::class)->create();
            $operatorForeign = factory(User::class)->create();
            $operatorForeign->setRole('foreign_operator');
            $operatorForeign->refresh();
            $foreignAttachment = factory(Attachment::class)->create();
            $operatorVenezuela = factory(User::class)->create();
            $operatorVenezuela->setRole('venezuelan_operator');
            $operatorVenezuela->refresh();
            $operatorAccount = factory(Account::class)->create(['user_id' => $operatorVenezuela->id]);
            $operatorForgeinAccount = factory(Account::class)->create(['user_id' => $operatorForeign->id]);
            factory(Rate::class)->create(['currency_id' => $operatorForgeinAccount->bank->currency->id, 'since' => Carbon::yesterday()]);
            $related = factory('App\User')->create();
            $relatedAccount = factory(Account::class)->create(['user_id' => $related->id]);
            $transaction = [
                'client_id' => $client->id,
                'foreign_account_id' => $operatorForgeinAccount->id,
                'foreign_attachment_id' => $foreignAttachment->id,
                'receiver_account_id' => $relatedAccount->id,
                'venezuelan_operator_account_id' => $operatorAccount->id,
                'amount' => 10000
            ];
            if (!$normal) {
                $transaction['rate'] = 2;
            }
            return $transaction;
        }

    }

