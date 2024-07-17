<?php

use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    Storage::fake('local');

    // Ensure directories exist in fake storage
    Storage::disk('local')->makeDirectory('chunks');
    Storage::disk('local')->makeDirectory('uploads');

    $this->service = new FileUploadService();
});

it('uploads a chunk correctly', function () {
    $file = UploadedFile::fake()->create('video.mp4', 1024);
    $chunkIndex = 0;
    $totalChunks = 1;
    $originalFilename = 'video.mp4';

    $response = $this->service->uploadChunk($file, $chunkIndex, $totalChunks, $originalFilename);

    // Assert final file is created
    Storage::disk('local')->assertExists('uploads/video.mp4');

    // Assert response
    expect($response)->toBe([
        'status' => 'success',
        'url' => config('app.url').'/uploads/video.mp4',
    ]);
});

it('combines multiple chunks correctly', function () {
    $chunkSize = 512;
    $chunk1 = UploadedFile::fake()->createWithContent('chunk1', str_repeat('A', $chunkSize));
    $chunk2 = UploadedFile::fake()->createWithContent('chunk2', str_repeat('B', $chunkSize));
    $totalChunks = 2;
    $originalFilename = 'video.mp4';

    // Upload first chunk
    $this->service->uploadChunk($chunk1, 0, $totalChunks, $originalFilename);
    // Upload second chunk
    $response = $this->service->uploadChunk($chunk2, 1, $totalChunks, $originalFilename);

    // Assert final file is created and contents are combined correctly
    $finalFilePath = 'uploads/video.mp4';
    Storage::disk('local')->assertExists($finalFilePath);

    $finalContents = Storage::disk('local')->get($finalFilePath);
    expect($finalContents)->toBe(str_repeat('A', $chunkSize) . str_repeat('B', $chunkSize));

    // Assert response
    expect($response)->toBe([
        'status' => 'success',
        'url' => config('app.url') . '/uploads/video.mp4',
    ]);
});
