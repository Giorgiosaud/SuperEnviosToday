<?php

namespace Tests\Unit;

use Tests\TestCase;
use \App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountTest extends TestCase
{
    use RefreshDatabase;
    /**
     * An Account Must have owner
    @test
     */
    public function anAccountMustHaveOwner()
    {
        $account = factory('App\Account')->create();
        $this->assertInstanceOf(\App\User::class, $account->owner);
    }
    /**
     * An Account Must be part of a Bank
    @test
     */
    public function anAccountMustHaveBank()
    {
        $account = factory('App\Account')->create();
        $this->assertInstanceOf(\App\Bank::class, $account->bank);
    }
}
