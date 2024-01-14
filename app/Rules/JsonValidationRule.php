<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class JsonValidationRule implements Rule
{

    public function passes($attribute, $value)
    {
        $mimeType = $value->getClientMimeType();


        if ($mimeType !== 'application/json') {
            return false;
        }

        $content = $value->getContent();
        json_decode($content);

        return json_last_error() === JSON_ERROR_NONE;

    }

    public function message()
    {
        return 'O arquivo deve ser um JSON válido.';
    }
}
