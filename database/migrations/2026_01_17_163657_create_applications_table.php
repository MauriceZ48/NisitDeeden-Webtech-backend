<?php

use App\Enums\ApplicationStatus;
use App\Enums\Domain;
use App\Models\ApplicationCategory;
use App\Models\ApplicationRound;
use App\Models\User;
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
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(ApplicationRound::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(ApplicationCategory::class)->constrained()->onDelete('cascade');
            $table->unique(['user_id', 'application_round_id']);

            $table->string('status')->default(ApplicationStatus::PENDING->value);
            $table->string('domain')->default(Domain::BANGKHEN->value);

            $table->text('rejection_reason')->nullable();
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
