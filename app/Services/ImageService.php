<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function processAndStore(UploadedFile $file, string $directory = 'posts'): string
    {
        $image = $this->imageManager->read($file->getRealPath());
        
        $width = $image->width();
        $height = $image->height();
        
        if ($width > $height) {
            $image->resize(null, 1080, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $image->resize(1080, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        
        $image->cover(1080, 1080, 'center');
        
        $filename = uniqid() . '.jpg';
        $path = $directory . '/' . $filename;
        $fullPath = storage_path('app/public/' . $path);
        
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $image->toJpeg(85)->save($fullPath);
        
        return $path;
    }

    public function delete(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }
}

