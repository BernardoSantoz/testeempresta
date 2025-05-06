<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class Util
{
    /**
     * Função responsável por carregar um arquivo JSON local e retornar seu conteúdo.
     * Caso o arquivo não exista ou haja algum erro na leitura, um array de erro é retornado.
     *
     * @param string $file Nome do arquivo a ser carregado
     * @return array Retorna o conteúdo decodificado do JSON ou um erro
     */
    public static function loadLocalJson(string $file)
    {
        try {
            // Verifica se o arquivo existe no armazenamento
            if (!Storage::exists("data/$file")) {
                // Retorna um erro se o arquivo não for encontrado
                return ['error' => ['message' => "Arquivo $file não encontrado", 'code' => 404]];
            }
    
            // Obtém o conteúdo do arquivo
            // Decodifica o JSON em um array associativo
            $json = Storage::get("data/$file");
            $decodedJson = json_decode($json, true);
    
            // Verifica se houve erro ao decodificar o JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Retorna um erro caso o JSON seja inválido
                return ['error' => ['message' => 'Erro ao decodificar o JSON', 'code' => 500]];
            }
    
            // Retorna o conteúdo decodificado do JSON
            return $decodedJson;
    
        } catch (\Exception $e) {
            // Registra qualquer exceção ocorrida no processo de leitura
            \Log::error("Erro ao ler $file" . $e->getMessage());
            // Retorna um erro genérico em caso de exceção
            return ['error' => ['message' => 'Erro interno do servidor', 'code' => 500]];
        }
    }

    /**
     * Função responsável por carregar um arquivo JSON e lançar uma exceção
     * caso ocorra algum erro na leitura ou validação do arquivo.
     * 
     * @param string $file Nome do arquivo a ser carregado
     * @return array Retorna os dados do JSON carregado
     * @throws \Exception Lança uma exceção caso ocorra um erro
     */
    public static function loadJsonWithValidation($file)
    {
        // Chama a função loadLocalJson para carregar o arquivo
        $data = self::loadLocalJson($file);

        // Se houver erro na leitura, lança uma exceção
        if (isset($data['error'])) {
            throw new \Exception($data['error']['message'], $data['error']['code']);
        }
        
        // Retorna os dados carregados caso não haja erro
        return $data;
    }

    /**
     * Função responsável por mapear e filtrar um array com base em um valor de chave específico.
     * Se o filtro for fornecido, ele aplica a filtragem de acordo com os valores das chaves fornecidas.
     * 
     * @param array $arr Array de dados a ser mapeado e filtrado
     * @param string $key A chave que será usada para buscar os valores no array
     * @param array $filter Valores de filtro que serão aplicados aos dados
     * @return array Array filtrado com base no valor da chave
     */
    public static function mapFilterArray(array $arr, string $key, array $filter)
    {
        // Mapeia o array para obter os valores da chave especificada
        $chaves = array_map(fn($i) => $i[$key], $arr);

        // Sanitiza o filtro, convertendo todos os valores para maiúsculas
        $sanitizedFilter = array_map(fn($i) => strtoupper($i), $filter);

        // Se houver valores de filtro, filtra os valores do array com base nas chaves
        if (count($sanitizedFilter)) {
            return array_values(array_filter($chaves, fn($i) => in_array($i, $sanitizedFilter)));
        }

        // Caso contrário, retorna todas as chaves
        return $chaves;
    }
}
