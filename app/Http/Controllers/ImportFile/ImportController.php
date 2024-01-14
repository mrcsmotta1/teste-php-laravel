<?php

namespace App\Http\Controllers\ImportFile;

use App\Http\Controllers\Controller;
use App\Helpers\ImportDispatcherFile;
use App\Jobs\ProcessDocumentCreation;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ImportFile\ImportValidationRequest;

class ImportController extends Controller
{

    public function showUploadForm()
    {
        return view('importFile.upload');
    }

    public function processUpload(ImportValidationRequest $request, Queue $queue): RedirectResponse
    {

        $file = $request->file('file');

        $filePath = $file->storeAs('import', 'imported_file.json', 'public');
        $jsonData = json_decode(Storage::disk('public')->get($filePath), true);

        ProcessDocumentCreation::dispatch($jsonData)->onQueue('file_processing_queue');

        return redirect()->route('index')->with('success', 'JSON enviado para processamento.');
    }
}
