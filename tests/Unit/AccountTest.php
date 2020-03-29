<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An Account Must have owner.
     *
     * @test
     */
    public function anAccountCanHaveManyOwners()
    {
        $account = factory('App\Account')->create();
        $this->assertInstanceOf(Collection::class, $account->owners);
    }

    /**
     * An Account Must be part of a Bank.
     *
     * @test
     */
    public function anAccountMustHaveBank()
    {
        $account = factory('App\Account')->create();
        $this->assertInstanceOf(\App\Bank::class, $account->bank);
    }
}
