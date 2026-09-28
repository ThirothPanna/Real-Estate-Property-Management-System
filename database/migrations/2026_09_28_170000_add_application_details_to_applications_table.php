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
        Schema::table('applications', function (Blueprint $table) {
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('unit_number', 50)->nullable();
            $table->string('current_address')->nullable();
            $table->string('current_city', 100)->nullable();
            $table->string('current_state', 100)->nullable();
            $table->string('current_zip', 20)->nullable();
            $table->string('employer')->nullable();
            $table->string('job_title')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->string('previous_landlord')->nullable();
            $table->string('previous_landlord_phone', 20)->nullable();
            $table->date('move_in_date')->nullable();
            $table->unsignedInteger('occupants')->nullable();
            $table->string('pets')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'email',
                'phone',
                'date_of_birth',
                'unit_number',
                'current_address',
                'current_city',
                'current_state',
                'current_zip',
                'employer',
                'job_title',
                'monthly_income',
                'previous_landlord',
                'previous_landlord_phone',
                'move_in_date',
                'occupants',
                'pets',
                'notes',
                'status',
            ]);
        });
    }
};
