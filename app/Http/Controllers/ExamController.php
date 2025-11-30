<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExamController extends Controller
{
    public function index()
    {
        // Lista simples de exames
        return Inertia::render('Company/Exams/Index', [
            'exams' => Exam::orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tuss_code' => 'nullable|string',
            'validity_months' => 'required|integer|min:1'
        ]);

        Exam::create($validated);

        return redirect()->back()->with('success', 'Exame adicionado ao catálogo!');
    }
}