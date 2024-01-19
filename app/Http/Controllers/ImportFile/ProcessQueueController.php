<?php

namespace App\Http\Controllers\ImportFile;

use App\Models\ImportFile\Category;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ImportFile\Documents;
use Illuminate\Support\Facades\Queue;

class ProcessQueueController extends Controller
{
    public function showProcessQueueForm()
    {
        return view('importFile.process_queue');
    }

    public function checkQueue()
    {
        $queueName = 'file_processing_queue';
        $jobsCount = Queue::size($queueName);

        return response()->json(['jobsCount' => $jobsCount]);
    }

    public function processQueue()
    {
        $connection = 'database';

        $result = false;
        $exercicioProcessado = [];
        while ($job = Queue::connection($connection)->pop('file_processing_queue')) {
            $rs = unserialize($job->payload()['data']['command']);

            if (empty($rs->data)) {
                Log::error("Erro ao tentar o unserialize no arquivo, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);
            }

            if (!empty($rs->data)) {
                $exercicio = trim($rs->data['exercicio']);
                array_push($exercicioProcessado, $exercicio);
                Log::info("Unserialize do arquivo: {$exercicio}, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);

                foreach ($rs->data['documentos'] as $key => $documento) {
                    $result = $this->processDocument($documento, $exercicio);
                }

                $job->delete();
            }
        }

        if (!$result) {
            Log::error("Erro ao processar a fila, verifique o conteudo do arquivo e importe novamente, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);
            return redirect()->route('import.process.queue.form')->with('error', 'Erro ao processar a fila, verifique o conteudo do arquivo e importe novamente.');
        }

        $exercicioString = implode(' - ', $exercicioProcessado);
        Log::info("Fila processada com sucesso arquivo: {$exercicioString}, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);
        return redirect()->route('import.process.queue.form')->with('success', 'Fila processada com sucesso.');
    }

    private function processDocument($data, $exercicio)
    {
        $dataLower = $this->dataLower($data);

        if ($dataLower['categoria'] !== 'remessa' && $dataLower['categoria'] !== 'remessa parcial') {
            Log::error("Categoria inválida: {$dataLower['categoria']}, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);
            return false;
        }

        if ($dataLower['categoria'] === 'remessa' && strpos($dataLower['titulo'], 'semestre') === false) {
            Log::error("Categoria Remessa inválida sem Semestre, Categoria: {$dataLower['categoria']} -- Titulo: {$dataLower['titulo']} " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);
            return false;
        }
        if ($dataLower['categoria'] === 'remessa parcial' && $this->isValidMonth($dataLower['titulo']) === false) {
            Log::error("Categoria Remessa Parcial inválida sem nome de um mês válido, Categoria: {$dataLower['categoria']} -- Titulo: {$dataLower['titulo']}" . " Class: " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);
            return false;
        }

        $categoryID = $this->getCategoryId($dataLower['categoria']);

        if (!$categoryID) {
            Log::error("Error ao selecionar categoria do arquivo {$exercicio} no BD!, Categoria: {$dataLower['categoria']} -- Titulo: {$dataLower['titulo']}  " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);
            return false;
        }

        $newDocumentId = Documents::create([
            'category_id' =>  $categoryID,
            'exercise' => $exercicio,
            'title' => $data['titulo'],
            'contents' => $data['conteúdo'],
        ])->id;

        Log::info("Documento id:{$newDocumentId} salvo com sucesso!, " . " Class: " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);

        return true;
    }

    private function dataLower($data)
    {
        $newData = [];
        $newdataLower = [];
        $newData = $this->removeAccent($data);
        foreach ($newData as $key => $value) {
            $newdataLower[$key] = ($key == 'conteudo') ? $value : strtolower($value);
        }

        return $newdataLower;
    }

    private function removeAccent($data)
    {
        $dataWithAccent = [];
        foreach ($data as $key => $value) {
            $withAccent = preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $key));
            $dataWithAccent[$withAccent] = $value;
        }

        return $dataWithAccent;
    }

    private function isValidMonth($title)
    {
        $months = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];

        foreach ($months as $month) {
            if (strpos(strtolower($title), $month) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getCategoryId($categoria)
    {
        $categoryID = Category::where('name', $categoria)->value('id');

        return $categoryID;
    }
}
