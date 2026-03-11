<?php

use App\Enums\Domain;
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
        Schema::create('application_rounds', function (Blueprint $table) {
            $table->id();

            $table->string('academic_year');
            $table->string('semester');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status');
            $table->string('domain')->default(Domain::BANGKHEN->value);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['academic_year', 'semester', 'domain'], 'round_year_sem_domain_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_rounds');
    }
};
