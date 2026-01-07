<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->nullable();
            $table->string('department_name')->nullable();
            $table->text('address')->nullable();
            $table->string('head_name')->nullable();
            $table->string('head_nip')->nullable();
            $table->string('theme_primary')->nullable();
            $table->string('footer_text')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};