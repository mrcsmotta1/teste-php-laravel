<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessDocumentCreation;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function moveAndDeleteFile($file, $fileName, $class, $function)
    {
        $sourceDirectory = 'public/import';
        $destinationDirectory = 'public/processed';

        $pathInPublic = "public/{$file}";

        $jsonData = json_decode(Storage::disk('public')->get($file), true);

        $newFileName = self::generateUniqueFileName($pathInPublic);
        Log::info("Renomeado arquivo {$fileName} para {$newFileName}, " . " Class: " . $class . " Function: " . $function . " Line: ". __line__);

        Storage::move($pathInPublic, $destinationDirectory . '/' . $newFileName);
        Log::info("Movendo arquivo de {$pathInPublic} para diretorio {$destinationDirectory}, " . " Class: " . $class . " Function: " . $function . " Line: ". __line__);

        Storage::delete($pathInPublic);
        Log::info("Deletado arquivo de {$fileName} do diretorio raiz {$sourceDirectory}, " . " Class: " . $class . " Function: " . $function . " Line: ". __line__);

        ProcessDocumentCreation::dispatch($jsonData)->onQueue('file_processing_queue');
        Log::info("Despachando arquivo {$fileName} para processar , " . " Class: " . $class . " Function: " . $function . " Line: ". __line__);
    }

    public static function generateUniqueFileName($originalFileName)
    {
        $info = pathinfo($originalFileName);
        $timestamp = uniqid() . '_' . date('YmdHis');

        return $info['filename'] . '_' . $timestamp . '.' . $info['extension'];
    }
}
