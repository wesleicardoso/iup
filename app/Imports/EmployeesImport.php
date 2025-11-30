<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $companyId;

    // Recebe o ID da empresa via construtor
    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    public function model(array $row)
    {
        // 1. Busca ou Cria o Cargo (Baseado no Nome e CBO)
        // Se o CBO não vier na planilha, tenta achar só pelo nome
        $role = Role::firstOrCreate(
            ['name' => $row['cargo']], // Coluna 'cargo' do Excel
            ['cbo' => $row['cbo'] ?? null] // Coluna 'cbo' do Excel
        );

        // 2. Trata as datas (Excel retorna números, precisamos converter)
        $birthDate = $this->transformDate($row['data_nascimento']);
        $admissionDate = $this->transformDate($row['data_admissao']);

        // 3. Cria ou Atualiza o Funcionário (Evita duplicados pelo CPF)
        return Employee::updateOrCreate(
            [
                'cpf' => $this->formatCpf($row['cpf']), // Chave única
            ],
            [
                'company_id' => $this->companyId,
                'role_id' => $role->id,
                'name' => $row['nome_completo'],
                'birth_date' => $birthDate,
                'admission_date' => $admissionDate,
                'is_active' => true,
            ]
        );
    }

    // Regras de validação para o Excel
    public function rules(): array
    {
        return [
            'nome_completo' => 'required',
            'cpf' => 'required',
            'cargo' => 'required',
            'data_nascimento' => 'required',
            'data_admissao' => 'required',
        ];
    }

    // Auxiliar: Formata data do Excel para Y-m-d
    private function transformDate($value)
    {
        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            // Tenta converter string PT-BR (31/12/2000)
            return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            return now(); // Retorna hoje se falhar (ou trate o erro)
        }
    }

    // Auxiliar: Limpa CPF (deixa só números)
    private function formatCpf($cpf)
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }
}