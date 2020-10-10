<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class RoleTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     *
     */
    public function testRolesHaveManyUsers()
    {
        Role::factory()->create(['name'=>'Clientes','name_id'=>'client']);
        $roleInitialCount=Role::find('client')->users->count();
        User::factory(30)->create();
        $role=Role::find('client');
        $this->assertCount($roleInitialCount+30,$role->users);
        $this->assertInstanceOf(User::class, $role->users[0]);
    }

}
