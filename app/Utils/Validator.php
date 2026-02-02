<?php
namespace App\Utils;

class Validator
{
    private array $erros = [];
    private array $dados;

    public function __construct(array $dados)
    {
        $this->dados = $dados;
    }

    public function obrigatório(string $campo, string $mensagem = null): self
    {
        if (!isset($this->dados[$campo]) || trim($this->dados[$campo]) === '') {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} é obrigatório";
        }
        return $this;
    }

    public function email(string $campo, string $mensagem = null): self
    {
        if (isset($this->dados[$campo]) && !filter_var($this->dados[$campo], FILTER_VALIDATE_EMAIL)) {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} deve ser um email válido";
        }
        return $this;
    }

    public function minimo(string $campo, int $min, string $mensagem = null): self
    {
        if (isset($this->dados[$campo]) && strlen($this->dados[$campo]) < $min) {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} deve ter no mínimo {$min} caracteres";
        }
        return $this;
    }

    public function maximo(string $campo, int $max, string $mensagem = null): self
    {
        if (isset($this->dados[$campo]) && strlen($this->dados[$campo]) > $max) {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} deve ter no máximo {$max} caracteres";
        }
        return $this;
    }

    public function inteiro(string $campo, string $mensagem = null): self
    {
        if (isset($this->dados[$campo]) && !filter_var($this->dados[$campo], FILTER_VALIDATE_INT)) {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} deve ser um número inteiro";
        }
        return $this;
    }

    public function entre(string $campo, $min, $max, string $mensagem = null): self
    {
        if (isset($this->dados[$campo])) {
            $valor = $this->dados[$campo];
            if ($valor < $min || $valor > $max) {
                $this->erros[$campo] = $mensagem ?? "O campo {$campo} deve estar entre {$min} e {$max}";
            }
        }
        return $this;
    }

    public function em(string $campo, array $valores, string $mensagem = null): self
    {
        if (isset($this->dados[$campo]) && !in_array($this->dados[$campo], $valores, true)) {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} contém um valor inválido";
        }
        return $this;
    }

    public function regex(string $campo, string $pattern, string $mensagem = null): self
    {
        if (isset($this->dados[$campo]) && !preg_match($pattern, $this->dados[$campo])) {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} não está no formato correto";
        }
        return $this;
    }

    public function valido(): bool
    {
        return empty($this->erros);
    }

    public function erros(): array
    {
        return $this->erros;
    }

    public function primeiroErro(): ?string
    {
        return !empty($this->erros) ? reset($this->erros) : null;
    }

    public static function sanitizar(string $valor, string $tipo = 'string'): mixed
    {
        switch ($tipo) {
            case 'email':
                return filter_var($valor, FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'url':
                return filter_var($valor, FILTER_SANITIZE_URL);
            case 'html':
                return htmlspecialchars($valor, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            case 'string':
            default:
                return strip_tags(trim($valor));
        }
    }

    public static function sanitizarArray(array $dados, array $tipos = []): array
    {
        $resultado = [];
        
        foreach ($dados as $campo => $valor) {
            $tipo = $tipos[$campo] ?? 'string';
            
            if (is_array($valor)) {
                $resultado[$campo] = self::sanitizarArray($valor, $tipos);
            } else {
                $resultado[$campo] = self::sanitizar($valor, $tipo);
            }
        }
        
        return $resultado;
    }

    public static function validarSenhaForte(string $senha): array
    {
        $erros = [];
        
        if (strlen($senha) < 8) {
            $erros[] = 'A senha deve ter no mínimo 8 caracteres';
        }
        if (!preg_match('/[A-Z]/', $senha)) {
            $erros[] = 'A senha deve conter pelo menos uma letra maiúscula';
        }
        if (!preg_match('/[a-z]/', $senha)) {
            $erros[] = 'A senha deve conter pelo menos uma letra minúscula';
        }
        if (!preg_match('/[0-9]/', $senha)) {
            $erros[] = 'A senha deve conter pelo menos um número';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $senha)) {
            $erros[] = 'A senha deve conter pelo menos um caractere especial';
        }
        
        return $erros;
    }
    
    /**
     * Validação estática com regras no formato "required|email|min:6"
     */
    public static function validar(array $dados, array $regras): array
    {
        $erros = [];
        
        foreach ($regras as $campo => $regrasStr) {
            $regrasList = is_array($regrasStr) ? $regrasStr : explode('|', $regrasStr);
            $valor = $dados[$campo] ?? null;
            
            foreach ($regrasList as $regra) {
                $partes = explode(':', $regra);
                $nomeRegra = $partes[0];
                $parametro = $partes[1] ?? null;
                
                $erro = self::aplicarRegra($nomeRegra, $valor, $parametro, $campo);
                if ($erro) {
                    $erros[$campo] = $erro;
                    break; // Apenas primeiro erro por campo
                }
            }
        }
        
        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }
    
    private static function aplicarRegra(string $regra, $valor, $parametro, string $campo): ?string
    {
        switch ($regra) {
            case 'required':
                if ($valor === null || $valor === '') {
                    return "O campo {$campo} é obrigatório";
                }
                break;
                
            case 'email':
                if ($valor && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    return "O campo {$campo} deve ser um email válido";
                }
                break;
                
            case 'min':
                if ($valor && strlen($valor) < (int)$parametro) {
                    return "O campo {$campo} deve ter no mínimo {$parametro} caracteres";
                }
                break;
                
            case 'max':
                if ($valor && strlen($valor) > (int)$parametro) {
                    return "O campo {$campo} deve ter no máximo {$parametro} caracteres";
                }
                break;
                
            case 'numeric':
                if ($valor && !is_numeric($valor)) {
                    return "O campo {$campo} deve ser numérico";
                }
                break;
                
            case 'int':
            case 'integer':
                if ($valor && !filter_var($valor, FILTER_VALIDATE_INT)) {
                    return "O campo {$campo} deve ser um número inteiro";
                }
                break;
        }
        
        return null;
    }
}
