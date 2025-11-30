<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // ----------------------------------------------------
        // FAILSAFE INICIAL (Se o usuário não tem role, ou não devia estar logado)
        if (!$user) {
            return redirect()->route('login');
        }

        // 1. ADMIN (Visão Macro)
        if ($user->role === 'admin') {
            
            $stats = [
                'total_companies' => \App\Models\Company::count(),
                'active_companies' => \App\Models\Company::where('is_active', true)->count(),
                'onboarding_companies' => \App\Models\Company::where('is_active', false)->count(),
                'total_exams' => \App\Models\Appointment::count(),
                'pending_tickets' => \App\Models\Ticket::where('status', 'aberto')->count(),
            ];

            $upcomingAppointments = Appointment::with(['company', 'provider'])
                ->where('scheduled_at', '>=', now())
                ->orderBy('scheduled_at', 'asc')
                ->take(10)
                ->get();

            return Inertia::render('Dashboards/Admin', [
                'user' => $user,
                'stats' => $stats,
                'upcomingAppointments' => $upcomingAppointments
            ]);
        }
        
        // 2. EMPRESA (Cliente)
        if ($user->role === 'company') {
            // FIX: Carrega o objeto company fresco
            $company = $user->company->fresh(); 

            return Inertia::render('Dashboards/Company', [
                'company' => $company, // Objeto fresh
                // FIX: Carregamento explícito da relação
                'appointments' => $company->appointments()->get(), 
                
                'upcomingAppointments' => $company->appointments()
                    ->where('scheduled_at', '>=', now())
                    ->orderBy('scheduled_at', 'asc')
                    ->take(5)
                    ->get()
            ]);
        }

        // 3. PROVIDER/CLÍNICA (Médico/Recepcionista)
        if ($user->role === 'provider') {
            return Inertia::render('Dashboards/Provider', [
                'provider' => $user->provider,
                'upcomingAppointments' => Appointment::where('provider_id', $user->provider_id)
                    ->where('scheduled_at', '>=', now())
                    ->with('company')
                    ->orderBy('scheduled_at', 'asc')
                    ->take(5)
                    ->get()
            ]);
        }

        // 4. VENDAS (Comercial) - Redireciona para o Pipeline
        if ($user->role === 'sales') {
            return redirect()->route('sales.index');
        }
        
        // 5. SEGURANÇA (Técnico de Segurança) - Redireciona para o Painel de PGR/Visitas
        if ($user->role === 'safety') {
            return redirect()->route('safety.index');
        }
        
        // 6. FAILSAFE FINAL (Caso a role seja desconhecida/estranha)
        Auth::logout();
        return redirect()->route('login');
    }
}