# Script para mover JavaScripts inline para $extraScripts

$files = @(
    'views/apis-externas.php',
    'views/eventos-api.php', 
    'views/workflows.php',
    'views/workflow-execucoes.php',
    'views/workflow-builder.php'
)

foreach($file in $files) {
    Write-Host "Processando $file..."
    
    $content = Get-Content $file -Raw -Encoding UTF8
    
    # Encontrar o script inline
    $pattern = '(?s)<script>(.*?)</script>\s*<\?php'
    if ($content -match $pattern) {
        $scriptContent = $matches[1]
        
        # Remover o script inline
        $content = $content -replace '(?s)<script>.*?</script>\s*(<\?php)', '$1'
        
        # Adicionar no extraScripts
        $content = $content -replace '(\$extraScripts = <<<SCRIPTS)([\r\n]+)(SCRIPTS;)', "`$1`n<script>`$scriptContent</script>`n`$3"
        
        # Salvar
        $content | Set-Content $file -Encoding UTF8 -NoNewline
        Write-Host "  OK Script movido para extraScripts" -ForegroundColor Green
    } else {
        Write-Host "  AVISO Padrao nao encontrado" -ForegroundColor Yellow
    }
}

Write-Host "`nTodos os arquivos processados!" -ForegroundColor Green
