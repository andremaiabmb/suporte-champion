<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sectors', function (Blueprint $table) {
            if (!Schema::hasColumn('sectors', 'deleted_at')) {
                $table->softDeletes(); // adiciona coluna deleted_at
            }
        });
    }

    public function down(): void
    {
        Schema::table('sectors', function (Blueprint $table) {
            if (Schema::hasColumn('sectors', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
