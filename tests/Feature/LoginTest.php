<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
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
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
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
        $response = $this
            ->followingRedirects()
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/login', $credentials);
        $this->assertAuthenticatedAs($user);
    }

    /**
     *
     */
    public function test_user_can_not_login_with_incorrect_credentials()
    {

        $user = User::factory()->create([
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
            ->withoutMiddleware(VerifyCsrfToken::class)
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
        $user = User::factory()->create([
            'id' => random_int(1, 100),
            'name' => 'Pedro Raul',
            'last_name' => 'Rodriguez Soto',
            'idn' => '123123123',
            'idn_type' => 'RUT',
            'email' => 'pedrosoto@mimail.com',
            'password' => bcrypt('passwordbest')
        ]);

        $response = $this
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/login', [
            'idn' => $user->idn,
            'idn_type' => $user->idn_type,
            'password' => 'passwordbest',
            'remember' => 'on',
        ]);

        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
        $this->assertAuthenticatedAs($user);
    }
}
