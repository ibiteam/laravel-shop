<?php

namespace Database\Seeders;

use App\Models\AppDecoration;
use Illuminate\Database\Seeder;

class AppDecorationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->save('首页', AppDecoration::ALIAS_HOME,  true);
    }

    private function save(string $name, string $alias, bool $is_show, $title = '', $description = '', $keywords = '', $parent_id = 0)
    {
        $app_decoration = AppDecoration::query()->firstOrNew(['alias' => $alias]);
        if (!$app_decoration->exists) {
            $app_decoration->name = $name;
            $app_decoration->alias = $alias;
            $app_decoration->is_show = $is_show;
            $app_decoration->image_url = '';
            $app_decoration->admin_user_id = 0;
            $app_decoration->title = $title;
            $app_decoration->description = $description;
            $app_decoration->keywords = $keywords;
            $app_decoration->parent_id = $parent_id;
            $app_decoration->save();
        }
    }
}
