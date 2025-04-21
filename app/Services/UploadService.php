<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * 文件上传.
     *
     * @throws BusinessException
     */
    public function uploadFile(UploadedFile $file): string
    {
        $storage = Storage::disk();

        $upload_path = date('Y/m/d');

        $file_path = $storage->put($upload_path, $file);

        if (! $file_path) {
            throw new BusinessException('文件上传失败~');
        }

        return $storage->url($file_path);
    }
}
