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
        Schema::create('application_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_attribute_id')->constrained()->onDelete('cascade');
            $table->text('value'); // Stores the answer or file path
            $table->timestamps();
        });
    }

    /**cd P
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_attribute_values');
    }
};
