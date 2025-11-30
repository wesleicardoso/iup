<?php

namespace App\Http\Controllers;

use App\Imports\EmployeesImport;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class SalesController extends Controller
{
    // 1. DASHBOARD DO VENDEDOR (Lista empresas em implantação)
    public function index()
    {
        $companies = Company::orderBy('created_at', 'desc')->get();

        return Inertia::render('Sales/Dashboard', [
            'companies' => $companies
        ]);
    }

    // 2. SALVAR NOVA EMPRESA (Inicia o processo)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'cnpj' => 'required|string|unique:companies',
            'email' => 'required|email|unique:users', // Email do gestor da empresa
        ]);

        // Cria a empresa
        $company = Company::create([
            'name' => $validated['name'],
            'trade_name' => $validated['name'], // Simplificado
            'cnpj' => $validated['cnpj'],
            'onboarding_step' => 'contrato_assinado',
            'is_active' => false
        ]);

        // Cria o usuário Gestor daquela empresa automaticamente
        User::create([
            'name' => 'Gestor ' . $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('mudar123'), // Senha provisória
            'role' => 'company',
            'company_id' => $company->id
        ]);

        return redirect()->back()->with('success', 'Empresa iniciada no funil de implantação!');
    }

    // 3. AVANÇAR ETAPA (Move o Card na Timeline)
    public function advanceStep($id)
    {
        $company = Company::findOrFail($id);
        
        $steps = [
            'contrato_assinado' => 'importacao_m1',
            'importacao_m1' => 'visita_tecnica',
            'visita_tecnica' => 'aprovacao_pgr',
            'aprovacao_pgr' => 'aprovacao_pcmso',
            'aprovacao_pcmso' => 'concluido'
        ];

        // Se tiver próximo passo, avança
        if (isset($steps[$company->onboarding_step])) {
            $company->onboarding_step = $steps[$company->onboarding_step];
            
            // Se chegou no final, ativa a empresa
            if ($company->onboarding_step === 'concluido') {
                $company->is_active = true;
            }
            
            $company->save();
        }

        return redirect()->back()->with('success', 'Etapa concluída com sucesso!');
    }

    public function importM1(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:5120', // Max 5MB
        ]);

        $company = Company::findOrFail($id);

        try {
            // Executa a importação
            Excel::import(new EmployeesImport($company->id), $request->file('file'));

            // Atualiza o status da empresa automaticamente
            if ($company->onboarding_step === 'contrato_assinado' || $company->onboarding_step === 'importacao_m1') {
                $company->update(['onboarding_step' => 'visita_tecnica']);
            }

            return redirect()->back()->with('success', 'Planilha M1 importada com sucesso! Funcionários cadastrados.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file' => 'Erro na importação: ' . $e->getMessage()]);
        }
    }

    // Download do Modelo (Template)
    public function downloadTemplate()
    {
        return response()->download(public_path('templates/modelo_m1.xlsx'));
    }
}