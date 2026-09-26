<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->boolean('is_cover')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Move any existing single image into the new table
        if (Schema::hasColumn('properties', 'image')) {
            $existing = DB::table('properties')->whereNotNull('image')->get();

            foreach ($existing as $row) {
                DB::table('property_photos')->insert([
                    'property_id' => $row->id,
                    'file_path'   => $row->image,
                    'is_cover'    => true,
                    'sort_order'  => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('properties', 'image')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('image')->nullable()->after('description');
            });
        }

        Schema::dropIfExists('property_photos');
    }
};