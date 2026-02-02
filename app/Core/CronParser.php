<?php
namespace App\Core;

use Cron\CronExpression;

/**
 * Parser de expressões Cron usando biblioteca dragonmantank/cron-expression
 */
class CronParser
{
    /**
     * Valida uma expressão cron
     * @return array ['valida' => bool, 'erro' => string|null]
     */
    public static function validar(string $expressao): array
    {
        if (empty(trim($expressao))) {
            return ['valida' => false, 'erro' => 'Expressão vazia'];
        }
        
        try {
            if (!CronExpression::isValidExpression($expressao)) {
                return ['valida' => false, 'erro' => 'Expressão cron inválida'];
            }
            new CronExpression($expressao);
            return ['valida' => true, 'erro' => null];
        } catch (\Throwable $e) {
            return ['valida' => false, 'erro' => $e->getMessage()];
        }
    }
    
    /**
     * Calcula a próxima execução
     */
    public static function proximaExecucao(string $expressao, ?\DateTime $base = null): ?\DateTime
    {
        try {
            $cron = new CronExpression($expressao);
            return $cron->getNextRunDate($base ?? new \DateTime());
        } catch (\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Calcula as próximas N execuções
     */
    public static function proximasExecucoes(string $expressao, int $quantidade = 5, ?\DateTime $base = null): array
    {
        try {
            $cron = new CronExpression($expressao);
            $execucoes = [];
            
            for ($i = 0; $i < $quantidade; $i++) {
                $execucoes[] = $cron->getNextRunDate($base ?? new \DateTime(), $i);
            }
            
            return $execucoes;
        } catch (\Throwable $e) {
            return [];
        }
    }
    
    /**
     * Verifica se está no momento de executar (considerando margem de 1 minuto)
     */
    public static function deveExecutarAgora(string $expressao): bool
    {
        try {
            $cron = new CronExpression($expressao);
            return $cron->isDue();
        } catch (\Throwable $e) {
            return false;
        }
    }
    
    /**
     * Retorna descrição legível da expressão cron
     */
    public static function descrever(string $expressao): string
    {
        $partes = explode(' ', trim($expressao));
        
        if (count($partes) !== 5) {
            return 'Expressão inválida';
        }
        
        [$minuto, $hora, $dia, $mes, $diaSemana] = $partes;
        
        // Expressões comuns
        $padroes = [
            '* * * * *' => 'A cada minuto',
            '*/5 * * * *' => 'A cada 5 minutos',
            '*/10 * * * *' => 'A cada 10 minutos',
            '*/15 * * * *' => 'A cada 15 minutos',
            '*/30 * * * *' => 'A cada 30 minutos',
            '0 * * * *' => 'A cada hora',
            '0 */2 * * *' => 'A cada 2 horas',
            '0 */6 * * *' => 'A cada 6 horas',
            '0 0 * * *' => 'Diariamente à meia-noite',
            '0 6 * * *' => 'Diariamente às 6h',
            '0 12 * * *' => 'Diariamente ao meio-dia',
            '0 18 * * *' => 'Diariamente às 18h',
            '0 0 * * 0' => 'Semanalmente (domingo)',
            '0 0 * * 1' => 'Semanalmente (segunda)',
            '0 0 1 * *' => 'Mensalmente (dia 1)',
            '0 0 1 1 *' => 'Anualmente (1º de janeiro)',
        ];
        
        if (isset($padroes[$expressao])) {
            return $padroes[$expressao];
        }
        
        // Descrição genérica
        $desc = [];
        
        if ($minuto !== '*') {
            $desc[] = "minuto {$minuto}";
        }
        if ($hora !== '*') {
            $desc[] = "hora {$hora}";
        }
        if ($dia !== '*') {
            $desc[] = "dia {$dia}";
        }
        if ($mes !== '*') {
            $desc[] = "mês {$mes}";
        }
        if ($diaSemana !== '*') {
            $dias = ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb'];
            $desc[] = "dia da semana " . ($dias[$diaSemana] ?? $diaSemana);
        }
        
        return empty($desc) ? 'A cada minuto' : 'Executa em: ' . implode(', ', $desc);
    }
    
    /**
     * Gera expressão cron a partir de parâmetros
     * Aceita array com chaves: minuto, hora, dia, mes, dia_semana
     * Ou valores posicionais: gerarExpressao(['minuto' => 30, 'hora' => 8])
     */
    public static function gerarExpressao(array $params): string
    {
        $minuto = $params['minuto'] ?? '*';
        $hora = $params['hora'] ?? '*';
        $dia = $params['dia'] ?? '*';
        $mes = $params['mes'] ?? '*';
        $diaSemana = $params['dia_semana'] ?? '*';
        
        return "{$minuto} {$hora} {$dia} {$mes} {$diaSemana}";
    }
    
    /**
     * Retorna presets comuns de cron
     * @return array Array associativo [label => valor]
     */
    public static function presets(): array
    {
        return [
            'A cada minuto' => '* * * * *',
            'A cada 5 minutos' => '*/5 * * * *',
            'A cada 15 minutos' => '*/15 * * * *',
            'A cada 30 minutos' => '*/30 * * * *',
            'A cada hora' => '0 * * * *',
            'A cada 2 horas' => '0 */2 * * *',
            'A cada 6 horas' => '0 */6 * * *',
            'Diariamente (meia-noite)' => '0 0 * * *',
            'Diariamente (6h)' => '0 6 * * *',
            'Dias úteis (8h)' => '0 8 * * 1-5',
            'Semanalmente (domingo)' => '0 0 * * 0',
            'Mensalmente (dia 1)' => '0 0 1 * *',
        ];
    }
}
