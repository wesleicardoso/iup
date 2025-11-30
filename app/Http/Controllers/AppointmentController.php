<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Provider;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AppointmentController extends Controller
{
    // 1. LISTAGEM (O histórico de exames)
    public function index()
    {
        $user = Auth::user();

        // Busca agendamentos apenas da empresa logada
        $appointments = Appointment::query()
            ->where('company_id', $user->company_id)
            ->with('provider') // Carrega dados da clínica
            ->latest()
            ->paginate(10); // Paginação

        return Inertia::render('Company/Appointments/Index', [
            'appointments' => $appointments
        ]);
    }

    public function create()
    {
        return Inertia::render('Company/Appointments/Create', [
            'providers' => Provider::all(),
            // Retorna APENAS funcionários da empresa logada
            'employees' => Employee::where('company_id', Auth::user()->company_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
        ]);
    }

    // 2. SALVAR SOLICITAÇÃO (Empresa)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id', // ID do funcionário
            'provider_id' => 'required|exists:providers,id',
            'exam_type' => 'required|string',
        ]);

        // Busca o nome do funcionário para manter histórico rápido
        $employee = Employee::find($validated['employee_id']);

        Appointment::create([
            'company_id' => Auth::user()->company_id,
            'provider_id' => $validated['provider_id'],
            'employee_id' => $validated['employee_id'],
            'employee_name' => $employee->name, // Mantemos o backup do nome
            'exam_type' => $validated['exam_type'],
            'scheduled_at' => null, // Data vazia
            'status' => 'solicitado' // Novo Status Inicial
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Exame solicitado! Aguarde a confirmação da data pela clínica.');
    }

    // 3. DEFINIR DATA (Ação da Clínica) - NOVO MÉTODO
    public function setSchedule(Request $request, $id)
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $appointment = Appointment::findOrFail($id);

        if ($appointment->provider_id !== Auth::user()->provider_id) {
            abort(403);
        }

        $appointment->update([
            'scheduled_at' => $request->scheduled_at,
            'status' => 'pendente' // Agora sim está agendado
        ]);

        return redirect()->back()->with('success', 'Agendamento confirmado com sucesso!');
    }
    public function providerIndex()
    {
        $user = Auth::user();

        $appointments = Appointment::where('provider_id', $user->provider_id)
            ->with(['company', 'employee'])

            // CORREÇÃO: Ordena explicitamente pelo status mais urgente
            // 1. Solicitado (Novo pedido)
            // 2. Aguardando (Chegou na recepção)
            // 3. Pendente (Agendado para o futuro)
            ->orderByRaw("FIELD(status, 'solicitado', 'aguardando', 'pendente', 'concluido')")

            // Depois ordena pela data agendada
            ->orderBy('scheduled_at', 'asc')
            ->paginate(20);

        return Inertia::render('Provider/Appointments/Index', [
            'appointments' => $appointments
        ]);
    }

    // 2. AÇÃO DA CLÍNICA: Salvar o Resultado (Upload)
    public function uploadResult(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,png|max:2048', // PDF ou Imagem até 2MB
        ]);

        $appointment = Appointment::findOrFail($id);

        // Verifica se o exame pertence a esta clínica (Segurança)
        if ($appointment->provider_id !== Auth::user()->provider_id) {
            abort(403, 'Você não tem permissão para alterar este exame.');
        }

        // Salva o arquivo na pasta 'exams'
        $path = $request->file('file')->store('exams', 'public');

        // Atualiza o agendamento
        $appointment->update([
            'status' => 'concluido',
            'result_url' => $path
        ]);

        return redirect()->back()->with('success', 'Resultado enviado com sucesso!');
    }

    // 3. AÇÃO DA EMPRESA: Baixar o Resultado
    public function downloadResult($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Verifica se a empresa é a dona do exame
        if ($appointment->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        if (!$appointment->result_url) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::download('public/' . $appointment->result_url);
    }

    // 1. CHECK-IN (Paciente chegou na recepção)
    public function checkIn($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Segurança: Só a clínica dona do agendamento pode dar check-in
        if ($appointment->provider_id !== Auth::user()->provider_id) {
            abort(403);
        }

        $appointment->update(['status' => 'aguardando']); // Novo status

        return redirect()->back()->with('success', 'Paciente confirmado e enviado para fila!');
    }

    // 2. ENCAIXE (Paciente chegou sem hora marcada)
    public function storeWalkIn(Request $request)
    {
        $validated = $request->validate([
            'employee_name' => 'required|string',
            'company_name' => 'required|string', // Apenas texto informativo
            'exam_type' => 'required|string',
        ]);

        // Cria o agendamento para AGORA
        Appointment::create([
            'provider_id' => Auth::user()->provider_id,
            // Como é avulso, podemos deixar company_id nulo ou vincular a uma empresa padrão "Avulso"
            // Para simplificar, vou assumir que o campo é nullable na migration ou usar um ID fixo se obrigatório
            'company_id' => 1, // <--- ATENÇÃO: Em produção, selecione a empresa real
            'employee_name' => $validated['employee_name'],
            'exam_type' => $validated['exam_type'],
            'scheduled_at' => now(), // Hora atual
            'status' => 'aguardando' // Já entra na fila
        ]);

        return redirect()->back()->with('success', 'Encaixe realizado com sucesso!');
    }
}
