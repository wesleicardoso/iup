<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\OnboardingDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientActionController extends Controller
{
    public function approveDocument(Request $request, Company $company, $stepKey)
    {
        // 1. Check de Segurança
        if (Auth::user()->company_id !== $company->id) abort(403);

        // 2. Encontra o Documento e Atualiza o Status
        $document = OnboardingDocument::where('company_id', $company->id)
            ->where('step_key', $stepKey)
            ->latest()
            ->firstOrFail();

        $document->is_approved_by_client = true;
        $document->save();

        // 3. Lógica para AVANÇAR O STEP DA EMPRESA
        $nextStep = null;
        if ($stepKey === 'aprovacao_pgr' && $company->onboarding_step === 'aprovacao_pgr') {
            $nextStep = 'aprovacao_pcmso';
        } elseif ($stepKey === 'aprovacao_pcmso' && $company->onboarding_step === 'aprovacao_pcmso') {
            $nextStep = 'concluido';
            $company->is_active = true;
        }
        
        if ($nextStep) {
            $company->onboarding_step = $nextStep;
            $company->save();
        }

        return redirect()->back()->with('success', "Documento '$stepKey' aprovado com sucesso! Processo avançou.");
    }
}