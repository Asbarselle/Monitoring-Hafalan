<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Analyze unprocessed audio files daily at midnight
        $schedule->command('hafalan:analyze-audios --limit=50')
            ->dailyAt('00:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/audio-analysis.log'));

        // Optional: Cleanup old temp files
        $schedule->call(function () {
            $tempDir = storage_path('temp');
            if (is_dir($tempDir)) {
                $files = glob($tempDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file) && time() - filemtime($file) > 86400) { // 24 hours
                        unlink($file);
                    }
                }
            }
        })->dailyAt('02:00');
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
