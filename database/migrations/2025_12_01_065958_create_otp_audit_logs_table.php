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
        Schema::create('otp_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rl_id')->constrained('requisition_letters')->onDelete('cascade');
            $table->integer('user_id'); // Pelaku

            $table->string('action', 100); // e.g. 'REQUEST_OTP'
            $table->string('otp_sent_to', 20)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_audit_logs');
    }
};
