<?php

namespace Tests;

use App\Role;
use App\Setting;
use App\User;
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

    protected function seedMigrations()
    {
        Role::create([
            'name_id' => 'coordinator',
            'name'    => 'Coordinador',
        ]);
        Role::create([
            'name_id' => 'foreign_operator',
            'name'    => 'Operador Extranjero',
        ]);
        Role::create([
            'name_id' => 'venezuelan_operator',
            'name'    => 'Operador Venezolano',
        ]);
        Role::create([
            'name_id' => 'client',
            'name'    => 'Cliente',
        ]);
        Role::create([
            'name_id' => 'receiver',
            'name'    => 'Receptor',
        ]);
    }

    protected function actingAsCoordinator()
    {
        $user = factory(User::class)->create([
            'name'     => 'Coordinador',
            'idn'      => '1',
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

    protected function actingAsForeignOperator()
    {
        $user = factory(User::class)->create([
            'name'     => 'Coordinator',
            'idn'      => '2',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);

        $user->setRole('foreign_operator');

        Passport::actingAs(
            $user,
            ['create-servers']
        );
        $user->refresh();

        return $user;
    }

    protected function actingAsVenezuelanOperator()
    {
        $user = factory(User::class)->create([
            'name'     => 'Venezuelan Operator',
            'idn'      => '3',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);

        $user->setRole('venezuelan_operator');

        Passport::actingAs(
            $user,
            ['create-servers']
        );
        $user->refresh();

        return $user;
    }

    protected function actingAsClient()
    {
        $user = factory(User::class)->create([
            'name'     => 'Client',
            'idn'      => '4',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);

        $user->setRole('client');

        Passport::actingAs(
            $user,
            ['create-servers']
        );
        $user->refresh();

        return $user;
    }

    protected function actingAsReceiver()
    {
        $user = factory(User::class)->create([
            'name'     => 'Receiver',
            'idn'      => '5',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);

        $user->setRole('receiver');

        Passport::actingAs(
            $user,
            ['create-servers']
        );
        $user->refresh();

        return $user;
    }
}
