<?php

declare(strict_types=1);

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Resources\CommonResourceCollection;
use App\Models\MaterialFile;
use App\Services\MaterialFileService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MaterialFileController extends BaseController
{
    // 列表数据
    public function index(Request $request)
    {
        $parent_id = $request->get('parent_id');
        $name = $request->get('name');
        $admin_name = $request->get('admin_name');
        $type = $request->get('type');
        $dir_type = $request->get('dir_type');
        $time = $request->get('time');
        $start_time = $time[0] ?? '';
        $end_time = $time[1] ?? '';
        $sort = $request->get('sort');
        $list = MaterialFile::query()
            ->with('admin_user:id,user_name')
            ->when($parent_id > -1, fn (Builder $query) => $query->whereParentId($parent_id))
            ->when($name, fn (Builder $query) => $query->whereLike('name', "%{$name}%"))
            ->when($type, fn (Builder $query) => $query->whereType($type))
            ->when($dir_type, fn (Builder $query) => $query->whereDirType($dir_type))
            ->when($start_time, fn (Builder $query) => $query->where('created_at', '>=', $start_time.' 00:00:00'))
            ->when($end_time, fn (Builder $query) => $query->where('created_at', '<=', $end_time.' 23:59:59'))
            ->when($admin_name, fn (Builder $query) => $query
                ->whereHas('admin_user', fn (Builder $query) => $query
                    ->whereLike('user_name', "%{$admin_name}%")
                )
            )
            ->when(in_array($sort, MaterialFile::$sorts), fn (Builder $query) => $query->orderByRaw(MaterialFile::$orderBy[$sort]))
            ->paginate($request->get('number', 10));
        $list->getCollection()->transform(function (MaterialFile $faterialFile) {
            $faterialFile->size = $faterialFile->size . 'KB';

            return $faterialFile;
        });

        return $this->success(new CommonResourceCollection($list));
    }

    // 新建文件夹
    public function newFolder(Request $request)
    {
        try {
            // 验证请求参数
            $data = $request->validate([
                'name' => 'required|string|max:20', // 文件夹名，最大长度 20
                'parent_id' => 'required|integer', // 上级文件夹 ID
                'dir_type' => 'required|integer', // 文件夹类型
            ], [], [
                'name' => '文件夹名',
                'parent_id' => '上级文件夹 ID',
                'dir_type' => '文件夹类型',
            ]);
            // 检查上级文件夹是否存在
            $parentId = $data['parent_id'];

            if ($parentId > 0) { // 如果 parent_id > 0，则需要验证上级文件夹是否存在
                $parentFolder = MaterialFile::find($parentId);

                if (! $parentFolder || $parentFolder->type !== MaterialFile::TYPE_DIR) {
                    throw new BusinessException('上级文件夹无效');
                }
            }

            // 创建新文件夹
            $newFolder = new MaterialFile;
            $newFolder->name = $data['name'];
            $newFolder->admin_user_id = $request->user()?->id ?: 0;
            $newFolder->parent_id = $parentId == -1 ? 0 : $parentId;
            $newFolder->dir_type = $data['dir_type'];
            $newFolder->type = MaterialFile::TYPE_DIR; // 设置为目录类型

            if (! $newFolder->save()) {
                throw new BusinessException('文件夹创建失败');
            }

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('保存失败');
        }
    }

    // 侧边目录
    public function folderList(MaterialFileService $materialFileService)
    {
        // 获取所有目录，并按最新记录排序
        $materialFile = MaterialFile::query()
            ->whereType(MaterialFile::TYPE_DIR)
            ->select('id', 'name', 'parent_id', 'dir_type')
            ->latest()->get();

        $groupFile = $materialFile->groupBy('dir_type');
        $formattedData = [];

        foreach ($groupFile as $dir_type => $materialFile) {
            $top_dir = MaterialFile::$dirTopManage[$dir_type];
            $top_dir['children'] = $materialFileService->buildTree($materialFile->toArray());
            $formattedData[] = $top_dir;
        }

        return $this->success($formattedData);
    }

    // 根据文件夹类型获取目录
    public function folderListForDirType(Request $request, MaterialFileService $materialFileService)
    {
        try {
            return $this->success($materialFileService->getFolderList((int) $request->get('dir_type')));
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取文件夹目录失败!');
        }
    }

    // 重新命名
    public function rename(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'required|integer',
                'name' => 'required|string',
            ], [], [
                'id' => 'ID',
                'name' => '文件名',
            ]);
            $materialFile = MaterialFile::find($data['id']);

            if (! $materialFile) {
                throw new BusinessException('文件不存在');
            }
            $materialFile->name = $data['name'];

            if (! $materialFile->save()) {
                throw new BusinessException('保存失败');
            }

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('保存失败');
        }
    }

    // 删除
    public function destory(Request $request)
    {
        try {
            $id = $request->get('id');
            $materialFile = MaterialFile::find($id);

            if (! $materialFile) {
                throw new BusinessException('文件不存在');
            }
            $this->deleteWithChildren($materialFile);

            // 删除文件
            if (! $materialFile->delete()) {
                throw new BusinessException('删除失败');
            }

            return $this->success('删除成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('删除失败');
        }
    }

    // 批量删除
    public function batchDestroy(Request $request)
    {
        try {
            $ids = $request->get('ids');
            $materialFiles = MaterialFile::whereIn('id', $ids)->get();

            if ($materialFiles->isEmpty()) {
                throw new BusinessException('文件不存在');
            }

            // 遍历每个文件并递归删除其子集
            foreach ($materialFiles as $materialFile) {
                $this->deleteWithChildren($materialFile);
            }

            return $this->success('删除成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('删除失败');
        }
    }

    // 移动至
    public function move(Request $request)
    {
        try {
            // 验证请求参数
            $data = $request->validate([
                'id' => 'required|integer', // 要移动的文件或文件夹 ID
                'target_directory_id' => 'required|integer', // 目标文件夹 ID
            ], [], [
                'id' => '文件/文件夹 ID',
                'target_directory_id' => '目标文件夹 ID',
            ]);

            // 获取要移动的文件或文件夹
            $materialFile = MaterialFile::find($data['id']);

            if (! $materialFile) {
                throw new BusinessException('文件或文件夹不存在');
            }

            // 获取目标文件夹
            $targetDirectoryId = $data['target_directory_id'];

            if ($targetDirectoryId) {
                $targetDirectory = MaterialFile::find($targetDirectoryId);

                if (! $targetDirectory || $targetDirectory->type !== MaterialFile::TYPE_DIR) {
                    throw new BusinessException('目标文件夹无效');
                }

                // 检查是否会导致循环嵌套
                if ($this->isCircularReference($materialFile, $targetDirectory)) {
                    throw new BusinessException('无法移动到子文件夹中');
                }
            }

            // 更新父级关系
            $materialFile->parent_id = $targetDirectoryId;

            if (! $materialFile->save()) {
                throw new BusinessException('移动失败');
            }

            return $this->success('移动成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('移动失败');
        }
    }

    public function batchMove(Request $request)
    {
        try {
            // 验证请求参数
            $data = $request->validate([
                'ids' => 'required|array', // 要移动的文件或文件夹 ID 列表
                'target_directory_id' => 'required|integer', // 目标文件夹 ID
            ], [], [
                'ids' => '文件/文件夹 ID 列表',
                'target_directory_id' => '目标文件夹 ID',
            ]);
            // 获取目标文件夹
            $targetDirectoryId = $data['target_directory_id'];
            $targetDirectory = MaterialFile::find($targetDirectoryId);

            if (! $targetDirectory || $targetDirectory->type !== MaterialFile::TYPE_DIR) {
                throw new BusinessException('目标文件夹无效');
            }

            // 获取要移动的文件或文件夹
            $ids = $data['ids'];
            $materialFiles = MaterialFile::whereIn('id', $ids)->get();

            if ($materialFiles->isEmpty()) {
                throw new BusinessException('文件或文件夹不存在');
            }

            // 遍历每个文件或文件夹并检查是否会导致循环嵌套
            foreach ($materialFiles as $materialFile) {
                if ($this->isCircularReference($materialFile, $targetDirectory)) {
                    throw new BusinessException("文件/文件夹 ID {$materialFile->id} 无法移动到目标文件夹中");
                }
            }

            // 更新父级关系
            foreach ($materialFiles as $materialFile) {
                $materialFile->parent_id = $targetDirectoryId;

                if (! $materialFile->save()) {
                    throw new BusinessException("文件/文件夹 ID {$materialFile->id} 移动失败");
                }
            }

            return $this->success('移动成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('移动失败');
        }
    }

    /**
     * 素材上传
     * @throws BusinessException
     */
    public function upload(Request $request, MaterialFileService $materialFileService)
    {
        try {
            $file = $request->file('file');
            $dir_type = $request->get('dir_type');
            $parent_id = $request->get('parent_id');
            $admin_user_id = $request->user()?->id ?: 0;

            if (! $file || ! $file->isValid()) {
                return $this->error('文件上传失1败');
            }
            $res = $materialFileService->saveMaterialFile($admin_user_id, $file, $dir_type, $parent_id);

            if (!$res) {
                return $this->error('上传失败');
            }

            return $this->success('上传成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('上传失败');
        }
    }

    /**
     * 递归删除文件及其子集.
     */
    private function deleteWithChildren(MaterialFile $materialFile)
    {
        // 如果是目录，递归删除其子文件和子目录
        if ($materialFile->type == MaterialFile::TYPE_DIR) {
            foreach ($materialFile->children as $child) {
                $this->deleteWithChildren($child);
            }
        }

        // 删除当前文件或目录
        $materialFile->delete();
    }

    /**
     * 检查是否会导致循环嵌套.
     */
    private function isCircularReference(MaterialFile $source, MaterialFile $target): bool
    {
        while ($target?->parent_id !== null) {
            if ($target?->parent_id === $source->id) {
                return true;
            }
            $target = MaterialFile::find($target->parent_id);
        }

        return false;
    }
}
