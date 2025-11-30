<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Empresas (Clientes)
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj')->unique();
            $table->string('name');
            $table->string('trade_name')->nullable();
            $table->timestamps();
        });

        // 2. Credenciadas (Clínicas)
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj')->unique();
            $table->string('name');
            $table->string('city');
            $table->timestamps();
        });

        // 3. Alterar tabela Users existente para adicionar FKs e Role
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('company')->after('email'); // admin, company, provider
            $table->foreignId('company_id')->default('1')->constrained('companies')->after('role');
            $table->foreignId('provider_id')->default('1')->constrained('providers')->after('company_id');
        });

        // 4. Agendamentos
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('provider_id')->constrained();
            $table->string('exam_type'); // Ex: Audiometria
            $table->dateTime('scheduled_at');
            $table->string('status')->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['provider_id']);
            $table->dropColumn(['role', 'company_id', 'provider_id']);
        });
        Schema::dropIfExists('providers');
        Schema::dropIfExists('companies');
    }
};