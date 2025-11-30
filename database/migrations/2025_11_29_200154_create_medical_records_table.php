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
    Schema::create('medical_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('appointment_id')->constrained()->unique(); // 1 atendimento = 1 registro
        $table->foreignId('doctor_id')->constrained('users'); // Médico que atendeu
        $table->foreignId('employee_id')->constrained(); // Paciente
        
        // Dados Clínicos
        $table->text('anamnesis')->nullable(); // Queixa principal / História
        $table->text('physical_exam')->nullable(); // Exame físico
        $table->text('diagnostic_hypothesis')->nullable(); // CID ou hipótese
        $table->text('work_restrictions')->nullable(); // Apto com restrição?
        
        // Conclusão (Apto, Inapto)
        $table->enum('conclusion', ['apto', 'inapto', 'apto_restricao'])->default('apto');
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
