<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ shop_config(\App\Models\ShopConfig::SHOP_LOGO) }}">
        <title>{{ shop_config(\App\Models\ShopConfig::SHOP_NAME) }}-管理系统</title>
        @vite(['resources/manage/app.ts'])
    </head>
    <body>
    <div id="app"></div>
    </body>
</html>
