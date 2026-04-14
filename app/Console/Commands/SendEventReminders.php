<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminder;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = now()->addDay()->toDateString();

        $events = Event::with('user')->whereDate('event_date', $tomorrow)->get();

        if ($events->isEmpty()) {
            $this->info('No events scheduled for tomorrow.');
            return;
        }

        foreach ($events as $event) {
            $event->user->notify(new EventReminder($event));
            $this->info("Reminder queued for: {$event->user->email} — {$event->title}");
        }

        $this->info("Event reminders sent. {$events->count()} reminder(s) queued.");
    }
}
