<?php

namespace App\Objects;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
            'width' => 800,
            'height' => 800,
        ],
    ];

    public function getFilePath($modelPhoto): ?string
    {

        $path = $this->getOptimizeDirectoryS3($modelPhoto->name);
        $url = Storage::url($path . '_'
            . 400 . 'x'
            . 400 . '.' .
            'jpg');

        return $url;
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


        foreach (self::$CROP as $resolution) {
            $image = \Intervention\Image\Facades\Image::make($file);
            $filteredImage = $image
                ->fit($resolution['width'], $resolution['height'])
                ->encode('jpg', 100);
            Storage::put(
                $path . '_'
                . $resolution['width'] . 'x'
                . $resolution['height'] . '.' .
                'jpg',
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
