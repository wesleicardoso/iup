<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // SEGURANÇA: Garante que o usuário tem uma empresa vinculada
        if (!$user->company_id) {
            abort(403, 'Usuário não vinculado a uma empresa.');
        }

        // QUERY FILTRADA: Apenas funcionários desta empresa
        $employees = Employee::where('company_id', $user->company_id)
            ->with('role') // Carrega o cargo junto
            ->latest()     // Mais recentes primeiro
            ->paginate(15); // Paginação de 15 em 15

        return Inertia::render('Company/Employees/Index', [
            'employees' => $employees
        ]);
    }

    public function create()
    {
        return Inertia::render('Company/Employees/Create', [
            'roles' => Role::orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|unique:employees,cpf',
            'role_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'admission_date' => 'required|date',
        ]);

        // Busca ou cria o Cargo
        $role = Role::firstOrCreate(
            ['name' => $validated['role_name']],
            ['cbo' => null]
        );

        // Cria o funcionário vinculado à empresa do usuário
        Employee::create([
            'company_id' => $user->company_id, // <--- O PULO DO GATO ESTÁ AQUI
            'role_id' => $role->id,
            'name' => $validated['name'],
            'cpf' => $validated['cpf'],
            'birth_date' => $validated['birth_date'],
            'admission_date' => $validated['admission_date'],
            'is_active' => true
        ]);

        return redirect()->route('employees.index')->with('success', 'Funcionário cadastrado com sucesso!');
    }
}
