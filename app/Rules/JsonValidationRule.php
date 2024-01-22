<?php

namespace App\Rules;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Rule;

class JsonValidationRule implements Rule
{

    public $errorMax;
    public $error;
    public function __construct(protected $maxSize)
    {
    }
    public function passes($attribute, $value)
    {
        $mimeType = $value->getClientMimeType();


        if ($mimeType !== 'application/json') {
            return false;
        }

        $content = $value->getContent();
        $result = json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error = "O arquivo JSON não é válido.";
            Log::error("O arquivo JSON não é válido." . " Class: " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);
            return false;
        }

        foreach ($result->documentos as $key => $value) {
            if (strlen($value->conteúdo) > $this->maxSize) {
                $this->error = "O campo contéudo é maior do que o máximo permitido de {$this->maxSize} caracteres.";
                Log::error("O campo contéudo é maior do que o máximo permitido de {$this->maxSize} caracteres. " . __CLASS__ . " Function: " . __FUNCTION__  .  " Line: " . __line__);
                return false;
            }
        }

        return json_last_error() === JSON_ERROR_NONE;
    }

    public function message()
    {
        return $this->error;
    }
}
