<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    // Listar Meus Tickets
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->with(['category', 'messages']) // Eager loading
            ->latest()
            ->paginate(10);

        return Inertia::render('Company/Support/Index', [
            'tickets' => $tickets
        ]);
    }

    // Tela de Criar
    public function create()
    {
        return Inertia::render('Company/Support/Create', [
            'categories' => TicketCategory::all()
        ]);
    }

    // Salvar Novo Ticket
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:ticket_categories,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:baixa,media,alta'
        ]);

        // 1. Cria o Ticket
        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'ticket_category_id' => $validated['category_id'],
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'aberto'
        ]);

        // 2. Cria a primeira mensagem
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message']
        ]);

        return redirect()->route('support.show', $ticket->id);
    }

    // Ver conversa (Chat)
    public function show($id)
    {
        $ticket = Ticket::where('id', $id)
            ->where('user_id', Auth::id()) // Segurança: só vê o seu
            ->with(['category', 'messages.user', 'messages' => function($q) {
                $q->latest(); // Ordem do chat
            }])
            ->firstOrFail();

        return Inertia::render('Company/Support/Show', [
            'ticket' => $ticket
        ]);
    }

    // Responder no Chat
    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);

        TicketMessage::create([
            'ticket_id' => $id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        // Atualiza status se necessário
        $ticket = Ticket::find($id);
        if ($ticket->status === 'respondido') {
            $ticket->update(['status' => 'aberto']); // Reabre se o cliente respondeu
        }

        return redirect()->back();
    }
}