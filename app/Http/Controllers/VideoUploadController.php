<?php

namespace App\Http\Controllers;

use App\Services\Contracts\FileUploadServiceInterface;
use Illuminate\Http\Request;

class VideoUploadController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadServiceInterface $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Uploads a chunk of a file.
     *
     * @param Request $request The HTTP request object containing the file chunk, chunk index, total chunks, and original file name.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the result of the file upload.
     */
    public function uploadChunk(Request $request)
    {
        \Log::info('Received chunk upload request for file ' . $request->input('originalFilename'));

        $file = $request->file('file');
        $chunkIndex = $request->input('chunk');
        $totalChunks = $request->input('totalChunks');
        $originalFilename = $request->input('originalFileName');
        $originalFilename = str_replace(' ', '_', $originalFilename);

        $result = $this->fileUploadService->uploadChunk($file, $chunkIndex, $totalChunks, $originalFilename);

        return response()->json($result);
    }
}
