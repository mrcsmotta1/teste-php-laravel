<?php

namespace App\Console;

use Illuminate\Support\Facades\Storage;
use App\Utilities\DirectoryHelper;
use App\Utilities\FileHelper;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        'App\Console\Commands\RunSqliteSchema'
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {

            DirectoryHelper::createDirectoryIfNotExists('schedule', __CLASS__, __FUNCTION__);

            $files = Storage::disk('public')->files('import');
            foreach ($files as $file) {
                $pathInPublic = "public/{$file}";

                $fileName = pathinfo($pathInPublic, PATHINFO_BASENAME);

                FileHelper::moveAndDeleteFile($file, $fileName, __CLASS__, __FUNCTION__);
            }
        })->everySecond();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
