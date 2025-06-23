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
            create table error_log
            (
                id              UUID     default generateUUIDv4(),
                channel         String,
                level           String,
                message         String,
                context         String,
                extra           String,
                datetime        DateTime default now()
            )
                engine = MergeTree PARTITION BY toYYYYMM(datetime)
                    ORDER BY toYYYYMM(datetime)
                    SETTINGS index_granularity = 8192;
            SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        self::write('DROP TABLE error_log');
    }
};
