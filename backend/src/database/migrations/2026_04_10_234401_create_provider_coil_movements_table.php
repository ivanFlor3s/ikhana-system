<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provider_coil_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            // nullable porque podrían haber movimientos manuales en un futuro
            $table->foreignId('raw_material_entry_id')->nullable()->constrained('raw_material_entries')->onDelete('set null');
            
            $table->integer('coils_received')->default(0);
            $table->integer('coils_returned')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_coil_movements');
    }
};
