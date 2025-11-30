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
        Schema::table('onboarding_documents', function (Blueprint $table) {
            // NOVO: Flag para aprovação do cliente
            $table->boolean('is_approved_by_client')->default(false)->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboarding_documents', function (Blueprint $table) {
            //
        });
    }
};
