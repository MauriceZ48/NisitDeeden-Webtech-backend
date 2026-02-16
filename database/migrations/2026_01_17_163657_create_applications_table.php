<?php

use App\Enums\ApplicationStatus;
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
        Schema::create('applications', function (Blueprint $table) {

            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\ApplicationRound::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\ApplicationCategory::class)->constrained()->onDelete('cascade');
            $table->unique(['user_id', 'application_round_id']);

            $table->string('status')->default(ApplicationStatus::PENDING->value);

            $table->text('rejection_reason')->nullable();
            $table->string('transcript_path')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
