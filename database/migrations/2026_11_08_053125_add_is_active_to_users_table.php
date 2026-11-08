<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('identity.users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('global_role');
        });
    }

    public function down(): void
    {
        Schema::table('identity.users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
