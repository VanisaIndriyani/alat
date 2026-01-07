<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->string('code')->nullable()->unique();
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedInteger('fine_amount')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('fine_amount');
        });
    }
};