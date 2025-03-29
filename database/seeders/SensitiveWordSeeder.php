<?php

namespace Database\Seeders;

use App\Models\SensitiveWord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SensitiveWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!SensitiveWord::exists()) {
            $this->seedSensitiveWords();
        }
    }

    private function seedSensitiveWords()
    {
        DB::beginTransaction();

        try {
            $json = File::get(resource_path('File/sensitive_words.json'));
            $regions = json_decode($json, true);
            SensitiveWord::insert($regions);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
