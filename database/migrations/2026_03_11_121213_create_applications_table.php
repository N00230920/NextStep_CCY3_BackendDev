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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cv_id')->nullable()->constrained()->nullOnDelete();
        
            $table->string('company_name');
            $table->string('position');
            $table->string('location')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('stage_order')->default(0); // for kanban ordering

            $table->integer('salary')->nullable();

            $table->enum('status', ['applied', 'interview', 'offer', 'rejected', 'ghosted']);
            $table->enum('job_type', ['full-time', 'part-time', 'internship', 'contract'])->nullable();

            $table->text('job_url')->nullable();
            $table->text('notes')->nullable();

            $table->date('applied_date')->nullable();

            $table->timestamps();
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
