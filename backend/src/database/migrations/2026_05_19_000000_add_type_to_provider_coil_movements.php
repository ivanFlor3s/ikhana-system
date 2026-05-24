<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('provider_coil_movements', function (Blueprint $table) {
            $table->string('type', 20)->default('entry')->after('raw_material_entry_id');
        });
    }

    public function down(): void
    {
        Schema::table('provider_coil_movements', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
