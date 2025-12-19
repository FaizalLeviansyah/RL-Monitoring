<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique()->nullable();
            $table->string('item_name');
            $table->string('category')->nullable();
            $table->string('unit')->default('Pcs');
            $table->text('specification')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_items');
    }
};
