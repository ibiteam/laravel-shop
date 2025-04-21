<?php

namespace App\Enums;

enum CacheNameEnum: string
{
    case REGION_TREE = 'cache_region_tree';

    case SHOP_CONFIG_ALL = 'shop_config_all_code';

    case ADMIN_PERMISSION_MENUS = 'admin_permission_menus';
}
