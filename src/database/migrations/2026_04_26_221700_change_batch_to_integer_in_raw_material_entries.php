<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clean existing non-numeric batch values so the column can be cast to integer
        // Extract numeric portion or set to NULL if no digits found
        DB::statement("
            UPDATE raw_material_entries
            SET batch = CASE
                WHEN batch REGEXP '[0-9]' THEN CAST(REGEXP_REPLACE(batch, '[^0-9]', '') AS UNSIGNED)
                ELSE NULL
            END
        ");

        Schema::table('raw_material_entries', function (Blueprint $table) {
            $table->unsignedInteger('batch')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_material_entries', function (Blueprint $table) {
            $table->string('batch', 50)->nullable()->change();
        });
    }
};
