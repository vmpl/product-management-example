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
        Schema::table('product_base', function (Blueprint $table) {
            $table->timestampTz('deleted_at')
                ->nullable();
        });
        Schema::table('product_pack', function (Blueprint $table) {
            $table->timestampTz('deleted_at')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_base', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('product_pack', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
};
