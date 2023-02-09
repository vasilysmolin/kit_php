<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Objects\Files;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as Intervention;

class ResizeImages extends Command
{
    protected $signature = 'resize-images  {--skip=} ';

    protected $description = 'Moves upload_files studios/masters to S3';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
//        $start = $this->getStartTime();
//        $skip = $this->option('skip');
        $images = Image::get();
        $bar = $this->output->createProgressBar($images->count());
        $bar->start();
        $files = new Files();
        $images->each(function ($image) use ($bar, $files) {
            $bigFile = $files->getFileContentBig($image);
            $bigFilePath = $files->getFilePathBig($image);
            $bar->advance();
            if (empty($bigFile)) {
                return;
            }

            $fitImage = Intervention::make($bigFilePath)
                ->fit(620, 400)
                ->encode('jpg', 100);

            $newPath = $files->createFilePath($image, 620, 400);

            Storage::put(
                $newPath,
                $fitImage
            );
        });

        $bar->finish();
    }
}
