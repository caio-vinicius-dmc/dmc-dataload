<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>CACHE CLEAR TEST</title></head>
<body style="font-family: monospace; padding: 20px; background: #1a1f2e; color: #fff;">

<h1 style="color: #10b981;">🧪 TESTE DE CACHE - WORKFLOW MODULE</h1>

<div style="background: #252b3b; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h2 style="color: #f59e0b;">⚠️ INSTRUÇÕES</h2>
    <ol style="line-height: 2;">
        <li><strong>LIMPE O CACHE DO NAVEGADOR</strong> (Ctrl + Shift + Delete)</li>
        <li><strong>FECHE TODAS AS ABAS</strong> do site DMC-DATALOAD</li>
        <li><strong>FECHE O NAVEGADOR COMPLETAMENTE</strong></li>
        <li><strong>ABRA O NAVEGADOR NOVAMENTE</strong></li>
        <li><strong>ACESSE OS LINKS ABAIXO</strong> (sem fazer login)</li>
    </ol>
</div>

<div style="background: #252b3b; padding: 20px; border-radius: 8px;">
    <h2 style="color: #3b82f6;">📋 PÁGINAS DE TESTE (SEM AUTENTICAÇÃO)</h2>
    <p>Estas páginas renderizam DIRETAMENTE sem passar pelo sistema de auth.</p>
    <p><strong>Se funcionarem, o problema é cache do navegador.</strong></p>
    
    <ul style="list-style: none; padding: 0; line-height: 2.5;">
        <li>✅ <a href="/DMC-DATALOAD/test_workflow_workflows.php" style="color: #10b981;">workflows.php</a></li>
        <li>✅ <a href="/DMC-DATALOAD/test_workflow_execucoes.php" style="color: #10b981;">workflow-execucoes.php</a></li>
        <li>✅ <a href="/DMC-DATALOAD/test_workflow_apis.php" style="color: #10b981;">apis-externas.php</a></li>
        <li>✅ <a href="/DMC-DATALOAD/test_workflow_eventos.php" style="color: #10b981;">eventos-api.php</a></li>
        <li>✅ <a href="/DMC-DATALOAD/test_workflow_builder.php" style="color: #10b981;">workflow-builder.php</a></li>
    </ul>
</div>

<div style="background: #252b3b; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h2 style="color: #ef4444">🔍 COMO VERIFICAR SE ESTÁ OK</h2>
    <ol style="line-height: 2;">
        <li>Abra o <strong>Console do Navegador</strong> (F12 → Console)</li>
        <li>Recarregue a página</li>
        <li><strong>NÃO DEVE TER ERROS VERMELHOS</strong></li>
        <li>Se tiver "Uncaught SyntaxError", o problema persiste</li>
        <li>Se NÃO tiver erros, o problema era cache</li>
    </ol>
</div>

<div style="background: #dc2626; padding: 20px; border-radius: 8px; color: #fff;">
    <h2>⚡ ÚLTIMA ATUALIZAÇÃO</h2>
    <p><strong>Data/Hora:</strong> <?= date('d/m/Y H:i:s') ?></p>
    <p><strong>Correção aplicada:</strong> Heredoc PHP com aspas simples <<<'SCRIPTS'></p>
    <p><strong>Arquivos corrigidos:</strong> 5/5</p>
</div>

</body>
</html>
