<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela de Funções (Cargos)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: Soldador, Motorista
            $table->string('cbo')->nullable(); // Código Brasileiro de Ocupações (Importante pro eSocial)
            $table->timestamps();
        });

        // 2. Tabela de Funcionários
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles'); // Vincula ao cargo
            $table->string('name');
            $table->string('cpf')->unique();
            $table->date('birth_date');
            $table->date('admission_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('roles');
    }
};