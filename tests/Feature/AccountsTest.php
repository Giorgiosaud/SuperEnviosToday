<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class UserTest
 * @package Tests\Feature
 */
class AccountsTest extends TestCase
{
    use RefreshDatabase;


    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function aUsersAPIWORKS()
    {
        $this->disableExceptionHandling();
        factory(User::class, 10)->create();
        $user = factory(User::class)->create([
            'name' => 'Coordinador',
            'idn' => '1',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);
        $user->toogleRole('coordinator');
        Passport::actingAs(
            $user,
            ['create-servers']
        );

        $response = $this->get('/api/users');
        $response->assertJsonCount(11, $key = 'data');

    }
}

