<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunSqliteSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sqlite:schema:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run SQLite schema file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schemaPath = database_path('schema/sqlite-schema.sql');

        if (file_exists($schemaPath)) {
            $this->info('Running SQLite schema file...');
            DB::unprepared(file_get_contents($schemaPath));
            $this->info('SQLite schema file executed successfully.');
        } else {
            $this->error('SQLite schema file not found.');
        }
    }
}
