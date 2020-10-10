<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * Displays the reset password request form.
     *
     * @return void
     */
    public function testDisplaysPasswordResetRequestForm()
    {
        $response = $this->get(route('password.reset',[
            'token'=>'token'
        ]));

        $response->assertSuccessful()
            ->assertSee(__('auth.RESET:PASSWORD'))
            ->assertSee(__('auth.IDN_TYPE'))
            ->assertSee(__('auth.EMAIL'))
            ->assertSee(__('auth.IDN'))
            ->assertSee(__('auth.RESET:PASSWORD'));
    }
    /**
     * Allows a user to reset their password.
     *
     * @return void
     */
    public function testDontChangeAUsersPasswordWithDifferentToken()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);
        $this->from(route('password.reset',[
            'token'=>$token
        ]))->post(route('password.update'), [
            'token' => '$token',
            'email' => $user->email,
            'idn' => $user->idn,
            'idn_type' => $user->idn_type,
            'password' => 'passwords123',
            'password_confirmation' => 'passwords123'
        ]);
        $this->assertFalse(Hash::check('passwords123', $user->fresh()->password));
    }
    public function testThrowErrorifChangeAUserPasswordWithDifferentTokenViaJson()
    {
        $user = User::factory()->create();
        $this
          ->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('password.update'), [
            'token' => '$token',
            'email' => $user->email,
            'idn' => $user->idn,
            'idn_type' => $user->idn_type,
            'password' => 'passwords123',
            'password_confirmation' => 'passwords123'
        ])
        ->assertJson([
            "errors"=>[
                'warning' => [trans('passwords.token')],
            ]
        ]);
    }
    /**
     * Allows a user to reset their password.
     *
     * @return void
     */
    public function testDontChangeAUsersPasswordWithDifferentEmail()
    {
        $user = User::factory()->create([
            'email' => 'uno@tres.com'
        ]);
        $token = Password::createToken($user);
        $response = $this->from(route('password.reset',[
            'token'=>$token
        ]))->post(route('password.update'), [
            'token' => $token,
            'email' => 'tres@uno.com',
            'idn' => $user->idn,
            'idn_type' => $user->idn_type,
            'password' => 'passwords123',
            'password_confirmation' => 'passwords123'
        ]);
        $this->assertFalse(Hash::check('passwords123', $user->fresh()->password));
    }

    /**
     * Allows a user to reset their password.
     *
     * @return void
     */
    public function testDontChangeAUsersPasswordWithDifferentIdn()
    {
        $user = User::factory()->create([
            'idn' => '321321'
        ]);
        $token = Password::createToken($user);
        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'idn' => '123213',
            'idn_type' => $user->idn_type,
            'password' => 'passwords123',
            'password_confirmation' => 'passwords123'
        ]);
        $this->assertFalse(Hash::check('passwords123', $user->fresh()->password));
    }

    /**
     * Allows a user to reset their password.
     *
     * @return void
     */
    public function testDontChangeAUsersPasswordWithDifferentIdnType()
    {
        $user = User::factory()->create([
            'idn_type' => 'CI'
        ]);
        $token = Password::createToken($user);
        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'idn' => $user->idn,
            'idn_type' => 'PASSPORT',
            'password' => 'passwords123',
            'password_confirmation' => 'passwords123'
        ]);
        $this->assertFalse(Hash::check('passwords123', $user->fresh()->password));
    }
    /**
     * Allows a user to reset their password.
     *
     * @return void
     */
    public function testChangesAUsersPassword()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);
        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'idn' => $user->idn,
            'idn_type' => $user->idn_type,
            'password' => 'passwords123',
            'password_confirmation' => 'passwords123'
        ]);
        $this->assertTrue(Hash::check('passwords123', $user->fresh()->password));
    }
    public function testChangesAUsersPasswordJson()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);
        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'idn' => $user->idn,
            'idn_type' => $user->idn_type,
            'password' => 'passwords123',
            'password_confirmation' => 'passwords123'
        ])
        ->assertJson(['message' => trans('passwords.reset')]);
    }
}
