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
        Schema::create('tax_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // Código del status (1, 2, 3, etc.)
            $table->string('name'); // Nombre completo
            $table->text('description')->nullable(); // Descripción adicional
            $table->boolean('is_active')->default(true); // Si está activo o no
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_statuses');
    }
};

