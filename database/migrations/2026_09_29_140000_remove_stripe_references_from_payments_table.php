<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('payments', 'stripe_checkout_session_id')
            && ! Schema::hasColumn('payments', 'stripe_payment_intent_id')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'stripe_checkout_session_id')) {
                $table->dropUnique(['stripe_checkout_session_id']);
            }
            if (Schema::hasColumn('payments', 'stripe_payment_intent_id')) {
                $table->dropUnique(['stripe_payment_intent_id']);
            }

            $table->dropColumn(['stripe_checkout_session_id', 'stripe_payment_intent_id']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent_id')->nullable()->unique();
        });
    }
};
