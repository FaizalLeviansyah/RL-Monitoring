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
        Schema::create('requisition_letters', function (Blueprint $table) {
            $table->id();
            
            // FK ke Master (Penghubung ke PT dan Karyawan)
            $table->integer('company_id')->index();
            $table->integer('requester_id')->index();
            
            $table->string('rl_no', 50)->unique(); // Nomor Surat
            $table->date('request_date');
            
            // Status Flow (Draft -> Approved)
            $table->enum('status_flow', ['DRAFT', 'ON_PROGRESS', 'APPROVED', 'REJECTED'])->default('DRAFT');
            
            // Atribut Opsional (Untuk mengakomodir PT ACS/CTP)
            $table->string('subject', 255)->nullable();        // Hal
            $table->string('to_department', 100)->nullable();  // Kepada
            $table->string('attachment_count', 50)->nullable(); // Lampiran
            $table->text('remark')->nullable();                // Penggunaan/Detail
            
            $table->timestamps(); // Created_at & Updated_at
            $table->softDeletes(); // Agar data tidak hilang permanen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_letters');
    }
};
