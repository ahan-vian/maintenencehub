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
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('location_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('serial_number')->unique(); 
            $table->string('type')->nullable(); 
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');

            // untuk dipakai di minggu 3 (due schedule)
            $table->date('last_calibrated_at')->nullable();
            $table->unsignedInteger('calibration_interval_days')->default(90);
            $table->date('next_due_date')->nullable();

            $table->timestamps();

            // index untuk query umum
            $table->index(['location_id', 'status']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
