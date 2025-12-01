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
        Schema::create('user_application_access', function (Blueprint $table) {
            $table->id(); // BigInt

            // FK ke Master (Pakai Integer karena tbl_employee pakai INT)
            $table->integer('user_id')->index();

            $table->string('app_code', 50)->default('RL-MONITORING');
            $table->boolean('is_active')->default(true);
            $table->date('valid_until')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_application_access');
    }
};
