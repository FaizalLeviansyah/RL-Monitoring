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
        Schema::create('approval_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rl_id')->constrained('requisition_letters')->onDelete('cascade');

            // FK ke Master (Siapa yang harus TTD)
            $table->integer('approver_id');

            $table->integer('level_order'); // Urutan: 1, 2, 3
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');

            // Metode Approval (Online/Manual)
            $table->string('method')->nullable(); // Ubah jadi String bebas
            $table->string('otp_code', 255)->nullable();
            $table->string('attachment', 255)->nullable(); // Bukti TTD Manual
            $table->dateTime('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_queues');
    }
};
