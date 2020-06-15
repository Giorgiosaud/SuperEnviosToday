<?php

namespace Tests\Feature;

use App\Auth\DatabaseTokenRepository;
use App\User;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test login form.
     *
     * @return void
     * @test
     */
    public function test_user_can_view_a_login_form()
    {
        $response = $this->get('/login');

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    /**
     * @test
     */
    public function test_user_cannot_view_a_login_form_when_authenticated()
    {
        $user = factory(User::class)->make();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/home');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = factory(User::class)->create([
            'name' => 'Pedro Raul',
            'last_name' => 'Rodriguez Soto',
            'idn' => '123123123',
            'idn_type' => 'RUT',
            'email' => 'pedrosoto@mimail.com',
            'password' => bcrypt('passwordbest')
        ]);
        $this->assertDatabaseHas('users', $user->toArray());
        $credentials = [
            'idn' => $user->idn,
            'idn_type' => $user->idn_type,
            'password' => 'passwordbest',
        ];
        $response = $this->post('/login', $credentials);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /**
     *
     */
    public function test_user_can_not_login_with_incorrect_credentials()
    {

        $user = factory(User::class)->create([
            'name' => 'Pedro Raul',
            'last_name' => 'Rodriguez Soto',
            'idn' => '123123123',
            'idn_type' => 'RUT',
            'email' => 'pedrosoto@mimail.com',
            'password' => bcrypt('passwordbest')
        ]);
        $this->assertDatabaseHas('users', $user->toArray());
        $credentials = [
            'idn' => $user->idn,
            'idn_type' => $user->idn_type,
            'password' => 'nopasswordbest',
        ];
        $response = $this
            ->followingRedirects()
            ->from(route('login'))
            ->post(route('login'), $credentials)
            ->assertSuccessful()
            ->assertSee(__('auth.failed'))
            ->assertSee($user->idn)
            ->assertSee($user->idn_type);
        $this->assertGuest();
    }

    /**
     * @throws Exception
     */
    public function test_remember_me_functionality()
    {
        $user = factory(User::class)->create([
            'id' => random_int(1, 100),
            'name' => 'Pedro Raul',
            'last_name' => 'Rodriguez Soto',
            'idn' => '123123123',
            'idn_type' => 'RUT',
            'email' => 'pedrosoto@mimail.com',
            'password' => bcrypt('passwordbest')
        ]);

        $response = $this->post('/login', [
            'idn' => $user->idn,
            'idn_type' => $user->idn_type,
            'password' => 'passwordbest',
            'remember' => 'on',
        ]);

        $response->assertRedirect('/');
        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
        // cookie assertion goes here
        $this->assertAuthenticatedAs($user);
    }
}
