<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function testExample()
    {
        factory(User::class,40)->create();
        $user=User::first();
        $this->actingAs($user);
        $response = $this->get('/users');
        $response->assertStatus(200);

    }
}
