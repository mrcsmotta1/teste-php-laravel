<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DirectoryHelper
{
    public static function createDirectoryIfNotExists($type, $class, $function)
    {
        $directoryPath = storage_path('app/public/import');
        $destinationDirectory = storage_path('app/public/processed');

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath);
            chmod($directoryPath, 0777);

            Log::info("Criado diretorio de arquivos {$directoryPath} com sucesso, " . " Class: " . $class . " Function: " . $function . " Line: ". __line__);

            if ($type == 'schedule') {
                $arquivoOrigem = storage_path('data/2023-03-28.json');
                $arquivoDestino = storage_path('app/public/import');
                $comando = "cp {$arquivoOrigem} {$arquivoDestino}";

                shell_exec($comando);

                Log::info("Realizado copia do arquivo 2023-03-28.json para o diretorio: {$destinationDirectory} com sucesso, " . " Class: " . $class . " Function: " . $function . " Line: ". __line__);
            }
        }

        if (!File::exists($destinationDirectory)) {
            File::makeDirectory($destinationDirectory);
            chmod($destinationDirectory, 0777);
            Log::info("Criado diretorio de arquivos processados {$destinationDirectory} com sucesso, " . " Class: " . $class . " Function: " . $function . " Line: ". __line__);
        }
    }
}
