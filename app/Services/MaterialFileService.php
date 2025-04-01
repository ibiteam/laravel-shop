<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\MaterialFile;
use Illuminate\Support\Facades\Storage;

class MaterialFileService
{
    /**
     * 获取图片目录
     * @param $dir_type
     * @return array
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
            ->select('id', 'name', 'parent_id', 'dir_type')
            ->latest()->get();
        $top_dir = MaterialFile::$dirTopManage[$dir_type];
        $top_dir['children'] = $this->buildTree($materialFile->toArray());

        return [$top_dir];
    }

    /**
     * 数据构建
     * @param array $elements
     * @param int|null $parentId
     * @return array
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
     * 保存素材文件
     * @param $admin_user_id
     * @param $file
     * @param $dir_type
     * @param $parent_id
     * @return MaterialFile
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

        // 初始化数据数组
        $data = [
            'type' => MaterialFile::TYPE_FILE,
            'parent_id' => $parent_id,
            'admin_user_id' => $admin_user_id,
            'name' => $fileName,
            'file_path' => $this->getFileUrl($file),
            'dir_type' => $dir_type,
            'size' => round($fileSize / 1024), // 存储 KB 大小
            'width' => null,
            'height' => null,
        ];

        if (MaterialFile::DIR_TYPE_IMAGE == $dir_type) {
            if (strpos($mimeType, 'image/') !== 0) {
                throw new BusinessException('文件类型不是图片 (MIME: ' . $mimeType . ')');
            }
            // 获取图片的宽高
            $imageInfo = getimagesize($file->getPathname());
            if ($imageInfo) {
                $data['width'] = $imageInfo[0];  // 宽度
                $data['height'] = $imageInfo[1]; // 高度
            } else {
                throw new BusinessException('无法解析图片信息');
            }
        } elseif (MaterialFile::DIR_TYPE_VIDEO == $dir_type) {
            if (strpos($mimeType, 'video/') !== 0) {
                throw new BusinessException('文件类型不是视频 (MIME: ' . $mimeType . ')');
            }
        } else {
            throw new BusinessException('文件夹类型必须为图片或者视频');
        }

        return MaterialFile::create($data);
    }

    public function getFileUrl($file): string
    {
        $storage = Storage::disk();

        $file_path = $storage->put(config('app.manage_prefix') . '/' . date('Y/m/d'), $file);

        if (! $file_path) {
            throw new BusinessException('文件上传失败~');
        }

        return $storage->url($file_path);
    }
}
