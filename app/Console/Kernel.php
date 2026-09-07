<?php

namespace App\Console;

use App\Models\ActivityLog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process scheduled posts every minute
        $schedule->command('posts:process-scheduled')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        // Retry failed posts every 5 minutes
        $schedule->command('posts:retry-failed')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        // Clean up old logs weekly
        $schedule->command('model:prune', [
            '--model' => [ActivityLog::class],
            '--days' => 90,
        ])->weekly();

        // Update scheduler heartbeat
        $schedule->command('scheduler:heartbeat')
            ->everyMinute()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
