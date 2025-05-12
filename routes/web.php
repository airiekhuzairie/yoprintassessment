<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvUploadController;


Route::get('/', function () {
    return view('welcome');
});



Route::get('/csv-upload', [CsvUploadController::class, 'index'])->name('csv.index');
Route::post('/csv-upload', [CsvUploadController::class, 'upload'])->name('csv.upload');
Route::get('/csv-status', [CsvUploadController::class, 'status'])->name('csv.status');
// Route::get('/test-job', function () {
//     $upload = \App\Models\CsvUpload::latest()->first(); // Make sure this fetches an existing record
//     if (!$upload) {
//         return "No CSV upload found!";
//     }
//     dispatch(new \App\Jobs\ProcessCsvUploadJob($upload)); // Dispatch the job with the $upload object
//     return "Job dispatched.";
// });

Route::get('/test-write', function () {
    $path = storage_path('app/uploads/debug.txt');
    try {
        file_put_contents($path, 'Testing manual file write');
        return file_exists($path) ? 'Manual write success' : 'Manual write failed';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
