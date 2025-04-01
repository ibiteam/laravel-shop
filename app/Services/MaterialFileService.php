<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\MaterialFile;

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
}
