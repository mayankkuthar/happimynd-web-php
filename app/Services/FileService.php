<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Collections\Constants;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class FileService
{
    public function saveAsAsset($assetName, $key)
    {
        return $this->saveFile(config('constants.mediaAssets.' . $assetName . '.folderName'), $key);
    }

    public function saveFile($destinationPath, $key)
    {
        $fileName = time() . '-' . request()->file($key)->getClientOriginalName();
        if (request()->hasFile($key)) {
            request()->file($key)->storeAs($destinationPath, $fileName, 's3');
            return $fileName;
        }
        return false;
    }

    public function deleteAssetFile($assetName, $fileName)
    {
        $path = config('constants.mediaAssets.' . $assetName . '.folderName') . '/' . $fileName;
        if (Storage::disk('s3')->exists($path)) {
            return Storage::disk('s3')->delete($path);
        }
        return false;
    }

    public function checkSize($key, $fixedWidth, $fixedHeight){

        $fileName = request()->file($key)->path();
        $fileInfo = getimagesize($fileName);
        $width = $fileInfo[0];
        $height = $fileInfo[1];
        $size = filesize($fileName);
        if($width != $fixedWidth || $height != $fixedHeight)
        {
            return false;
        }
        return true;
    }

    public function getFilePath($file)
    {
        $filePath = \Storage::disk('public')->putFile($file->getClientOriginalName(), $file);
        $filePath = storage_path('app/public') . '/' . $filePath;
        return $filePath;
    }
}