<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cobre entry details
        Schema::create('cobre_entry_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_entry_id')->constrained()->onDelete('cascade')->unique();
            $table->foreignId('raw_material_characteristic_id')->constrained();
            $table->decimal('quantity_kg', 12, 3)->nullable();
            $table->integer('coils_count')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Cuerda entry details (same structure for now)
        Schema::create('cuerda_entry_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_entry_id')->constrained()->onDelete('cascade')->unique();
            $table->foreignId('raw_material_characteristic_id')->constrained();
            $table->decimal('quantity_kg', 12, 3)->nullable();
            $table->integer('coils_count')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Cobre tests
        Schema::create('cobre_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_entry_id')->constrained()->onDelete('cascade')->unique();
            $table->date('test_date');
            $table->decimal('resistance_ohm_km', 10, 2);
            $table->decimal('elongation_pct', 5, 2)->nullable();
            $table->boolean('check_winding')->nullable();
            $table->boolean('check_cleanliness')->nullable();
            $table->boolean('check_packaging')->nullable();
            $table->boolean('check_identification')->nullable();
            $table->string('result', 10);
            $table->string('conducted_by', 120)->nullable();
            $table->string('approved_by', 120)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Cuerda tests (same structure for now)
        Schema::create('cuerda_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_entry_id')->constrained()->onDelete('cascade')->unique();
            $table->date('test_date');
            $table->decimal('resistance_ohm_km', 10, 2);
            $table->decimal('elongation_pct', 5, 2)->nullable();
            $table->boolean('check_winding')->nullable();
            $table->boolean('check_cleanliness')->nullable();
            $table->boolean('check_packaging')->nullable();
            $table->boolean('check_identification')->nullable();
            $table->string('result', 10);
            $table->string('conducted_by', 120)->nullable();
            $table->string('approved_by', 120)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. IRAM for Cuerda
        Schema::create('iram_cuerda_max_resistances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_characteristic_id');
            $table->foreign('raw_material_characteristic_id', 'fk_iram_cuerda_char')
                ->references('id')
                ->on('raw_material_characteristics')
                ->onDelete('cascade');
            $table->unique('raw_material_characteristic_id', 'uk_iram_cuerda_char');
            $table->decimal('max_resistance_ohm_km', 10, 2);
            $table->timestamps();
        });

        // 6. Migrate existing data: entries -> cobre_entry_details
        $cobreType = DB::table('raw_material_types')->where('name', 'Cobre')->first();
        if ($cobreType) {
            DB::statement("
                INSERT INTO cobre_entry_details
                    (raw_material_entry_id, raw_material_characteristic_id, quantity_kg, coils_count, created_at, updated_at)
                SELECT id, raw_material_characteristic_id, quantity_kg, coils_count, created_at, updated_at
                FROM raw_material_entries
                WHERE raw_material_type_id = {$cobreType->id}
            ");

            // 7. Migrate existing data: material_tests -> cobre_tests
            DB::statement("
                INSERT INTO cobre_tests
                    (raw_material_entry_id, test_date, resistance_ohm_km, elongation_pct,
                     check_winding, check_cleanliness, check_packaging, check_identification,
                     result, conducted_by, approved_by, created_at, updated_at)
                SELECT raw_material_entry_id, test_date, resistance_ohm_km, elongation_pct,
                       check_winding, check_cleanliness, check_packaging, check_identification,
                       result, conducted_by, approved_by, created_at, updated_at
                FROM material_tests
            ");
        }

        // 8. Drop old columns from raw_material_entries
        Schema::table('raw_material_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('raw_material_characteristic_id');
            $table->dropColumn('quantity_kg');
            $table->dropColumn('coils_count');
        });

        // 9. Drop old material_tests table
        Schema::dropIfExists('material_tests');
    }

    public function down(): void
    {
        // Recreate material_tests
        Schema::create('material_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_entry_id')->constrained()->onDelete('cascade')->unique();
            $table->date('test_date');
            $table->decimal('resistance_ohm_km', 10, 2);
            $table->decimal('elongation_pct', 5, 2)->nullable();
            $table->boolean('check_winding')->nullable();
            $table->boolean('check_cleanliness')->nullable();
            $table->boolean('check_packaging')->nullable();
            $table->boolean('check_identification')->nullable();
            $table->string('result', 10);
            $table->string('conducted_by', 120)->nullable();
            $table->string('approved_by', 120)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Restore columns to raw_material_entries
        Schema::table('raw_material_entries', function (Blueprint $table) {
            $table->foreignId('raw_material_characteristic_id')->nullable()->constrained();
            $table->decimal('quantity_kg', 12, 3)->nullable();
            $table->integer('coils_count')->nullable();
        });

        // Drop new tables
        Schema::dropIfExists('cobre_tests');
        Schema::dropIfExists('cuerda_tests');
        Schema::dropIfExists('cobre_entry_details');
        Schema::dropIfExists('cuerda_entry_details');
        Schema::dropIfExists('iram_cuerda_max_resistances');
    }
};
