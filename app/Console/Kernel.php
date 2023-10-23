<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UserResult::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('assessment:update-score')->everyMinute();
        $schedule->command('subscriber-leaderboard:update')->everyFiveMinutes();
        $schedule->command('flash-news:content')->dailyAt('09:00');
        $schedule->command('leaderboard-rank:downfall')->dailyAt("10:00");
        $schedule->command('app-not-opended:in-5-days')->dailyAt("09:05");
        $schedule->command('app-not-opended:in-10-days')->dailyAt("09:10");
        $schedule->command('app-not-opened:in-14-days')->dailyAt("09:15");
        $schedule->command('leaderboard-pending:badges')->dailyAt("09:20");
        $schedule->command('planned:assessment')->everyMinute();
        $schedule->command('planned-assessment:before-an-hour')->everyMinute();
        $schedule->command('planned-assessment:before-5-minutes')->everyMinute();
        $schedule->command('forgot-password:send-otp')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
