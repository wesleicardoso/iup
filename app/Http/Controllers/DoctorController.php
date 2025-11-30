<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\MedicalAttachment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    // 1. A FILA DE ESPERA (Dashboard do Médico)
    public function index()
    {
        $user = Auth::user();

        // Debug: Se o usuário não tiver provider_id, vai dar erro ou vazio
        if (!$user->provider_id) {
            dd("ERRO: Este usuário médico (ID: $user->id) não está vinculado a nenhuma clínica (provider_id null).");
        }

        $appointments = Appointment::where('provider_id', $user->provider_id)
            // Filtra para mostrar agendamentos de HOJE
            ->whereDate('scheduled_at', today())
            // OU mostra qualquer um que esteja "AGUARDANDO" (mesmo que fosse de ontem e atrasou)
            ->orWhere(function ($query) use ($user) {
                $query->where('provider_id', $user->provider_id)
                    ->where('status', 'aguardando');
            })
            ->with(['employee', 'company'])
            // Ordenação: Primeiro quem está 'aguardando', depois 'pendente', depois 'concluido'
            ->orderByRaw("FIELD(status, 'aguardando', 'pendente', 'concluido')")
            ->orderBy('scheduled_at')
            ->get();

        return Inertia::render('Doctor/Dashboard', [
            'queue' => $appointments
        ]);
    }

    // 2. A TELA DE ATENDIMENTO (O Prontuário)
    public function attend($appointmentId)
    {
        $appointment = Appointment::with(['employee.role', 'company'])
            ->findOrFail($appointmentId);

        // Verifica segurança (se é da mesma clínica)
        if ($appointment->provider_id !== Auth::user()->provider_id) abort(403);

        // Se já existe prontuário, carrega. Se não, nulo.
        $record = MedicalRecord::where('appointment_id', $appointmentId)
            ->with('attachments')
            ->first();

        return Inertia::render('Doctor/AttendanceRoom', [
            'appointment' => $appointment,
            'existingRecord' => $record,
            'history' => [] // Aqui você carregaria atendimentos antigos desse paciente
        ]);
    }

    // 3. SALVAR ATENDIMENTO
    public function store(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);

        // 1. Cria ou Atualiza o Prontuário
        $record = MedicalRecord::updateOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'doctor_id' => Auth::id(),

                // LÓGICA CORRIGIDA:
                // Se tiver ID (cadastrado), salva o ID.
                'employee_id' => $appointment->employee_id,

                // Sempre salva o nome (seja do cadastro ou o nome digitado no encaixe)
                'patient_name' => $appointment->employee ? $appointment->employee->name : $appointment->employee_name,

                'anamnesis' => $request->anamnesis,
                'physical_exam' => $request->physical_exam,
                'conclusion' => $request->conclusion,
            ]
        );

        // 2. Upload de Imagens/Arquivos (Mantido igual)
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('medical_records', 'public');
                MedicalAttachment::create([
                    'medical_record_id' => $record->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }

        // 3. Finaliza o Agendamento
        $appointment->update(['status' => 'concluido']);

        return redirect()->route('doctor.index')->with('success', 'Atendimento finalizado!');
    }
}
