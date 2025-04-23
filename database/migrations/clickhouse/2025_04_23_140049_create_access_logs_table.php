<?php

use PhpClickHouseLaravel\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        self::write(
            <<<'SQL'
            create table access_log
            (
                id              UUID     default generateUUIDv4(),
                user_id         String,
                ip              String,
                url             String,
                source          String,
                method          String,
                referer_url     String,
                user_agent      String,
                browser         String,
                system          String,
                request_data    String,
                access_datetime DateTime default now()
            )
                engine = MergeTree PARTITION BY toYYYYMM(access_datetime)
                    ORDER BY toYYYYMM(access_datetime)
                    SETTINGS index_granularity = 8192;
            SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        self::write('DROP TABLE IF EXISTS access_log');
    }
};
