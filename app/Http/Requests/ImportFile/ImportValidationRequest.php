<?php
namespace App\Http\Requests\ImportFile;



use App\Rules\JsonValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportValidationRequest extends FormRequest
{
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
        return [
            'file' => ['required', 'max:10240', new JsonValidationRule],
        ];
    }
    public function messages()
    {
        return [
            'file.required' => 'Nenhum arquivo foi enviado.',
            'file.max' => 'O tamanho máximo permitido para o arquivo é de 10 MB.',
        ];
    }
}
