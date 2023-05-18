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
        Schema::create('product_pack', function (Blueprint $table) {
            $table->unsignedBigInteger('id')
                ->autoIncrement();
            $table->string('name', 255)
                ->nullable(false);
            $table->dateTimeTz('created_at')
                ->nullable(false)
                ->default(new \Illuminate\Database\Query\Expression('current_timestamp'));
            $table->dateTimeTz('updated_at')
                ->nullable(false)
                ->useCurrentOnUpdate();

            $table->foreignId('team_id')
                ->references('id')
                ->on('teams')
                ->cascadeOnDelete();
        });

        Schema::create('product_pack_children', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->references('id')
                ->on('product_pack')
                ->cascadeOnDelete();
            $table->foreignId('child_id')
                ->references('id')
                ->on('product_base')
                ->cascadeOnDelete();

            $table->unique(['parent_id', 'child_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_pack_children');
        Schema::dropIfExists('product_pack');
    }
};
