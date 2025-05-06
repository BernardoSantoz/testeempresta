<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Services\Util;

class SimulacaoService
{
    /**
     * Função responsável por processar a simulação de empréstimo,
     * utilizando os parâmetros das instituições, convênios, taxas e dados validados.
     * A simulação retorna um array com os cálculos ou uma mensagem de erro se não for possível realizar a simulação.
     *
     * @param array $instituicoes
     * @param array $convenios
     * @param array $taxas
     * @param array $validated
     * @return array
     */
    public static function processarSimulacao(array $instituicoes, array $convenios, array $taxas, array $validated)
    {
        // Filtra as instituições com base nas chaves validadas fornecidas na requisição
        $instituicoes = Util::mapFilterArray($instituicoes, 'chave', $validated['instituicoes'] ?? []);
        
        // Filtra os convênios com base nas chaves validadas fornecidas na requisição
        $convenios = Util::mapFilterArray($convenios, 'chave', $validated['convenios'] ?? []);

        // Inicializa o array que irá conter os resultados da simulação
        $calcsimul = [];

        // Itera sobre todas as instituições para realizar a simulação para cada uma delas
        // A cada iteração, vai buscar as taxas que correspondem à instituição e convênios filtrados
        for ($i = 0; $i < count($instituicoes); $i++) {
            $curInst = $instituicoes[$i]; // A instituição atual sendo processada

            // Filtra as taxas de acordo com a instituição e os convênios válidos
            $taxasFilt = array_filter($taxas, fn($i) => $curInst == $i['instituicao'] && in_array($i['convenio'], $convenios));
            
            // Se o parâmetro 'parcela' foi fornecido na requisição, filtra também as taxas pela quantidade de parcelas
            if ($validated['parcela']) {
                $taxasFilt = array_filter($taxasFilt, fn($i) => $validated['parcela'] == $i['parcelas']);
            }

            // Re-indexa o array para garantir que a chave dos elementos filtrados seja numérica consecutiva
            $taxasFilt = array_values($taxasFilt);

            // Itera sobre as taxas filtradas para calcular o valor da parcela para cada taxa encontrada
            for ($t = 0; $t < count($taxasFilt); $t++) {
                $curTaxa = $taxasFilt[$t]; // A taxa atual sendo processada

                // Inicializa o array para a instituição caso ainda não tenha sido inicializado
                $calcsimul[$curInst] = $calcsimul[$curInst] ?? [];
                
                // Adiciona o cálculo para a instituição atual no array de resultados
                $calcsimul[$curInst][$t] = [
                    'taxa' => $curTaxa['taxaJuros'],
                    'parcelas' => $curTaxa['parcelas'],
                    'valor_parcela' => ($validated['valor_emprestimo'] * $curTaxa['coeficiente']), // Cálculo do valor da parcela
                    'convenio' => $curTaxa['convenio']
                ];
            }
        }

        // Se não foram encontrados resultados, retorna um erro com a mensagem apropriada
        if (!count($calcsimul)) {
            $calcsimul = ['error' => ['message' => "Não há uma simulação possível com esses parâmetros", 'code' => 404]];
        }

        // Retorna o resultado da simulação (ou o erro, caso não tenha sido possível realizar a simulação)
        return $calcsimul;
    }
}
