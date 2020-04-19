<?php

namespace Tests\Unit;

use App\Role;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class RoleTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        // you can call
        $this->artisan('db:seed');

        // or
        $this->seed();
    }

    /**
     *
     */
    public function testRolesHaveManyUsers()
    {
        $roleInitialCount=Role::find('client')->users->count();
        factory(User::class,30)->create();
        $role=Role::find('client');
        $this->assertCount($roleInitialCount+30,$role->users);
    }

}
