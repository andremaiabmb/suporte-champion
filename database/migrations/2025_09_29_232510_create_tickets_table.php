<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ex.: ST-2025-0001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();       // autor
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete(); // responsável atual
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low','normal','high','urgent'])->default('normal');
            $table->enum('status', ['open','in_progress','waiting','resolved','closed','reopened'])->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};
