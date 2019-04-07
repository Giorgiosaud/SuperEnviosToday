<?php

namespace Tests;

use App\Role;
use App\User;
use Exception;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();
        $this->seedMigrations();
        $this->resetEvents();
    }

    private function resetEvents()
    {
        // Define the models that have event listeners.
        $models = array('App\User');

        // Reset their event listeners.
        foreach ($models as $model) {

            // Flush any existing listeners.
            call_user_func(array($model, 'flushEventListeners'));

            // Reregister them.
            call_user_func(array($model, 'boot'));
        }
    }

    protected function seedMigrations()
    {
        Role::create([
            'name_id' => 'coordinator',
            'name'=>'Coordinador'
        ]);
        Role::create([
            'name_id' => 'foreign_operator',
            'name'=>'Operador Extranjero'
        ]);
        Role::create([
            'name_id' => 'venezuelan_operator',
            'name'=>'Operador Venezolano'
        ]);
        Role::create([
            'name_id' => 'client',
            'name'=>'Cliente'
        ]);
        Role::create([
            'name_id' => 'receiver',
            'name'=>'Receptor'
        ]);
    }
    protected function actingAsChileanOperator(){
        $user = factory(User::class)->create([
            'name' => 'Chilean Operator',
            'idn' => '4',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);
        $user->toogleRole('foreign_operator');
        Passport::actingAs(
            $user,
            ['create-servers']
        );
        return $user;
    }
    protected function actingAsVenezuelanOperator(){
        $user = factory(User::class)->create([
            'name' => 'Venezuelan Operator',
            'idn' => '3',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);
        $user->toogleRole('venezuelan_operator');

        Passport::actingAs(
            $user,
            ['create-servers']
        );
        return $user;
    }
    protected function actingAsClient(){
        $user = factory(User::class)->create([
            'name' => 'Client',
            'idn' => '2',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);
        Passport::actingAs(
            $user,
            ['create-servers']
        );
        return $user;
    }
    protected function actingAsCoordinator(){
        $user = factory(User::class)->create([
            'name' => 'Coordinador',
            'idn' => '1',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);

        $user->setRole('coordinator');

        Passport::actingAs(
            $user,
            ['create-servers']
        );
        $user->refresh();
        return $user;
    }
    protected function disableExceptionHandling()
    {
        $this->app->instance(Handler::class, new class() extends Handler {
            public function __construct()
            {
            }

            public function report(Exception $e)
            {
            }

            public function render($request, Exception $e)
            {
                throw $e;
            }
        }
        );
    }
}
