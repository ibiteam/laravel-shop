<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
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
        $url = $upload_service->uploadFile($file);

        return $this->success(['url' => $url]);
    }
}
