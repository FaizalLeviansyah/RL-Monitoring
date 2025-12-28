<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    DB::statement("ALTER TABLE approval_queues MODIFY COLUMN status ENUM('PENDING', 'APPROVED', 'REJECTED', 'SCHEDULED') DEFAULT 'PENDING'");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_queues', function (Blueprint $table) {
            //
        });
    }
};
