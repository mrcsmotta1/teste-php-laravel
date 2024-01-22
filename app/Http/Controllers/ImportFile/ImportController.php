<?php

namespace App\Http\Controllers\ImportFile;

use App\Utilities\FileHelper;
use App\Utilities\DirectoryHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ImportFile\ImportValidationRequest;

class ImportController extends Controller
{

    public $maxSize;
    public function showUploadForm()
    {
        return view('importFile.upload');
    }

    public function processUpload(ImportValidationRequest $request)
    {

        DirectoryHelper::createDirectoryIfNotExists('controller', __CLASS__, __FUNCTION__);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('import', $fileName, 'public');

        $jsonData = $jsonData = json_decode(Storage::disk('public')->get($filePath), true);

        $validationErrors = $this->validateJsonContent($jsonData);

        if ($validationErrors) {
            return back()->withErrors($validationErrors)->withInput();

        }

        FileHelper::moveAndDeleteFile($filePath, $fileName, __CLASS__, __FUNCTION__);

        return redirect()->route('import.process.queue')->with('success', 'JSON enviado para processamento.');
    }


    protected function validateJsonContent($jsonData)
    {
        $this->maxSize = env('SIZE_MAX_CONTENT');
        $max = "max:{$this->maxSize}";
        $validator = Validator::make($jsonData, [
            'documentos.*.conteúdo' => $max,
        ], [
            'max' => "O campo conteúdo deve ter no máximo {$this->maxSize} caracteres.",
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        return null;
    }
}
