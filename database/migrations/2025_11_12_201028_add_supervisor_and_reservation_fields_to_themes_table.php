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
        Schema::table('themes', function (Blueprint $table) {
            // Связь с руководителем (научный руководитель)
            $table->foreignId('supervisor_id')->nullable()->after('assigned_to')->constrained('supervisors')->nullOnDelete();
            
            // Поля для блокировки тем (блокировка на 30 минут)
            $table->string('reserved_by_group')->nullable()->after('supervisor_id'); // Группа, которая заблокировала тему
            $table->timestamp('reserved_at')->nullable()->after('reserved_by_group'); // Время блокировки
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['supervisor_id', 'reserved_by_group', 'reserved_at']);
        });
    }
};
