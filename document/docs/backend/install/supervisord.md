# supervisor

### 安装 supervisor
```sh
  pip install supervisor && echo_supervisord_conf > /etc/supervisord.conf
```

### 修改刚刚生成的配置文件最下面2行
```ini
[include]
files = supervisord.d/*.ini
```

### 配置文件
```ini
[program:laravel-shop-scout]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laravel-shop/artisan queue:work --sleep=3 --tries=3 --queue=scout
autostart=true
autorestart=true
user=www
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/laravel-shop/storage/logs/supervisord-scout.log
[program:laravel-shop-order]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laravel-shop/artisan queue:work --sleep=3 --tries=3 --queue=order
autostart=true
autorestart=true
user=www
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/laravel-shop/storage/logs/supervisord-order.log
```
### 启动
```shell
supervisord -c /etc/supervisord.conf
```
