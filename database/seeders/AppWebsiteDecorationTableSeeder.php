<?php

namespace Database\Seeders;

use App\Models\AppWebsiteDecoration;
use Illuminate\Database\Seeder;

class AppWebsiteDecorationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //买家版
        $this->save('首页', AppWebsiteDecoration::ALIAS_HOME, [AppWebsiteDecoration::TYPE_BOTTOM_MENU], true);
    }

    private function save(string $name, string $alias, array $type, bool $is_show, $version = AppWebsiteDecoration::VERSION_BUYER, $title = '', $description = '', $keywords = '', $parent_id = 0)
    {
        $app_website_decoration = AppWebsiteDecoration::query()->firstOrNew(['alias' => $alias]);
        if (!$app_website_decoration->exists) {
            $app_website_decoration->name = $name;
            $app_website_decoration->alias = $alias;
            $app_website_decoration->version = $version;
            $app_website_decoration->type = $type;
            $app_website_decoration->is_show = $is_show;
            $app_website_decoration->image_url = '';
            $app_website_decoration->admin_user_id = 0;
            $app_website_decoration->title = $title;
            $app_website_decoration->description = $description;
            $app_website_decoration->keywords = $keywords;
            $app_website_decoration->parent_id = $parent_id;
            $app_website_decoration->save();
        }
    }
}
