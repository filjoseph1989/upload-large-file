<?php

namespace App\Http\Controllers;

use App\Services\FileUploadService;
use Illuminate\Http\Request;

class VideoUploadController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

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
