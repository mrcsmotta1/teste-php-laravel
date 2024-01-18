<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Jobs\ProcessDocumentCreation;
use Illuminate\Support\Facades\Storage;
use App\Console\Commands\RunSqliteSchema;
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
            $sourceDirectory = 'public/import';
            $destinationDirectory = 'public/processed';

            $files = Storage::disk('public')->files('import');

            if (!Storage::exists($sourceDirectory)) {
                Storage::makeDirectory($sourceDirectory);
                File::chmod(storage_path('app/public/import'), 0777);

                $arquivoOrigem = storage_path('data/2023-03-28.json');
                $arquivoDestino = storage_path('app/public/import');
                $comando = "cp {$arquivoOrigem} {$arquivoDestino}";

                shell_exec($comando);

                Log::info("Realizado copia do arquivo 2023-03-28.json com sucesso, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__);

                Log::info("Criado diretorio root de arquivos {$destinationDirectory} com sucesso, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__);
            }

            if (!Storage::exists($destinationDirectory)) {
                Storage::makeDirectory($destinationDirectory);
                File::chmod(storage_path('app/public/processed'), 0775);
                Log::info("Criado diretorio de arquivos processados {$destinationDirectory} com sucesso, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__);
            }

            foreach ($files as $file) {
                $pathInPublic = "public/{$file}";

                $fileName = pathinfo($pathInPublic, PATHINFO_BASENAME);

                $jsonData = json_decode(Storage::disk('public')->get($file), true);

                Log::info("Movendo arquivo de {$pathInPublic} para diretorio {$destinationDirectory}, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__);
                Storage::move($pathInPublic, $destinationDirectory . '/' . $fileName);

                Log::info("Deletado arquivo de {$fileName} do diretorio raiz {$sourceDirectory}, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__);
                Storage::delete($pathInPublic);

                Log::info("Despachando arquivo {$fileName} para processar , " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__);
                ProcessDocumentCreation::dispatch($jsonData)->onQueue('file_processing_queue');
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
