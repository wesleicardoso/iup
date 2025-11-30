<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            // 1. Permite que o ID seja nulo (para pacientes avulsos)
            $table->foreignId('employee_id')->nullable()->change();
            
            // 2. Adiciona coluna para gravar o nome do paciente (Histórico seguro)
            $table->string('patient_name')->nullable()->after('employee_id');
        });
    }

    public function down(): void
    {
        // Reverter (opcional)
        Schema::table('medical_records', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable(false)->change();
            $table->dropColumn('patient_name');
        });
    }
};