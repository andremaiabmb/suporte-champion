<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'locale') && !Schema::hasColumn('users', 'preferred_lang')) {
                $table->renameColumn('locale', 'preferred_lang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'preferred_lang') && !Schema::hasColumn('users', 'locale')) {
                $table->renameColumn('preferred_lang', 'locale');
            }
        });
    }
};
