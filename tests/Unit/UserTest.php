<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
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
        Role::factory()->create(['name'=>'Clientes','name_id'=>'client']);
        $user = User::factory()->create([
            'name'     => 'ALEX',
            'email'    => 'A@be.com',
            'password' => bcrypt('hidden'),
        ]);
        $this->assertTrue($user->hasRole('client'));
    }

    /**
     * Test user Has Role
     */
    public function testHasRoleClientOnCreated()
    {
        $user=User::factory()->create();
        $this->assertEquals(
            $user->hasRole('client'),
            true);
    }
    /**
     * Test user Has accounts
     */
    public function testHasAccounts()
    {
        $user=User::factory()->create();
        $this->assertCount(0,
            $user->accounts
            );
        $account=Account::factory(1)->create();
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
        $receiver=User::factory()->create();
        $account=Account::factory(1)->create();
        $receiver->accounts()->save($account[0]);
        $user=User::factory()->create();
        $user->receivers()->save($receiver);
        $user->refresh();
        $this->assertCount(1,$user->receivers);
    }
    /**
     * Test user Has Senders
     */
    public function testHasSenders()
    {
        $receiver=User::factory()->create();
        $account=Account::factory(1)->create();
        $receiver->accounts()->save($account[0]);
        $user=User::factory()->create();
        $user->receivers()->save($receiver);
        $receiver->refresh();
        $this->assertCount(1,$receiver->senders);
    }

    public function testSendPasswordResetNotificationSendNotifications()
    {
        Notification::fake();
        $token='123';
        $user=User::factory()->create();
        $this->expectsNotification($user,ResetPassword::class);
        $user->sendPasswordResetNotification($token);

    }

    /**
     *
     */
    public function testSendEmailVerificationNotification()
    {
        Notification::fake();
        $user=User::factory()->create();
        $this->expectsNotification($user,VerifyEmail::class);
        $user->sendEmailVerificationNotification();
    }
    public function testUserHaveSenders(){
        $user=User::factory()->create();
        $sender=User::factory()->create();
        $user->senders()->save($sender);
        $this->assertCount(1,$user->senders);

    }
    public function testUserHaveReceivers(){
        $user=User::factory()->create();
        $receivers=User::factory()->create();
        $user->receivers()->save($receivers);
        $this->assertCount(1,$user->receivers);
    }
    public function testHasRoleTwoTimes(){
        $user=User::factory()->create();
        $role=Role::factory()->create();
        $role->users()->save($user);
        $this->assertTrue($user->hasRole($role->name_id));
    }
    public function testFullName(){
        $user=User::factory()->create();
        $this->assertEquals($user->name.' '.$user->last_name,$user->fullName);
    }


}
