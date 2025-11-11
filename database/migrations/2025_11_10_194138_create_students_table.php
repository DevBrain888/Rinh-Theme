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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ФИО
            $table->string('group'); // Группа
            $table->string('email')->nullable(); // Email (опционально)
            $table->string('phone')->nullable(); // Телефон (опционально)
            $table->foreignId('theme_id')->nullable()->constrained('themes')->nullOnDelete(); // Назначенная тема
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
