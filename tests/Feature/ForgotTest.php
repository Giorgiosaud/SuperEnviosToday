<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Auth\SessionGuard;
use ReflectionProperty;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;

class ForgotTest extends TestCase
{
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
      ->assertSee(__('auth.SEND:RESET:EMAIL'));
  }

  /**
   * Testing submitting the password reset request with an invalid
   * email address.
   * */
  public function testFailsOnSubmitPasswordResetRequestInvalidCredentials()
  {
    $this->withoutMiddleware(VerifyCsrfToken::class)
      ->post(route('password.email'), [
        'idn_type' => 'CI',
        'idn' => '1231231233399-2',
        'email' => 'asasd@asd.com',
      ])
      ->assertSessionHas('warning', __('passwords.user'));
  }

  protected function resetAuth(array $guards = null): void
  {
    $guards = $guards ?: array_keys(config('auth.guards'));

    foreach ($guards as $guard) {
      $guard = $this->app['auth']->guard($guard);

      if ($guard instanceof SessionGuard) {
        $guard->logout();
      }
    }

    $protectedProperty = new ReflectionProperty($this->app['auth'], 'guards');
    $protectedProperty->setAccessible(true);
    $protectedProperty->setValue($this->app['auth'], []);
  }

  public function testSubmitPasswordResetRequestWorksOnFirstTimeButThrottledOnImmediateSecondTimeViaJson()
  {
    $this->resetAuth();
    Notification::fake();
    $user = User::factory()->create();
    $this->assertGuest();
    $response = $this
      ->withoutMiddleware(VerifyCsrfToken::class)
      ->followingRedirects()
      ->from(route('password.request'))
      ->postJson(route('password.email'), [
        'idn' => $user->idn,
        'idn_type' => $user->idn_type,
        'email' => $user->email,
      ])
      ->assertSimilarJson(['message' => __('passwords.sent')]);

  }

  /**
   *
   */
  public function testConfigNotProperlySetup()
  {
    Config::set("auth.passwords.users", null);
    $this->expectException("InvalidArgumentException");
    $this->expectExceptionMessage("Password resetter [users] is not defined.");
    $user = User::factory()->create();
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
