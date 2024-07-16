<?php

namespace App\Services;

class FileUploadService
{
    public function uploadChunk($file, $chunkIndex, $totalChunks, $originalFilename)
    {
        // Generate a unique temporary filename
        $tempFilename = 'upload_' . md5($originalFilename) . '.tmp';

        // Store the chunk temporarily
        $file->storeAs('chunks', $tempFilename . '.' . $chunkIndex);

        // Check if all chunks are uploaded
        $chunksPath = storage_path('app/chunks');
        $uploadedChunks = glob($chunksPath . '/' . $tempFilename . '.*');

        if (count($uploadedChunks) == $totalChunks) {
            // Ensure the uploads directory exists
            $uploadsDirectory = storage_path('app/uploads');
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