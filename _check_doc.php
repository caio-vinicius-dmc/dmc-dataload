<?php
$html = file_get_contents('docs/documentacao_completa.html');
echo 'File size: ' . strlen($html) . ' bytes' . PHP_EOL;
echo 'Sections found: ' . substr_count($html, 'class="section"') . PHP_EOL;
echo 'Open divs: ' . substr_count($html, '<div') . PHP_EOL;
echo 'Close divs: ' . substr_count($html, '</div>') . PHP_EOL;
echo 'Open tables: ' . substr_count($html, '<table') . PHP_EOL;
echo 'Close tables: ' . substr_count($html, '</table>') . PHP_EOL;
