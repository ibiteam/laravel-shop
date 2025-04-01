<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property string $admin_user_id 添加人
 * @property string $name 素材名
 * @property int|null $size 素材大小
 * @property int|null $width 素材宽度
 * @property int|null $height 素材高度
 * @property int|null $file_path 素材地址
 * @property int $type 素材类型 1、文件夹 2、素材
 * @property int $parent_id 父级ID
 * @property int $dir_type 1、图片，2、视频
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminUser|null $admin_user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MaterialFile> $children
 * @property-read int|null $children_count
 * @property-read MaterialFile|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereDirType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile whereWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialFile withoutTrashed()
 * @mixin \Eloquent
 */
class MaterialFile extends Model
{
    use DatetimeTrait, softDeletes;

    protected $guarded = [];


    // 文件夹
    public const TYPE_DIR = 1;

    // 素材
    public const TYPE_FILE = 2;

    // 图片
    public const DIR_TYPE_IMAGE = 1;
    // 视频
    public const DIR_TYPE_VIDEO = 2;

    public static $dirType = [
        self::DIR_TYPE_IMAGE => '图片',
        self::DIR_TYPE_VIDEO => '视频',
    ];

    public static $dirTopManage = [
        self::DIR_TYPE_IMAGE => [
            "id" => 0,
            "name" => "图片管理",
            "parent_id" => -1,
            "dir_type" => 1,
            "children" => []
        ],
        self::DIR_TYPE_VIDEO => [
            "id" => 0,
            "name" => "视频管理",
            "parent_id" => -1,
            "dir_type" => 2,
            "children" => []
        ],
    ];

    // 排序
    public static $orderBy = [
        '1' => 'created_at desc',
        '2' => 'created_at asc',
        '3' => 'updated_at desc',
        '4' => 'updated_at asc',
        '5' => 'name COLLATE utf8mb4_general_ci desc',
        '6' => 'name COLLATE utf8mb4_general_ci asc',
    ];

    /*
     * 排序数组
     * 最新上传在前 1
     * 最新上传在后 2
     * 最新更新在前 3
     * 最新更新在后 4
     * 按文件名降序 5
     * 按文件名升序 6
     */
    public static $sorts = [1, 2, 3, 4, 5, 6];

    public function admin_user()
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(MaterialFile::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(MaterialFile::class, 'parent_id', 'id');
    }
}
