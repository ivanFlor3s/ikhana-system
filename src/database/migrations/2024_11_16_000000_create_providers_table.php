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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('fantasy_name')->nullable();
            $table->string('business_name');
            $table->string('cuit')->unique();
            $table->string('iibb')->nullable();
            $table->foreignId('tax_status_id')->nullable()->constrained('tax_statuses')->nullOnDelete();
            $table->foreignId('agreement_id')->nullable()->constrained('agreements')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('phone_3')->nullable();
            $table->string('phone_4')->nullable();
            $table->string('phone_5')->nullable();
            $table->string('email_1')->nullable();
            $table->string('email_2')->nullable();
            $table->string('email_3')->nullable();
            $table->string('email_4')->nullable();
            $table->string('email_5')->nullable();
            
            // Dirección y web
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            
            // Contacto
            $table->string('contact_name')->nullable();
            
            // Observaciones
            $table->text('observations')->nullable();
            
            // Horarios
            $table->time('business_hours_start')->nullable();
            $table->time('business_hours_end')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};

