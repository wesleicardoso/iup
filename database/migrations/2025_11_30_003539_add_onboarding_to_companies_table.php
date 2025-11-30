<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Define a etapa atual do cadastro
            $table->enum('onboarding_step', [
                'contrato_assinado',
                'importacao_m1',
                'visita_tecnica',
                'aprovacao_pgr',
                'aprovacao_pcmso',
                'concluido'
            ])->default('contrato_assinado')->after('trade_name');
            
            // Flag para saber se a empresa já pode operar
            $table->boolean('is_active')->default(false)->after('onboarding_step');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['onboarding_step', 'is_active']);
        });
    }
};