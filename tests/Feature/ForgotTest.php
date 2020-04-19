<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

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
     * Testing submitting a password reset request.
     */
    public function testSubmitPasswordResetRequestAndSendEmailWithResetLink()
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
    }

    /**
     * Testing submitting a password reset request.
     * */
    public function testSubmitPasswordResetRequestThrottled()
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

}
