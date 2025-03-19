# 源码安装

安装

```sh
git clone git@github.com:ibiteam/laravel-shop.git
composer install
cp .env.example .env
```

本地nginx 配置

```sh
server {
    listen 80;

    server_name shop.host;
    client_max_body_size 50m;
    root "/home/www/Documents/php/laravel-shop/public";
    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
   error_log /var/log/nginx/ai-error.log;
    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_pass unix:/usr/local/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_read_timeout 600;
        include fastcgi_params;
    }
}

```
