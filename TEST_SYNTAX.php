<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Teste de SyntaxError</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Consolas', 'Monaco', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #252526;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.6);
        }
        h1 {
            color: #4ec9b0;
            margin-bottom: 20px;
            font-size: 28px;
        }
        h2 {
            color: #569cd6;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .instructions {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #f48771;
        }
        .test-links {
            list-style: none;
            display: grid;
            gap: 10px;
        }
        .test-links li {
            background: #1e1e1e;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #4ec9b0;
        }
        .test-links a {
            color: #4ec9b0;
            text-decoration: none;
            font-weight: 600;
            display: block;
        }
        .test-links a:hover {
            color: #6dd3b8;
        }
        .code {
            background: #1e1e1e;
            padding: 15px;
            border-radius: 6px;
            font-size: 14px;
            margin: 10px 0;
            border: 1px solid #3e3e42;
            overflow-x: auto;
        }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        .console {
            background: #0e0e0e;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 2px solid #3e3e42;
            min-height: 200px;
        }
        .console-line {
            margin: 5px 0;
            font-size: 14px;
        }
        button {
            background: #0e639c;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            margin: 5px;
        }
        button:hover {
            background: #1177bb;
        }
        .timestamp {
            color: #858585;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Diagnóstico de SyntaxError - DMC Workflow</h1>
        
        <div class="instructions">
            <h2>⚠️ IMPORTANTE</h2>
            <p>Se você está vendo "Uncaught SyntaxError: Invalid or unexpected token", é cache do navegador.</p>
            <div class="code">
                <strong class="warning">Solução:</strong><br>
                1. Abra o DevTools (F12)<br>
                2. Vá na aba "Network"<br>
                3. Marque "Disable cache"<br>
                4. Recarregue a página (Ctrl+F5)<br>
                <strong class="error">OU:</strong><br>
                Clique com direito → "Inspecionar" → Ctrl+Shift+Delete → Limpar dados
            </div>
        </div>

        <h2>🔗 Links de Teste (Com Cache-Buster)</h2>
        <ul class="test-links">
            <li>
                <a href="/DMC-DATALOAD/test_workflow_workflows.php?nocache=<?= time() ?>" target="_blank">
                    test_workflow_workflows.php?nocache=<?= time() ?>
                </a>
            </li>
            <li>
                <a href="/DMC-DATALOAD/test_workflow_execucoes.php?nocache=<?= time() ?>" target="_blank">
                    test_workflow_execucoes.php?nocache=<?= time() ?>
                </a>
            </li>
            <li>
                <a href="/DMC-DATALOAD/test_workflow_apis.php?nocache=<?= time() ?>" target="_blank">
                    test_workflow_apis.php?nocache=<?= time() ?>
                </a>
            </li>
            <li>
                <a href="/DMC-DATALOAD/test_workflow_eventos.php?nocache=<?= time() ?>" target="_blank">
                    test_workflow_eventos.php?nocache=<?= time() ?>
                </a>
            </li>
            <li>
                <a href="/DMC-DATALOAD/test_workflow_builder.php?nocache=<?= time() ?>" target="_blank">
                    test_workflow_builder.php?nocache=<?= time() ?>
                </a>
            </li>
        </ul>

        <h2>🎯 Teste Rápido de JavaScript</h2>
        <button onclick="testarJavaScript()">▶ Executar Teste</button>
        <button onclick="document.getElementById('console').innerHTML = ''">🗑️ Limpar Console</button>
        
        <div class="console" id="console">
            <div class="console-line success">Console pronto. Clique em "Executar Teste" acima.</div>
        </div>

        <h2>📋 Status dos Heredocs</h2>
        <div class="code">
            <span class="success">✅ workflows.php linha 334:</span> $extraScripts = &lt;&lt;&lt;'SCRIPTS'<br>
            <span class="success">✅ workflow-execucoes.php linha 365:</span> $extraScripts = &lt;&lt;&lt;'SCRIPTS'<br>
            <span class="success">✅ workflow-builder.php linha 408:</span> $extraScripts = &lt;&lt;&lt;'SCRIPTS'<br>
            <span class="success">✅ apis-externas.php linha 533:</span> $extraScripts = &lt;&lt;&lt;'SCRIPTS'<br>
            <span class="success">✅ eventos-api.php linha 546:</span> $extraScripts = &lt;&lt;&lt;'SCRIPTS'<br>
        </div>

        <div class="timestamp">
            Gerado em: <?= date('d/m/Y H:i:s') ?> | Timestamp: <?= time() ?>
        </div>
    </div>

    <script>
        console.log('✅ Esta página carregou sem SyntaxError');
        console.log('📅 Timestamp:', <?= time() ?>);

        function testarJavaScript() {
            const consoleEl = document.getElementById('console');
            consoleEl.innerHTML = '';
            
            function addLog(msg, type = 'success') {
                const line = document.createElement('div');
                line.className = `console-line ${type}`;
                line.textContent = msg;
                consoleEl.appendChild(line);
            }

            addLog('🚀 Iniciando testes...', 'warning');
            
            // Teste 1: Template literals
            try {
                const test1 = `Template literal funciona: ${new Date().toISOString()}`;
                addLog('✅ Template literals: OK', 'success');
            } catch (e) {
                addLog(`❌ Template literals: ${e.message}`, 'error');
            }

            // Teste 2: Arrow functions
            try {
                const test2 = () => 'Arrow function funciona';
                addLog('✅ Arrow functions: OK', 'success');
            } catch (e) {
                addLog(`❌ Arrow functions: ${e.message}`, 'error');
            }

            // Teste 3: Destructuring
            try {
                const {name, value} = {name: 'teste', value: 123};
                addLog('✅ Destructuring: OK', 'success');
            } catch (e) {
                addLog(`❌ Destructuring: ${e.message}`, 'error');
            }

            // Teste 4: Fetch API
            try {
                if (typeof fetch !== 'undefined') {
                    addLog('✅ Fetch API: Disponível', 'success');
                } else {
                    addLog('⚠️ Fetch API: Não disponível', 'warning');
                }
            } catch (e) {
                addLog(`❌ Fetch API: ${e.message}`, 'error');
            }

            // Teste 5: jQuery
            try {
                if (typeof $ !== 'undefined') {
                    addLog('✅ jQuery: Carregado', 'success');
                } else {
                    addLog('⚠️ jQuery: Não carregado (esperado nesta página)', 'warning');
                }
            } catch (e) {
                addLog(`❌ jQuery: ${e.message}`, 'error');
            }

            addLog('', 'success');
            addLog('🎉 Testes concluídos!', 'success');
            addLog('Se todos passaram, o JavaScript está funcionando corretamente.', 'success');
        }
    </script>
</body>
</html>
