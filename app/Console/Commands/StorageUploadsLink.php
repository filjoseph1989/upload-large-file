<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StorageUploadsLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:storage-uploads-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link from public/uploads to storage/app/uploads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $target = storage_path('app/uploads');
        $link = public_path('uploads');

        if (File::exists($link)) {
            $this->error('The "public/uploads" directory already exists.');
            return;
        }

        File::link($target, $link);

        $this->info('The [public/uploads] directory has been linked.');
    }
}
