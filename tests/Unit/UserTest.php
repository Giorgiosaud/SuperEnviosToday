<?php

namespace Tests\Unit;

use App\Account;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use App\Role;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }
    /**
     * A basic test example.
     *
     * @return void
     * @test
     * */
    public function aUserHaveADefaultRoleOfClient()
    {
        factory(Role::class)->create(['name'=>'Clientes','name_id'=>'client']);
        $user = factory(User::class)->create([
            'name'     => 'ALEX',
            'email'    => 'A@be.com',
            'password' => bcrypt('hidden'),
        ]);
        $this->assertTrue($user->hasRole('client'));
    }

    /**
     * Test user Has Role
     */
    public function testHasRole()
    {
        $user=factory(User::class)->create();
        $this->assertEquals(
            $user->hasRole('client'),
            true);
    }
    /**
     * Test user Has accounts
     */
    public function testHasAccounts()
    {
        $user=factory(User::class)->create();
        $this->assertCount(0,
            $user->accounts
            );
        $account=factory(Account::class,1)->create();
        $user->accounts()->save($account[0]);
        $user->refresh();
        $this->assertCount(1,
            $user->accounts
            );
    }
    /**
     * Test user Has Receivers
     */
    public function testHasReceivers()
    {
        $receiver=factory(User::class)->create();
        $account=factory(Account::class,1)->create();
        $receiver->accounts()->save($account[0]);
        $user=factory(User::class)->create();
        $user->receivers()->save($receiver);
        $user->refresh();
        $this->assertCount(1,$user->receivers);
    }
    /**
     * Test user Has Senders
     */
    public function testHasSenders()
    {
        $receiver=factory(User::class)->create();
        $account=factory(Account::class,1)->create();
        $receiver->accounts()->save($account[0]);
        $user=factory(User::class)->create();
        $user->receivers()->save($receiver);
        $receiver->refresh();
        $this->assertCount(1,$receiver->senders);
    }

    public function testSendPasswordResetNotificationSendNotifications()
    {
        Notification::fake();
        $token='123';
        $user=factory(User::class)->create();
        $this->expectsNotification($user,ResetPassword::class);
        $user->sendPasswordResetNotification($token);

    }

    /**
     *
     */
    public function testSendEmailVerificationNotification()
    {
        Notification::fake();
        $user=factory(User::class)->create();
        $this->expectsNotification($user,VerifyEmail::class);
        $user->sendEmailVerificationNotification();
    }


}
