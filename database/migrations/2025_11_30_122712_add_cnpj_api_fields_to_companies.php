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
        Schema::table('companies', function (Blueprint $table) {
            // DADOS ESTRUTURAIS E STATUS
            $table->string('corporate_name')->nullable()->after('name'); // Razão Social Completa
            $table->string('status_code')->nullable()->after('cnpj'); // Situação Cadastral
            $table->date('foundation_date')->nullable()->after('status_code'); // Data de Abertura
            $table->string('cnae_primary')->nullable()->after('foundation_date'); // CNAE Principal
            $table->string('legal_nature')->nullable()->after('cnae_primary'); // Natureza Jurídica

            // ENDEREÇO
            $table->string('zip_code')->nullable();
            $table->string('address_line_1')->nullable(); // Logradouro
            $table->string('address_number')->nullable();
            $table->string('address_complement')->nullable();
            $table->string('neighborhood')->nullable(); // Bairro
            $table->string('city')->nullable();
            $table->string('state')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
};
