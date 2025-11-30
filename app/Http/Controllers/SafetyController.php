<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SafetyController extends Controller
{
    // 1. Painel com as empresas que precisam de visita ou PGR
    public function index()
    {
        // Filtra empresas que estão nas etapas de responsabilidade da segurança
        $companies = Company::whereIn('onboarding_step', ['importacao_m1', 'visita_tecnica', 'aprovacao_pgr'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Safety/Dashboard', [
            'companies' => $companies
        ]);
    }

    // 2. Salvar a Data da Visita
    public function storeVisitDate(Request $request, $id)
    {
        $request->validate([
            'visit_date' => 'required|date|after_or_equal:today',
        ]);

        $company = Company::findOrFail($id);

        $company->update([
            'technical_visit_at' => $request->visit_date,
            // Se ainda estava na etapa anterior, já move para "Em visita"
            'onboarding_step' => 'visita_tecnica'
        ]);

        return redirect()->back()->with('success', 'Visita agendada com sucesso!');
    }

    // 3. Concluir a etapa (Aprovar PGR)
    public function approvePGR($id)
    {
        $company = Company::findOrFail($id);

        // CORREÇÃO: Mudar para a PROXIMA etapa (PCMSO)
        // Antes estava: 'aprovacao_pgr' (ficava parado no mesmo lugar)
        $company->update([
            'onboarding_step' => 'aprovacao_pcmso'
        ]);

        return redirect()->back()->with('success', 'PGR Aprovado! Processo enviado para o Médico.');
    }
}
