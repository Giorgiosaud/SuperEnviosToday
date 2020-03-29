<?php

namespace Tests;

use App\Rate;
use App\Role;
use App\Bank;
use App\Currency;
use App\Setting;
use App\Account;
use App\User;
use Illuminate\Auth\RequestGuard;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Carbon;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $client=null;
    protected $receiver=null;
    protected $venezuelan_account= null;
    protected $venezuelan_operator=null;
    protected $coordinator_operator=null;
    protected $foreign_operator=null;
    protected $normal_user=null;
    
    
    protected function setUp()
    {
        parent::setUp();
        $this->seedRoles();
        $this->seedBanks();
        $this->resetEvents();
        factory(Setting::class)->create(['key'=>'venezuelanBankTax', 'value'=>'2']);
        factory(Setting::class)->create(['key'=>'status', 'value'=>'1']);
    }
    
    private function resetEvents()
    {
        // Define the models that have event listeners.
        $models = ['App\User'];
        
        // Reset their event listeners.
        foreach ($models as $model) {
            
            // Flush any existing listeners.
            call_user_func([$model, 'flushEventListeners']);
            
            // Reregister them.
            call_user_func([$model, 'boot']);
        }
    }
    protected function currenciesSeed(){
        
        Currency::firstOrCreate([
            'name'=>'Pesos Chilenos',
            'identificator'=>'CLP',
            'sign'=>'$'
            ]
        );
        
        Currency::firstOrCreate([
            'name'=>'Bolivares Soberanos',
            'identificator'=>'BsS',
            'sign'=>'BS'
            ]
        );
        
    }
    protected function rateSeed(){
        $this->currenciesSeed();
        Rate::create([
            'since'=>Carbon::yesterday(),
            'amount'=>'319500',
            'currency_id'=>Currency::whereName('Pesos Chilenos')->first()->id
            ]
        );
        
    }
    protected function addVenezuelanOperatorAndAccount(): void
    {
        $this->venezuelan_operator = factory(User::class)->create();
        $this->venezuelan_operator->setRole('venezuelan_operator');
        $this->venezuelan_operator->fresh();
        $bank=Bank::whereName('Mercantil')->get()->first();
        $this->venezuelan_account = factory(Account::class)->create([ 
            'bank_id'=>$bank->id,
            'is_operator_account' => true,
            'number' => '123123']
        );
        $this->venezuelan_account->owners()->sync([$this->venezuelan_operator->id], false);
    }
    protected function addForeignOperatorAndAccount():void
    {
        $this->venezuelan_operator = factory(User::class)->create();
        $this->venezuelan_operator->setRole('foreign_operator');
        $this->venezuelan_operator->fresh();
        $bank=Bank::whereName('Banco Estado')->get()->first();
        $this->venezuelan_account = factory(Account::class)->create([ 
            'bank_id'=>$bank->id,
            'is_operator_account' => true,
            'number' => '111111111']
        );
        $this->venezuelan_account = factory(Account::class)->create([ 'is_operator_account' => true, 'number' => '123123']);
        $this->venezuelan_account->owners()->sync([$this->venezuelan_operator->id], false);
    }
    protected function seedRoles()
    {
        Role::create([
            'name_id' => 'coordinator',
            'name'    => 'Coordinador',
            ]
        );
        Role::create([
            'name_id' => 'foreign_operator',
            'name'    => 'Operador Extranjero',
            ]
        );
        Role::create([
            'name_id' => 'venezuelan_operator',
            'name'    => 'Operador Venezolano',
            ]
        );
        Role::create([
            'name_id' => 'client',
            'name'    => 'Cliente',
            ]
        );
        Role::create([
            'name_id' => 'receiver',
            'name'    => 'Receptor',
            ]
        );
        
        
    }
    protected function seedBanks(){
        $this->currenciesSeed();
        $bs = Currency::whereName('Bolivares Soberanos')->get()->first();
        $clp = Currency::whereName('Pesos Chilenos')->get()->first();
        factory(Bank::class)->create([
            'name'       => 'Banco Estado',
            'currency_id'=> $clp->id,
            ]);
            factory(Bank::class)->create([
                'name'       => 'Mercantil',
                'currency_id'=> $bs->id,
                ]);
            }
            protected function actingAsCoordinator()
            {
                if(!$this->coordinator_operator){
                    $this->coordinator_operator = factory(User::class)->create([
                        'name'     => 'Coordinador',
                        'idn'      => '1',
                        'idn_type' => 'CI',
                        'password' => bcrypt('hidden'),
                        ]
                    );
                    
                    $this->coordinator_operator->setRole('coordinator');
                }
                if ($this->coordinator_operator instanceof User) {
                    $this->actingAs($this->coordinator_operator, 'api');
                    $this->coordinator_operator->refresh();
                    
                    return $this->coordinator_operator;
                }
                return false;
            }
            
            protected function actingAsForeignOperator()
            {
                if(!$this->foreign_operator){
                    $this->foreign_operator = factory(User::class)->create([
                        'name'     => 'Coordinator',
                        'idn'      => '2',
                        'idn_type' => 'CI',
                        'password' => bcrypt('hidden'),
                        ]
                    );
                    
                    $this->foreign_operator->setRole('foreign_operator');
                }
                if ($this->foreign_operator instanceof User) {
                    $this->actingAs($this->foreign_operator, 'api');
                    $this->foreign_operator->refresh();
                    
                    return $this->foreign_operator;
                }
                return false;
            }
            
            protected function actingAsVenezuelanOperator()
            {
                if (!$this->venezuelan_operator) {
                    $this->venezuelan_operator = factory(User::class)->create(
                        [
                            'name'     => 'Venezuelan Operator',
                            'idn'      => '3',
                            'idn_type' => 'CI',
                            'password' => bcrypt('hidden'),
                            ]
                        );
                        
                        $this->venezuelan_operator->setRole('venezuelan_operator');
                        $this->actingAs($this->venezuelan_operator,'api');
                        
                    }
                    if ($this->venezuelan_operator instanceof User) {
                        $this->actingAs($this->venezuelan_operator, 'api');
                        $this->venezuelan_operator->refresh();
                        
                        return $this->venezuelan_operator;
                    }
                    return false;
                }
                
                protected function actingAsClient()
                {
                    if (!$this->client) {
                        $this->client = factory(User::class)->create(
                            [
                                'name'     => 'Client',
                                'idn'      => '4',
                                'idn_type' => 'CI',
                                'password' => bcrypt('hidden'),
                                ]
                            );
                            $this->client->setRole('client');
                        }
                        if ($this->client instanceof User) {
                            $this->actingAs($this->client, 'api');
                            $this->client->refresh();
                            
                            return $this->client;
                        }
                        return false;
                    }
                    protected function actingAsGuest(){
                        RequestGuard::macro('logout', function() {
                            $this->user = null;
                        });
                        $this->app['auth']->guard('api')->logout(); // or omit `->guard('api')` part
                        
                    }
                    protected function actingAsReceiver()
                    {
                        if (!$this->receiver) {
                            $this->receiver = factory(User::class)->create(
                                [
                                    'name'     => 'Receiver',
                                    'idn'      => '5',
                                    'idn_type' => 'CI',
                                    'password' => bcrypt('hidden'),
                                    ]
                                );
                                
                                $this->receiver->setRole('receiver');
                            }
                            if ($this->receiver instanceof User) {
                                $this->actingAs($this->receiver, 'api');
                                $this->receiver->refresh();
                                
                                return $this->receiver;
                            }
                            return false;
                        }
                    }
                    