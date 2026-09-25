<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('card_last4')->nullable()->after('method');
            $table->string('card_expiry')->nullable()->after('card_last4');
            $table->string('card_name')->nullable()->after('card_expiry');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['card_last4', 'card_expiry', 'card_name']);
        });
    }
};