<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('property_id')->nullable()->after('tenancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipient_id')->nullable()->after('sender_id')->constrained('users')->cascadeOnDelete();
            $table->index(['property_id', 'sender_id', 'recipient_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('tenancy_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('messages')->whereNull('tenancy_id')->delete();

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['property_id', 'sender_id', 'recipient_id']);
            $table->dropConstrainedForeignId('property_id');
            $table->dropConstrainedForeignId('recipient_id');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('tenancy_id')->nullable(false)->change();
        });
    }
};
