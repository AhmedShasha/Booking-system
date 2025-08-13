<?php

use App\Enums\DayOfWeek;
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
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->enum('day_of_week', DayOfWeek::values())->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('recurring')->default(true);
            $table->date('override_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
