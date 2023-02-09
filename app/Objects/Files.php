<?php

namespace App\Objects;

use App\Models\Image;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as Intervention;

class Files
{
    protected static $CROP = [
        [
            'width' => 180,
            'height' => 180,
        ],
        [
            'width' => 360,
            'height' => 360,
        ],
        [
            'width' => 400,
            'height' => 400,
        ],
        [
            'width' => 620,
            'height' => 400,
        ],
        [
            'width' => 800,
            'height' => 800,
        ],
    ];

    public function getFilePath($modelPhoto): ?string
    {

        $path = $this->getOptimizeDirectoryS3($modelPhoto->name);
        if (Storage::exists($path . '_' . 400 . 'x' . 400 . '.' . 'jpg')) {
            $url = Storage::url($path . '_'
                . 400 . 'x'
                . 400 . '.' .
                'jpg');
        }

        return $url ?? null;
    }

    public function getFileContentBig($modelPhoto): ?string
    {

        $path = $this->getOptimizeDirectoryS3($modelPhoto->name);
        if (Storage::exists($path . '_' . 800 . 'x' . 800 . '.' . 'jpg')) {
            $url = Storage::get($path . '_'
                . 800 . 'x'
                . 800 . '.' .
                'jpg');
        }

        return $url ?? null;
    }

    public function getFilePathBig($modelPhoto): ?string
    {

        $path = $this->getOptimizeDirectoryS3($modelPhoto->name);
        if (Storage::exists($path . '_' . 800 . 'x' . 800 . '.' . 'jpg')) {
            $url = Storage::url($path . '_'
                . 800 . 'x'
                . 800 . '.' .
                'jpg');
        }

        return $url ?? null;
    }

    public function createFilePath(Image $modelPhoto, int $width, int $height): ?string
    {

        $path = $this->getOptimizeDirectoryS3($modelPhoto->name);
        $url = Storage::path($path . '_'
            . $width . 'x'
            . $height . '.' .
            'jpg');

        return $url;
    }

    public function save($model, ?array $files): void
    {

        if (isset($files) && count($files) > 0) {
            foreach ($files as $file) {
                $dataFile = $this->preparationFileS3($file);
                $model->image()->create([
                    'mimeType' => $dataFile['mineType'],
                    'extension' => $dataFile['extension'],
                    'name' => $dataFile['name'],
                    'uniqueValue' => $dataFile['name'],
                    'size' => $dataFile['size'],
                ]);
            }
        }
    }

    public function saveParser($model, $url): void
    {

        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

            $contents = @file_get_contents($url, false, stream_context_create($arrContextOptions));
        if ($contents !== false) {
            $name = Str::random(40);
            $mineType = 'image/jpeg';
            $extension = 'jpg';
            $size = 999;
            $path = $this->getPathS3($name);

            Storage::put("{$path}.{$extension}", $contents);

            foreach (self::$CROP as $resolution) {
                $image = Intervention::make($contents);
                $filteredImage = $image
                    ->fit($resolution['width'], $resolution['height'])
                    ->encode('jpg', 100);
//                Log::info($path);
//                Log::info('');
                Storage::put(
                    $path . '_'
                    . $resolution['width'] . 'x'
                    . $resolution['height'] . '.' .
                    'jpg',
                    $filteredImage
                );
            }

            if ($model->images->count() < 15) {
                $model->image()->create([
                    'mimeType' => $mineType,
                    'extension' => $extension,
                    'name' => $name,
                    'size' => $size,
                ]);
            }
        }
    }

    protected function getFileType(string $nameWithType): string
    {
        $explodeAvatar = explode('.', $nameWithType);

        if (!is_array($explodeAvatar) && count($explodeAvatar) < 1) {
            throw new ModelNotFoundException(__('errors.not_found'), 404);
        }
        return $explodeAvatar[1];
    }

    /**
     * @param string|null $fileName
     * @return string
     * @throws \Exception
     */
    protected function generateFileName(?string $fileName = null): string
    {
        return md5(microtime() . random_int(0, 9999));
    }

    protected function getPathS3(string $nameFile): string
    {
        return $this->getOptimizeDirectoryS3($nameFile);
    }

    protected function getOptimizeDirectoryS3($string): string
    {
        $dir = '';
        if (mb_strlen($string, 'utf-8') > 4) {
            $dir = mb_substr($string, 0, 2, 'utf-8') . '/' .  mb_substr($string, 2, 2, 'utf-8') . '/';
        }

        return $dir . $string . '/' . $string;
    }

    /**
     * @param UploadedFile $file
     * @return array
     * @throws \Exception
     */
    public function preparationFileS3(
        UploadedFile $file
    ): array {
        $extension = $this->getFileType($file->getClientOriginalName());
        $name = $this->generateFileName();
        $mineType = $file->getClientMimeType();
        $sizeFile = $file->getSize();
        $path = $this->getPathS3($name);
        Storage::putFileAs("{$path}.{$extension}", $file, $name);

        foreach (self::$CROP as $resolution) {
            $image = Intervention::make($file);
            $filteredImage = $image
                ->fit($resolution['width'], $resolution['height'])
                ->encode($extension, 100);
            Storage::put(
                $path . '_'
                . $resolution['width'] . 'x'
                . $resolution['height'] . '.' .
                $extension,
                $filteredImage
            );
        }

        return [
            'mineType' => $mineType,
            'extension' => $extension,
            'size' => $sizeFile,
            'name' => $name,
            'path' => $path,
        ];
    }
}
