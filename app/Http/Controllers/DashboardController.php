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

        // 1. ADMIN (Vê tudo)
        if ($user->role === 'admin') {
            
            // Coleta estatísticas rápidas
            $stats = [
                'total_companies' => \App\Models\Company::count(),
                'active_companies' => \App\Models\Company::where('is_active', true)->count(),
                'onboarding_companies' => \App\Models\Company::where('is_active', false)->count(),
                'total_exams' => \App\Models\Appointment::count(),
                'pending_tickets' => \App\Models\Ticket::where('status', 'aberto')->count(),
            ];

            // Próximos 10 agendamentos de TODO o sistema
            $upcomingAppointments = \App\Models\Appointment::with(['company', 'provider'])
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
        
        // 2. EMPRESA (Vê só os seus)
        if ($user->role === 'company') {
            return Inertia::render('Dashboards/Company', [
                'company' => $user->company->fresh(),
                'appointments' => $user->company->appointments, // Mantém para contagem total
                // Adiciona lista filtrada de futuros
                'upcomingAppointments' => $user->company->appointments()
                    ->where('scheduled_at', '>=', now())
                    ->orderBy('scheduled_at', 'asc')
                    ->take(5)
                    ->get()
            ]);
        }

        // 3. PROVIDER/CLÍNICA (Vê só os agendados para ela)
        if ($user->role === 'provider') {
            return Inertia::render('Dashboards/Provider', [
                'provider' => $user->provider,
                // Próximos 5 agendamentos desta clínica
                'upcomingAppointments' => Appointment::where('provider_id', $user->provider_id)
                    ->where('scheduled_at', '>=', now())
                    ->with('company')
                    ->orderBy('scheduled_at', 'asc')
                    ->take(5)
                    ->get()
            ]);
        }
    }
}
