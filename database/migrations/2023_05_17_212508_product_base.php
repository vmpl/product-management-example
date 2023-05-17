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
        Schema::create('product_base', function (Blueprint $table) {
            $table->unsignedBigInteger('id')
                ->primary();
            $table->string('name', 255)
                ->nullable(false);
            $table->integer('number')
                ->nullable()
                ->index();
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_base');
    }
};
