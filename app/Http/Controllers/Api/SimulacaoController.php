<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SimulacaoService;
use App\Services\Util;

class SimulacaoController extends Controller
{
    /**
     * Função responsável por processar a simulação de empréstimo.
     * Recebe os dados da requisição, valida, carrega os arquivos necessários e
     * chama o serviço para processar a simulação.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calcSimulacao(Request $request)
    {
        try {
            // Carrega os arquivos JSON necessários para o processamento
            // e valida a existência e a integridade dos dados.
            $instituicoes = Util::loadJsonWithValidation('instituicoes.json');
            $convenios = Util::loadJsonWithValidation('convenios.json');
            $taxas = Util::loadJsonWithValidation('taxas_instituicoes.json');
        } catch (\Throwable $th) {
            // Em caso de erro ao carregar os arquivos, retorna um erro com a mensagem e código da exceção
            return response()->json(
                ['error' => $th->getMessage()],
                $th->getCode(),
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        // Valida os dados da requisição com base nas regras definidas.
        $validated = $request->validate([
            'parcela' => 'nullable|integer',
            'instituicoes' => 'nullable|array',
            'convenios' => 'nullable|array',
            'valor_emprestimo' => 'required|numeric'
        ]);

        // Chama o serviço SimulacaoService para processar a simulação de acordo com os dados validados
        return response()->json(
            SimulacaoService::processarSimulacao($instituicoes, $convenios, $taxas, $validated), 
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
