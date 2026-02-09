<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('event_attendances')) {
            Schema::create('event_attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('registration_id');
                $table->date('date'); // dia da presença
                $table->timestamp('checked_in_at')->nullable();
                $table->timestamp('checked_out_at')->nullable();
                $table->timestamps();

                $table->unique(['event_id','registration_id','date'], 'uniq_event_reg_date');

                // Índices simples (FKs opcionais conforme seus modelos)
                $table->index('event_id');
                $table->index('registration_id');
                $table->index('date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};
