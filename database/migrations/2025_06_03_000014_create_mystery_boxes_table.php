<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mystery_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Mystery Box');
            $table->decimal('budget', 10, 2);
            $table->enum('mood', ['Romantic', 'Mysterious', 'Funny', 'Brave', 'Calm', 'Happy']);
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mystery_boxes');
    }
};
