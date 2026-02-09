<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('message_id')->nullable()->constrained('ticket_messages')->nullOnDelete(); // anexo de uma mensagem
            $table->string('original_name');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->unsignedBigInteger('size')->default(0);
            $table->string('mime')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ticket_attachments');
    }
};
