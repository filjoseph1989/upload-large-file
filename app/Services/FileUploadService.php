<?php

namespace App\Services;

use App\Services\Contracts\FileUploadServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
            $uploadsDirectory = Storage::disk('local')->path('uploads');
            if (!is_dir($uploadsDirectory)) {
                mkdir($uploadsDirectory, 0755, true);
            }

            // Combine all chunks to create the final file
            $finalFilePath = $uploadsDirectory . '/' . $originalFilename;
            $finalFile = fopen($finalFilePath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $chunksPath . '/' . $tempFilename . '.' . $i;
                $chunk = fopen($chunkPath, 'rb');
                while ($buffer = fread($chunk, 4096)) {
                    fwrite($finalFile, $buffer);
                }
                fclose($chunk);

                // Delete the chunk after appending it to the final file
                unlink($chunkPath);
            }

            fclose($finalFile);
        }

        return ['status' => 'success'];
    }
}