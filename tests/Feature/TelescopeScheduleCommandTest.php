<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class TelescopeScheduleCommandTest extends TestCase
{
    /**
     * @throws BindingResolutionException
     */
    public function testIsAvailableInTheScheduler()
    {
        /** @var Schedule $schedule */
        $schedule = app()->make(Schedule::class);

        $events = collect($schedule->events())->filter(function (Event $event) {
            return stripos($event->command, 'telescope:prune --hours=48');
        });

        if ($events->count() == 0) {
            $this->fail('No events found');
        }

        $events->each(function (Event $event) {
            // This example is for hourly commands.
            $this->assertEquals('0 0 * * *', $event->expression);
        });
    }
}
