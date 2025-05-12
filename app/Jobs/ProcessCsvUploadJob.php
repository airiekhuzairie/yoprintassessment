<?php 

namespace App\Jobs;

use App\Models\Product;
use App\Models\CsvUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessCsvUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $upload;

    public function __construct(CsvUpload $upload)
    {
        $this->upload = $upload;
    }

    public function handle()
    {
        try {
            // Get the file path
            $path = storage_path('app/' . $this->upload->filename);

            // Log the path to confirm
            Log::info("Processing file from path: " . $path);

            // Check if the file exists
            if (!file_exists($path)) {
                Log::error("File not found: " . $path);
                return;
            }

            // Read CSV rows into an array
            $rows = array_map('str_getcsv', file($path));

            // Extract the header (first row)
            $header = array_map('trim', $rows[0]);
            unset($rows[0]);

            // Loop through each row of the CSV
            foreach ($rows as $row) {
                // Clean up data (ensure UTF-8 encoding)
                $row = array_map(fn($v) => mb_convert_encoding(trim($v), 'UTF-8', 'UTF-8'), $row);

                // Combine the header with the row values
                $data = array_combine($header, $row);

                // Filter data to include only columns that are in the fillable array
                $fillableData = array_filter($data, function ($key) {
                    return in_array($key, [
                        'UNIQUE_KEY',
                        'PRODUCT_TITLE',
                        'PRODUCT_DESCRIPTION',
                        'STYLE#',
                        'SANMAR_MAINFRAME_COLOR',
                        'SIZE',
                        'COLOR_NAME',
                        'PIECE_PRICE'
                    ]);
                }, ARRAY_FILTER_USE_KEY);

                // Log the processed data for debugging
                Log::info('Processed row: ' . json_encode($fillableData));

                // Insert or update the product record
                Product::updateOrCreate(
                    ['UNIQUE_KEY' => $fillableData['UNIQUE_KEY']], // Use UNIQUE_KEY to find or create the product
                    $fillableData // The product data to insert or update
                );
            }

            // Update CSV upload status to completed
            $this->upload->update(['status' => 'completed']);
        } catch (\Exception $e) {
            // Handle errors, mark as failed
            $this->upload->update(['status' => 'failed']);
            Log::error('CSV processing failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
