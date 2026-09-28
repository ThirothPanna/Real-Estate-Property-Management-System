<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', [
                'electricity', 'water', 'gas', 'internet', 'trash', 'other',
            ]);
            $table->string('account_number')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
{
    Schema::table('utility_providers', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn([
            'user_id', 'name', 'type', 'account_number',
            'contact_phone', 'contact_email', 'notes', 'is_active',
        ]);
    });
}
};
