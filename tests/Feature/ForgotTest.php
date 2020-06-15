<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ForgotTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Displays the forget password request form.
     *
     * @return void
     */
    public function testDisplaysPasswordResetRequestForm()
    {
        $response = $this->get(route('password.request', [
            'token' => 'token'
        ]));

        $response->assertSuccessful()
            ->assertSee(__('auth.FORGET'))
            ->assertSee(__('auth.IDN_TYPE'))
            ->assertSee(__('auth.EMAIL'))
            ->assertSee(__('auth.IDN'))
            ->assertSee(__('auth.SEND:RESET:EMAIL'));;
    }

    /**
     * Testing submitting the password reset request with an invalid
     * email address.
     * */
    public function testFailsOnSubmitPasswordResetRequestInvalidCredentials()
    {
        $this
            ->followingRedirects()
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => 'asasd@asd.com',
                'idn' => '1231231233399-2',
                'idn_type' => 'CI'
            ])
            ->assertSuccessful()
            ->assertSee(__('passwords.user'));
    }
    /**
     * Testing submitting a password reset request and throttling.
     * */
    public function testSubmitPasswordResetRequestWorksOnFirstTimeButThrottledOnImmediateSecondTime()
    {
        Notification::fake();
        $user = factory(User::class)->create();

        $response = $this
            ->followingRedirects()
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'idn' => $user->idn,
                'idn_type' => $user->idn_type,
                'email' => $user->email,
            ])
            ->assertSuccessful()
            ->assertSee(__('passwords.sent'));

        Notification::assertSentTo($user, ResetPassword::class);
        $response = $this
            ->followingRedirects()
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'idn' => $user->idn,
                'idn_type' => $user->idn_type,
                'email' => $user->email,
            ])
            ->assertSuccessful()
            ->assertSee(__('passwords.throttled'));
    }
    public function testSubmitPasswordResetRequestWorksOnFirstTimeButThrottledOnImmediateSecondTimeViaJson()
    {
        Notification::fake();
        $user = factory(User::class)->create();

        $response = $this
            ->followingRedirects()
            ->from(route('password.request'))
            ->postJson(route('password.email'), [
                'idn' => $user->idn,
                'idn_type' => $user->idn_type,
                'email' => $user->email,
            ])
            ->assertSuccessful()
            ->assertSee('Le hemos enviado al correo el link de reinicio de clave');

    }

    /**
     *
     */
    public function testConfigNotProperlySetup()
    {
        Config::set("auth.passwords.users",null);
        $this->expectException("InvalidArgumentException");
        $this->expectExceptionMessage("Password resetter [users] is not defined.");
        $user = factory(User::class)->create();
        $token = Password::createToken($user);
        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'idn' => $user->idn,
            'idn_type' => $user->idn_type,
            'password' => 'passwords123',
            'password_confirmation' => 'passwords123'
        ]);
    }

}
