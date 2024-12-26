<?php

namespace App\Helper;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageFake
{
    public static function createImage($dir = '', $width = 70, $height = 70)
    {
        $imageUrl = "https://loremflickr.com/$width/$height/face";

        $randomName = Str::random(10) . '.jpg';

        $filePath = "$dir/$randomName";
        Storage::disk('public')->put($filePath, file_get_contents($imageUrl));

        return $filePath;
    }
}
