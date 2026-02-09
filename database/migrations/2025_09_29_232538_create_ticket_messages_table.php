<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // null => sistema
            $table->text('message');
            $table->boolean('is_internal')->default(false); // visível só ao staff
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ticket_messages');
    }
};
