<?php

namespace Tests;

use App\Role;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();
        $this->seedMigrations();
    }

    protected function seedMigrations()
    {
        Role::create([
            'name_id' => 'coordinator',
            'name'=>'Coordinador'
        ]);
        Role::create([
            'name_id' => 'chilean_operator',
            'name'=>'Operador Chileno'
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

    protected function disableExceptionHandling()
    {
        $this->app->instance(Handler::class, new class() extends Handler {
            public function __construct()
            {
            }

            public function report(\Exception $e)
            {
            }

            public function render($request, \Exception $e)
            {
                throw $e;
            }
        }
        );
    }
}
