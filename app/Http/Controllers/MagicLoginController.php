<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MagicLoginController extends Controller
{
    // 1. Gerar o QR Code (Para o Admin ver e imprimir/enviar)
    // Exemplo: /gerar-qr-login/1 (Onde 1 é o ID do usuário)
    public function generate($userId)
    {
        $user = User::findOrFail($userId);

        // Cria uma URL assinada que expira em 1 ano (ou o tempo que preferir)
        // Se quiser que nunca expire, use URL::signedRoute (sem temporary)
        $url = URL::temporarySignedRoute(
            'magic.login.attempt',
            now()->addYear(), // Validade
            ['user' => $user->id]
        );

        // Gera o QR Code apontando para essa URL
        $qrCode = QrCode::size(300)->generate($url);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

    // 2. A Rota Mágica (Onde o QR Code leva)
    public function login(Request $request, User $user)
    {
        // A validação da assinatura é feita pelo Middleware 'signed' na rota.
        // Se chegou aqui, a URL é válida e segura.

        // Loga o usuário automaticamente
        Auth::login($user);

        // Redireciona para o Dashboard
        return redirect()->route('dashboard')
            ->with('success', 'Login automático realizado com sucesso!');
    }

    public function generateForCompany($companyId)
    {
        // 1. Acha a empresa
        $company = Company::with('users')->findOrFail($companyId);

        // 2. Pega o primeiro usuário vinculado a ela (O Gestor)
        $user = $company->users->first();

        if (!$user) {
            return response()->json(['error' => 'Esta empresa não tem usuário vinculado.'], 404);
        }

        // 3. Gera a URL Mágica (Válida por 1 ano)
        $url = URL::temporarySignedRoute(
            'magic.login.attempt',
            now()->addYear(),
            ['user' => '7']
        );

        // 4. Gera o SVG
        $qrCode = QrCode::size(300)->margin(2)->generate($url);

        return response()->json([
            'company_name' => $company->name,
            'qr_code' => (string) $qrCode,
            'url' => $url // Opcional: se quiser mandar o link por WhatsApp
        ]);
    }
}