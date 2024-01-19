<?php

namespace App\Http\Controllers\ImportFile;

use App\Utilities\FileHelper;
use App\Utilities\DirectoryHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

use App\Http\Requests\ImportFile\ImportValidationRequest;

class ImportController extends Controller
{

    public function showUploadForm()
    {
        return view('importFile.upload');
    }

    public function processUpload(ImportValidationRequest $request): RedirectResponse
    {

        DirectoryHelper::createDirectoryIfNotExists('controller', __CLASS__, __FUNCTION__);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('import', $fileName, 'public');

        FileHelper::moveAndDeleteFile($filePath, $fileName, __CLASS__, __FUNCTION__);

        return redirect()->route('index')->with('success', 'JSON enviado para processamento.');
    }
}
