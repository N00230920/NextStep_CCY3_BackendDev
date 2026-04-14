<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendEventRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_event_reminders_only_notifies_users_with_events_tomorrow(): void
    {
        Carbon::setTestNow('2026-04-10 08:00:00');
        Notification::fake();

        $userWithReminder = User::factory()->create();
        $userWithoutReminder = User::factory()->create();

        Event::factory()->create([
            'user_id' => $userWithReminder->id,
            'event_date' => '2026-04-11 10:00:00',
        ]);

        Event::factory()->create([
            'user_id' => $userWithoutReminder->id,
            'event_date' => '2026-04-12 10:00:00',
        ]);

        Artisan::call('events:send-event-reminders');

        Notification::assertSentTo($userWithReminder, EventReminder::class);
        Notification::assertNotSentTo($userWithoutReminder, EventReminder::class);

        Carbon::setTestNow();
    }
}
