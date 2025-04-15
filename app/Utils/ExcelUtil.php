<?php

namespace App\Utils;

use App\Utils\Excel\ImportUtil;
use Illuminate\Http\UploadedFile;

class ExcelUtil
{
    public static function import(UploadedFile $uploaded_file, \Closure $callback)
    {
        try {
            $collection = (new ImportUtil)->toCollection($uploaded_file);

            return $callback($collection[0]);
        } catch (\Exception $exception) {
            return null;
        }
    }
}
