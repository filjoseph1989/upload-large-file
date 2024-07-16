<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoUploadController extends Controller
{
    public function uploadChunk(Request $request)
    {
        \Log::info('Received chunk upload request for file ' . $request->input('originalFilename'));

        $file = $request->file('file');
        $chunkIndex = $request->input('chunk');
        $totalChunks = $request->input('totalChunks');
        $originalFilename = $request->input('originalFileName');
        $originalFilename = str_replace(' ', '_', $originalFilename);

        // Generate a unique filename for the file
        $tempFilename = 'upload_' . md5($originalFilename) . '.tmp';

        // Store the chunk temporarily
        $file->storeAs('chunks', $tempFilename . '.' . $chunkIndex);

        // Check if all chunks are uploaded
        $chunksPath = storage_path('app/chunks');
        $uploadedChunks = glob($chunksPath . '/' . $tempFilename . '.*');

        if (count($uploadedChunks) == $totalChunks) {
            $uploadsDirectory = storage_path('app/uploads');
            if (!is_dir($uploadsDirectory)) {
                mkdir($uploadsDirectory, 0755, true);
            }

            // Combine all chunks to create the final file
            $finalFilePath = storage_path('app/uploads') . '/' . $originalFilename;
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
            \Log::info('Uploaded file ' . $originalFilename . ' successfully');
        }

        return response()->json(['status' => 'success']);
    }
}
