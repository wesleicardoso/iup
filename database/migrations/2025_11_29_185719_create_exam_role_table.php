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
    Schema::create('exams', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nome: Audiometria Tonal
        $table->string('tuss_code')->nullable(); // Código TUSS/AMB (Importante p/ faturamento)
        $table->integer('validity_months')->default(6); // Validade padrão (6 meses, 12 meses)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_role');
    }
};
