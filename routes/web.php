<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SafetyController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\TicketController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rota Inteligente (Redireciona para o painel certo)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas Exclusivas EMPRESA
    Route::middleware('role:company')->prefix('empresa')->group(function () {
        Route::get('/novo-exame', function () {
            return "Tela de Agendar";
        });
    });

    // Rotas Exclusivas ADMIN SESI
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/relatorios', function () {
            return "Tela de Relatórios";
        });

        Route::middleware('role:company')->prefix('empresa')->group(function () {

            // Rota para ver o formulário
            Route::get('/agendar', [AppointmentController::class, 'create'])->name('appointments.create');

            // Rota para salvar os dados
            Route::post('/agendar', [AppointmentController::class, 'store'])->name('appointments.store');

            Route::get('/controle-epis', function () {
                return Inertia::render('Company/EpiControl');
            })->name('epi.index');
        });
    });

    // Listagem
    Route::get('/agendamentos', [AppointmentController::class, 'index'])->name('appointments.index');

    // Criação

    Route::post('/agendar', [AppointmentController::class, 'store'])->name('appointments.store');

    // ✅ O DASHBOARD DEVE FICAR AQUI (Livre para todos os logados)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 🔒 Rotas SÓ DA EMPRESA (Protegidas)
    Route::middleware('role:company')->prefix('empresa')->group(function () {
        Route::get('/agendar', [AppointmentController::class, 'create'])->name('appointments.create');
        // ... outras rotas
    });

    // 🔒 Rotas SÓ DO ADMIN (Protegidas)
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // ...
    });

    // 🔒 Rotas SÓ DA CREDENCIADA (Protegidas)
    Route::middleware('role:provider')->prefix('credenciada')->group(function () {
        Route::post('/agenda/{id}/agendar', [AppointmentController::class, 'setSchedule'])->name('provider.appointments.schedule');
        Route::get('/funcionarios', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/funcionarios/novo', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/funcionarios', [EmployeeController::class, 'store'])->name('employees.store');
    });




    // EXAMES
    Route::get('/exames', [ExamController::class, 'index'])->name('exams.index');
    Route::post('/exames', [ExamController::class, 'store'])->name('exams.store');

    Route::get('/suporte', [TicketController::class, 'index'])->name('support.index');
    Route::get('/suporte/novo', [TicketController::class, 'create'])->name('support.create');
    Route::post('/suporte', [TicketController::class, 'store'])->name('support.store');
    Route::get('/suporte/{id}', [TicketController::class, 'show'])->name('support.show');
    Route::post('/suporte/{id}', [TicketController::class, 'reply'])->name('support.reply');
});

Route::middleware(['auth', 'role:company'])->prefix('empresa')->group(function () {
    // ... outras rotas ...
    // Rota para baixar o PDF
    Route::get('/agendamentos/{id}/download', [AppointmentController::class, 'downloadResult'])->name('appointments.download');
});

// 2. Rotas da CREDENCIADA (Clínica)
Route::middleware(['auth', 'role:provider'])->prefix('credenciada')->group(function () {
    // Painel da Clínica ver a agenda
    Route::get('/agenda', [AppointmentController::class, 'providerIndex'])->name('provider.appointments.index');
    // Enviar resultado
    Route::post('/agenda/{id}/upload', [AppointmentController::class, 'uploadResult'])->name('provider.appointments.upload');

    // Check-in
    Route::post('/agenda/{id}/checkin', [AppointmentController::class, 'checkIn'])->name('provider.appointments.checkin');

    // Encaixe (Novo Avulso)
    Route::post('/agenda/encaixe', [AppointmentController::class, 'storeWalkIn'])->name('provider.appointments.walkin');
});


Route::middleware(['auth', 'role:provider'])->prefix('medico')->group(function () {
    Route::get('/painel', [DoctorController::class, 'index'])->name('doctor.index');
    Route::get('/atender/{id}', [DoctorController::class, 'attend'])->name('doctor.attend');
    Route::post('/atender/{id}', [DoctorController::class, 'store'])->name('doctor.store');
});

Route::middleware(['auth'])->prefix('vendas')->group(function () {
    Route::get('/dashboard', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/nova-empresa', [SalesController::class, 'store'])->name('sales.store');
    Route::post('/empresa/{id}/avancar', [SalesController::class, 'advanceStep'])->name('sales.advance');
    Route::post('/empresa/{id}/importar-m1', [SalesController::class, 'importM1'])->name('sales.import_m1');
    Route::get('/template-m1', [SalesController::class, 'downloadTemplate'])->name('sales.template_m1');
});


Route::middleware(['auth'])->prefix('seguranca')->group(function () {
    Route::get('/dashboard', [SafetyController::class, 'index'])->name('safety.index');
    Route::post('/empresa/{id}/agendar', [SafetyController::class, 'storeVisitDate'])->name('safety.schedule');
    Route::post('/empresa/{id}/aprovar-pgr', [SafetyController::class, 'approvePGR'])->name('safety.approve');
});

require __DIR__ . '/auth.php';
