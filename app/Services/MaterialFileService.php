<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\MaterialFile;
use Illuminate\Support\Facades\Storage;

class MaterialFileService
{
    /**
     * 获取图片目录.
     *
     * @throws BusinessException
     */
    public function getFolderList($dir_type): array
    {
        if (! in_array($dir_type, [MaterialFile::DIR_TYPE_IMAGE, MaterialFile::DIR_TYPE_VIDEO])) {
            throw new BusinessException('文件夹类型必须为图片或者视频');
        }

        $materialFile = MaterialFile::query()
            ->whereDirType($dir_type)
            ->whereType(MaterialFile::TYPE_DIR)
            ->select('id', 'name', 'parent_id', 'dir_type', 'type')
            ->latest()->get();
        $top_dir = MaterialFile::$dirTopManage[$dir_type];
        $top_dir['children'] = $this->buildTree($materialFile->toArray());

        return [$top_dir];
    }

    /**
     * 数据构建.
     */
    public function buildTree(array $elements, ?int $parentId = null): array
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);

                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    /**
     * 保存素材文件.
     *
     * @return MaterialFile
     *
     * @throws BusinessException
     */
    public function saveMaterialFile($admin_user_id, $file, $dir_type, $parent_id)
    {
        // 获取文件大小（单位：字节）
        $fileSize = $file->getSize();
        // 文件名称
        $fileName = $file->getClientOriginalName();
        // 获取文件的 MIME 类型
        $mimeType = $file->getMimeType();
        $file_url = app(UploadService::class)->uploadFile($file);

        // 初始化数据数组
        $data = [
            'type' => MaterialFile::TYPE_FILE,
            'parent_id' => $parent_id,
            'admin_user_id' => $admin_user_id,
            'name' => $fileName,
            'file_path' => $file_url,
            'dir_type' => $dir_type,
            'size' => round($fileSize / 1024), // 存储 KB 大小
            'width' => null,
            'height' => null,
        ];

        if ($dir_type == MaterialFile::DIR_TYPE_IMAGE) {
            // 限制图片大小最大为5M
            if ($fileSize > 5 * 1024 * 1024) {
                throw new BusinessException('图片大小不能超过5M');
            }

            if (strpos($mimeType, 'image/') !== 0) {
                throw new BusinessException('文件类型不是图片 (MIME: '.$mimeType.')');
            }
            // 获取图片的宽高
            $imageInfo = getimagesize($file->getPathname());

            if ($imageInfo) {
                $data['width'] = $imageInfo[0];  // 宽度
                $data['height'] = $imageInfo[1]; // 高度
            } else {
                throw new BusinessException('无法解析图片信息');
            }
        } elseif ($dir_type == MaterialFile::DIR_TYPE_VIDEO) {
            if (strpos($mimeType, 'video/') !== 0) {
                throw new BusinessException('文件类型不是视频 (MIME: '.$mimeType.')');
            }
        } else {
            throw new BusinessException('文件夹类型必须为图片或者视频');
        }

        return MaterialFile::create($data);
    }
}
