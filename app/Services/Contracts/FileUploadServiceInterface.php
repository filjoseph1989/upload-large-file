<?php

namespace App\Services\Contracts;

use Illuminate\Http\UploadedFile;

interface FileUploadServiceInterface
{
    public function uploadChunk(UploadedFile $file, int $chunkIndex, int $totalChunks, string $originalFilename): array;
}