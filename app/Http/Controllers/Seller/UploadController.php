<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends BaseController
{
    public function upload(Request $request)
    {
        $file = $request->file('file');

        if (! $file || ! $file->isValid()) {
            return $this->error('文件上传失败');
        }
        $storage = Storage::disk();

        $file_path = $storage->put('seller/' . date('Y/m/d'), $file);

        if (! $file_path) {
            return $this->error('文件上传失败~');
        }
        $url = $storage->url($file_path);

        return $this->success(['url' => $url]);
    }
}
