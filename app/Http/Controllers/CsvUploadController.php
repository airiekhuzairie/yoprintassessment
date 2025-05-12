<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CsvUpload;
use App\Jobs\ProcessCsvUploadJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CsvUploadController extends Controller
{

     public function index()
    {
        $uploads = CsvUpload::latest()->get();
        return view('csv_upload.index', compact('uploads'));
    }
    //
    public function upload(Request $request) 
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:51200',
        ]);

        $file = $request->file('csv_file');

        // Just save using Storage::put with full content
        $path  = 'uploads/' . uniqid() . '_' . $file->getClientOriginalName();
        Storage::disk('local')->put($path , file_get_contents($file));
        
        $upload = CsvUpload::create([
            'filename' => $path,
            'status' => 'pending',
        ]);
    Log::info("Dispatching job for upload ID: " . $upload->id);
        ProcessCsvUploadJob::dispatch($upload);

        return redirect()->route('csv.index');
    }

    public function status()
    {
        return CsvUpload::latest()->get();
    }
}
