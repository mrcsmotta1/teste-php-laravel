<?php
namespace App\Http\Requests\ImportFile;

use App\Rules\JsonValidateContentSizeRule;
use App\Rules\JsonValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportValidationRequest extends FormRequest
{
    public $maxSize;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $this->maxSize = env('SIZE_MAX_CONTENT');

        return [
            'file' => ['required', 'max:10240', new JsonValidationRule($this->maxSize)],
        ];
    }
    public function messages()
    {
        return [
            "file.max" => "O tamanho máximo permitido para o arquivo é de {$this->maxSize}.",
            'file.required' => 'Nenhum arquivo foi enviado.',
        ];
    }
}
