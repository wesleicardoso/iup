<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\OnboardingDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SafetyController extends Controller
{
    // 1. Painel com as empresas que precisam de visita ou PGR
    public function index()
    {
        // Filtra empresas que estão nas etapas de responsabilidade da segurança
        $companies = Company::whereIn('onboarding_step', ['importacao_m1', 'visita_tecnica', 'aprovacao_pgr','aprovacao_pcmso'])
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

        // CORREÇÃO: Salva a data E avança o status para a próxima fase (PGR)
        $company->update([
            'technical_visit_at' => $request->visit_date,
            // ✅ AVANÇA O FUNIL: Manda para a etapa de Aprovação de Documento
            'onboarding_step' => 'aprovacao_pgr'
        ]);

        return redirect()->back()->with('success', 'Visita agendada e processo avançado para Aprovação do PGR!');
    }

    public function storeDocument(Request $request, $companyId, $stepKey)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:5120', // Garantindo que é PDF
        ]);

        $company = Company::findOrFail($companyId);
        $user = Auth::user();

        // 1. Upload do Arquivo
        $path = $request->file('file')->store('onboarding_docs', 'public');

        // 2. Cria o Registro do Documento
        OnboardingDocument::create([
            'company_id' => $company->id,
            'uploaded_by_user_id' => $user->id,
            'step_key' => $stepKey,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
            'is_approved_by_client' => false // CRUCIAL: O status do documento é 'Pendente'
        ]);

        return redirect()->back()->with('success', "Documento '$stepKey' enviado com sucesso! Cliente deve aprová-lo.");
    }

    // 2. NOVO: Função para Download (A ser usada pelo Cliente)
    public function downloadDocument($companyId, $stepKey)
    {
        $document = OnboardingDocument::where('company_id', $companyId)
            ->where('step_key', $stepKey)
            ->latest() // Pega a última versão enviada
            ->firstOrFail();

        // Segurança: Apenas o Admin/Safety/Própria Empresa deve poder baixar (Adicione Policy)
        // Por enquanto, vamos apenas permitir o download
        return Storage::download('public/' . $document->file_path, $document->file_name);
    }
}
