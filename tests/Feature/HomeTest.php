<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     * @test
     */
    public function RedirectToLoginOnBasePathTest()
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
    /**
     * A basic test example.
     *
     * @return void
     * @test
    public function RedirectToHomeOnBasePathWithUserSessionTest()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user);
        $response = $this->get('/');
        }
     *      */

}
