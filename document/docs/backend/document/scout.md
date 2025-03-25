# Laravel Scout

### [文档地址](https://laravel.com/docs/contributions)

### scout settings
```dotenv
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://192.168.54.200:7700
MEILISEARCH_KEY=ibisaas-ibi603613
```
#### 相关命令与说明
```
# 启动队列
php artisan queue:work --queue=scout --tries=3
# 同步索引设置
php artisan scout:sync-index-settings
# 批量导入
php artisan scout:import "App\Models\AdminUser"
# 批量删除
php artisan scout:flush "App\Models\AdminUser"
```
```text
1、默认使用队列
2、当执行 create|save|update|delete 时，会自动删除索引中的数据，故而不要使用其他的处理，如：直接从数据库更新数据，这样不会更新索引中的数据
3、指定字段搜索时：
    如：指定 username 字段搜索，则：
        AdminUser::search('ch')->options(['attributesToSearchOn' => ['username']])->paginate(5,page: 1);
```
