<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $exams = [
            ['name' => 'Audiometria Tonal', 'tuss_code' => '40103121', 'validity_months' => 6],
            ['name' => 'Espirometria Ocupacional', 'tuss_code' => '40105051', 'validity_months' => 12],
            ['name' => 'Acuidade Visual', 'tuss_code' => '41301036', 'validity_months' => 12],
            ['name' => 'Raio-X de Tórax OIT', 'tuss_code' => '40805018', 'validity_months' => 12],
            ['name' => 'Hemograma Completo', 'tuss_code' => '40304361', 'validity_months' => 6],
            ['name' => 'Eletrocardiograma (ECG)', 'tuss_code' => '40101010', 'validity_months' => 12],
            ['name' => 'Eletroencefalograma (EEG)', 'tuss_code' => '40101029', 'validity_months' => 12],
            ['name' => 'Avaliação Psicossocial', 'tuss_code' => '00000001', 'validity_months' => 12],
            ['name' => 'Glicemia de Jejum', 'tuss_code' => '40302040', 'validity_months' => 12],
            ['name' => 'Exame Clínico (ASO)', 'tuss_code' => '10101012', 'validity_months' => 12],
        ];

        foreach ($exams as $exam) {
            Exam::firstOrCreate(['name' => $exam['name']], $exam);
        }
    }
}