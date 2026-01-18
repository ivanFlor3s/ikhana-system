<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tipos de Materia Prima (Cobre, PVC, etc.)
        Schema::create('raw_material_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Características (Diámetro, Color, etc.)
        Schema::create('raw_material_characteristics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_type_id')->constrained()->onDelete('cascade');
            $table->string('name', 50); // "Diámetro", "Color"
            $table->decimal('decimal_value', 10, 3)->nullable(); // 0.350
            $table->string('text_value', 100)->nullable(); // "Rojo"
            $table->string('unit', 20)->nullable(); // "mm"

            $table->timestamps();
            $table->softDeletes();

            // Evitar duplicados
            $table->unique(['raw_material_type_id', 'name', 'decimal_value'], 'uk_type_name_dec');
            $table->unique(['raw_material_type_id', 'name', 'text_value'], 'uk_type_name_txt');
        });

        // 3. Regla IRAM específica para Cobre
        Schema::create('iram_copper_max_resistances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_characteristic_id');
            $table->foreign('raw_material_characteristic_id', 'fk_iram_char') // Shortened Constraint Name
                ->references('id')
                ->on('raw_material_characteristics')
                ->onDelete('cascade');

            $table->unique('raw_material_characteristic_id', 'uk_iram_char'); // Shortened Unique Index Name

            $table->decimal('max_resistance_ohm_km', 10, 2);

            $table->timestamps();
        });

        // 4. Entradas de Materia Prima
        Schema::create('raw_material_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_type_id')->constrained();
            $table->foreignId('provider_id')->constrained();
            // Característica elegida (ej: Diámetro 0.35mm)
            $table->foreignId('raw_material_characteristic_id')->constrained();

            $table->integer('entry_number')->nullable(); // Número de ingreso interno
            $table->string('remito', 50)->nullable();
            $table->string('batch', 50)->nullable(); // Lote
            $table->date('entry_date');

            $table->decimal('quantity_kg', 12, 3)->nullable(); // Peso en KG
            $table->integer('coils_count')->nullable(); // Cantidad de bobinas

            $table->text('observations')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['raw_material_type_id', 'entry_date']);
        });

        // 5. Ensayos (Tests de Calidad)
        Schema::create('material_tests', function (Blueprint $table) {
            $table->id();
            // Relación 1:1 con la entrada
            $table->foreignId('raw_material_entry_id')
                ->constrained()
                ->onDelete('cascade')
                ->unique();

            $table->date('test_date');

            // Mediciones
            $table->decimal('resistance_ohm_km', 10, 2); // Resistencia medida
            $table->decimal('elongation_pct', 5, 2)->nullable(); // Estiramiento %

            // Chequeos visuales (Booleanos)
            $table->boolean('check_winding')->nullable(); // Bobinado
            $table->boolean('check_cleanliness')->nullable(); // Limpieza
            $table->boolean('check_packaging')->nullable(); // Acondicionado
            $table->boolean('check_identification')->nullable(); // Identificación

            $table->string('result', 10); // "OK" / "NO_OK"

            $table->string('conducted_by', 120)->nullable(); // Realizado por
            $table->string('approved_by', 120)->nullable(); // Controlado/Aprobado por

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_tests');
        Schema::dropIfExists('raw_material_entries');
        Schema::dropIfExists('iram_copper_max_resistances');
        Schema::dropIfExists('raw_material_characteristics');
        Schema::dropIfExists('raw_material_types');
    }
};
