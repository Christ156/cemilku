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
        Schema::create('customizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['tower', 'bouquet']);
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->string('base_image_path')->nullable();
            $table->enum('layer', [2, 3, 4]);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customizes');
    }
};
