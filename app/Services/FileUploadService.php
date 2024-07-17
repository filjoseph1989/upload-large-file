<?php

namespace App\Services;

use App\Services\Contracts\FileUploadServiceInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileUploadService implements FileUploadServiceInterface
{
    public function uploadChunk(UploadedFile $file, int $chunkIndex, int $totalChunks, string $originalFilename): array
    {
        // Generate a unique temporary filename
        $tempFilename = 'upload_' . md5($originalFilename) . '.tmp';

        // Store the chunk temporarily
        Storage::disk('local')->putFileAs('chunks', $file, $tempFilename . '.' . $chunkIndex);

        // Check if all chunks are uploaded
        $chunksPath = Storage::disk('local')->path('chunks');
        $uploadedChunks = glob($chunksPath . '/' . $tempFilename . '.*');

        if (count($uploadedChunks) == $totalChunks) {
            // Ensure the uploads directory exists
            $uploadsDirectory = 'uploads';
            if (!Storage::disk('local')->exists($uploadsDirectory)) {
                Storage::disk('local')->makeDirectory($uploadsDirectory);
            }

            // Combine all chunks to create the final file
            $finalFilePath = $uploadsDirectory . '/' . $originalFilename;
            $finalFile = Storage::disk('local')->path($finalFilePath);

            $fileHandle = fopen($finalFile, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = Storage::disk('local')->path('chunks/' . $tempFilename . '.' . $i);
                $chunk = fopen($chunkPath, 'rb');
                while ($buffer = fread($chunk, 4096)) {
                    fwrite($fileHandle, $buffer);
                }
                fclose($chunk);

                // Delete the chunk after appending it to the final file
                Storage::disk('local')->delete('chunks/' . $tempFilename . '.' . $i);
            }

            fclose($fileHandle);

            // Get the URL of the uploaded file
            $videoUrl = Storage::disk('local')->url($finalFilePath);
            $videoUrl = str_replace("/storage/", '', $videoUrl);

            return [
                'status' => 'success',
                'url' => config('app.url') . '/' . $videoUrl,
            ];
        }

        return ['status' => 'chunk uploaded'];
    }
}
