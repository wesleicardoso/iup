<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CnpjApiController extends Controller
{
    /**
     * Consulta dados da empresa via CNPJ na API CNPJA (Modo Público/Sem Token).
     */
    public function lookup(Request $request, $cnpj)
    {
        // Limpa o CNPJ para a consulta
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return response()->json(['error' => 'CNPJ inválido ou incompleto.'], 422);
        }

        $url = "https://open.cnpja.com/office/{$cnpj}";

        try {
            $response = Http::timeout(10)->get($url);

            if ($response->failed()) {
                $error = $response->json('message') ?? 'CNPJ não encontrado ou erro de conexão com a API.';
                return response()->json(['error' => $error], $response->status());
            }

            $data = $response->json();

            // Verifica se o JSON tem a estrutura base esperada
            if (!isset($data['company'])) {
                return response()->json(['error' => 'Estrutura de dados CNPJ incompleta.'], 500);
            }

            // ✅ Mapeamento e retorno dos dados REVISADO para a estrutura aninhada (company, address, status)
            return response()->json([
                'success' => true,

                // DADOS ESTRUTURAIS
                'corporate_name' => $data['company']['name'], // Mapeado de company -> name
                'trade_name' => $data['alias'], // Mapeado de alias (Nome Fantasia da Filial)
                'foundation_date' => date('Y-m-d', strtotime($data['founded'])), // Mapeado de founded
                'status_code' => $data['status']['text'], // Mapeado de status -> text
                'cnae_primary' => $data['mainActivity']['id'], // Mapeado de mainActivity -> id
                'legal_nature' => $data['company']['nature']['text'], // Mapeado de company -> nature -> text

                // ENDEREÇO
                'zip_code' => $data['address']['zip'],
                'address_line_1' => $data['address']['street'],
                'address_number' => $data['address']['number'],
                'neighborhood' => $data['address']['district'],
                'city' => $data['address']['city'],
                'state' => $data['address']['state'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro interno na consulta.'], 500);
        }
    }
}
