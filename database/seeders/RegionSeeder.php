<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Region::exists()) {
            $this->seedRegions();
        }
    }

    private function seedRegions()
    {
        DB::beginTransaction();

        try {
            $json = File::get(resource_path('files/regions.json'));
            $regions = json_decode($json, true);
            Region::insert($regions);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
