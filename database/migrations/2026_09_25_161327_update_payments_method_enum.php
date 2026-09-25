<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('card','bank_transfer','cash','paypal','aba_khqr') DEFAULT 'card'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('card','bank_transfer','cash','paypal') DEFAULT 'card'");
    }
};