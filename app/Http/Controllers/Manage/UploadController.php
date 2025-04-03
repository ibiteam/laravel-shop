<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends BaseController
{
    /**
     * @throws BusinessException
     */
    public function upload(Request $request, UploadService $upload_service): JsonResponse
    {
        $file = $request->file('file');

        if (! $file || ! $file->isValid()) {
            return $this->error('文件上传失败');
        }

        $url = $upload_service->uploadFile($file, config('app.manage_prefix').'/'.date('Y/m/d'));

        return $this->success(['url' => $url]);
    }
}
