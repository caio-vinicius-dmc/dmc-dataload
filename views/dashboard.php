<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <title>Dashboard - DMC DataLoad</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    .sidebar {
      min-height: 100vh;
      background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 12px 20px;
      display: block;
      border-radius: 8px;
      margin: 5px 10px;
      transition: all 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background: rgba(255,255,255,0.2);
    }
    .card-metric {
      border-left: 4px solid #667eea;
    }
    .stat-number {
      font-size: 32px;
      font-weight: bold;
      color: #667eea;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar p-0">
        <div class="p-3">
          <h4 class="text-center mb-4">🔐 DMC DataLoad</h4>
          <div id="userInfo" class="text-center mb-4">
            <small>Usuário: <strong id="userName"></strong></small><br>
            <small id="userLevel"></small>
          </div>
        </div>
        <?php $base = defined('BASE_URL') ? BASE_URL : ''; ?>
        <nav>
          <a href="<?= $base ?>/dashboard" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
          <a href="<?= $base ?>/rotinas"><i class="bi bi-play-circle"></i> Rotinas</a>
          <a href="<?= $base ?>/conexoes"><i class="bi bi-hdd-network"></i> Conexões</a>
          <a href="<?= $base ?>/historico"><i class="bi bi-clock-history"></i> Histórico</a>
          <hr style="border-color: rgba(255,255,255,0.3)">
          <a href="#" id="btnLogout"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </nav>
      </div>

      <!-- Content -->
      <div class="col-md-10 p-4">
        <h2>Dashboard</h2>
        <p class="text-muted">Visão geral das execuções</p>

        <!-- Métricas -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="card card-metric">
              <div class="card-body">
                <small class="text-muted">Total de Rotinas</small>
                <div class="stat-number" id="totalRotinas">-</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card card-metric" style="border-color: #28a745;">
              <div class="card-body">
                <small class="text-muted">Execuções Hoje</small>
                <div class="stat-number" style="color: #28a745;" id="execHoje">-</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card card-metric" style="border-color: #dc3545;">
              <div class="card-body">
                <small class="text-muted">Falhas Hoje</small>
                <div class="stat-number" style="color: #dc3545;" id="falhasHoje">-</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card card-metric" style="border-color: #ffc107;">
              <div class="card-body">
                <small class="text-muted">Em Execução</small>
                <div class="stat-number" style="color: #ffc107;" id="emExec">-</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-header">📊 Execuções (Últimos 7 dias)</div>
              <div class="card-body">
                <canvas id="chartExec" height="200"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-header">🎯 Taxa de Sucesso</div>
              <div class="card-body">
                <canvas id="chartSucesso" height="200"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Últimas Execuções -->
        <div class="card">
          <div class="card-header">📋 Últimas Execuções</div>
          <div class="card-body">
            <table class="table">
              <thead>
                <tr>
                  <th>Rotina</th>
                  <th>Data/Hora</th>
                  <th>Status</th>
                  <th>Duração</th>
                </tr>
              </thead>
              <tbody id="ultimasExec">
                <tr><td colspan="4" class="text-center">Carregando...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    const baseUrl = '<?= defined("BASE_URL") ? BASE_URL : "" ?>';
    
    // Verificar sessão
    $.getJSON(baseUrl + '/api/sessao', function(res){
      if (!res.autenticado) {
        window.location.href = baseUrl + '/login';
        return;
      }
      $('#userName').text(res.usuario.nome_usuario);
      $('#userLevel').text('Nível: ' + res.usuario.nivel_acesso);
    }).fail(function(){
      window.location.href = baseUrl + '/login';
    });

    // Logout
    $('#btnLogout').on('click', function(e){
      e.preventDefault();
      $.post(baseUrl + '/logout', function(){
        window.location.href = baseUrl + '/login';
      });
    });

    // Carregar métricas
    let chartExec, chartSucesso;
    
    function carregarMetricas(){
      $.getJSON(baseUrl + '/api/dashboard/metricas', function(res){
        if (!res.sucesso) return;
        
        $('#totalRotinas').text(res.total_rotinas || 0);
        $('#execHoje').text(res.execucoes_hoje || 0);
        $('#falhasHoje').text(res.falhas_hoje || 0);
        $('#emExec').text(res.em_execucao || 0);
        
        // Atualizar gráfico de execuções
        if (res.grafico_7dias && res.grafico_7dias.length > 0) {
          const labels = res.grafico_7dias.map(d => {
            const dt = new Date(d.data);
            return dt.toLocaleDateString('pt-BR', {weekday: 'short'});
          });
          const sucesso = res.grafico_7dias.map(d => parseInt(d.sucesso) || 0);
          const falha = res.grafico_7dias.map(d => parseInt(d.falha) || 0);
          
          chartExec.data.labels = labels;
          chartExec.data.datasets[0].data = sucesso;
          chartExec.data.datasets[1].data = falha;
          chartExec.update();
          
          // Atualizar gráfico de sucesso
          const totalSucesso = sucesso.reduce((a,b) => a+b, 0);
          const totalFalha = falha.reduce((a,b) => a+b, 0);
          chartSucesso.data.datasets[0].data = [totalSucesso, totalFalha];
          chartSucesso.update();
        }
        
        // Atualizar últimas execuções
        if (res.ultimas_execucoes) {
          let html = '';
          if (res.ultimas_execucoes.length === 0) {
            html = '<tr><td colspan="4" class="text-center text-muted">Nenhuma execução recente</td></tr>';
          } else {
            res.ultimas_execucoes.forEach(e => {
              const statusClass = e.status === 'sucesso' ? 'success' : (e.status === 'falha' ? 'danger' : 'warning');
              const data = e.data_inicio ? new Date(e.data_inicio).toLocaleString('pt-BR') : '-';
              const duracao = e.duracao_ms ? (e.duracao_ms < 1000 ? e.duracao_ms + 'ms' : (e.duracao_ms/1000).toFixed(1) + 's') : '-';
              html += `<tr>
                <td>${e.rotina || 'Desconhecida'}</td>
                <td>${data}</td>
                <td><span class="badge bg-${statusClass}">${e.status}</span></td>
                <td>${duracao}</td>
              </tr>`;
            });
          }
          $('#ultimasExec').html(html);
        }
      });
    }

    // Gráfico de execuções
    const ctxExec = document.getElementById('chartExec').getContext('2d');
    chartExec = new Chart(ctxExec, {
      type: 'bar',
      data: {
        labels: [],
        datasets: [
          {
            label: 'Sucesso',
            data: [],
            backgroundColor: '#28a745',
          },
          {
            label: 'Falha',
            data: [],
            backgroundColor: '#dc3545'
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          x: { stacked: true },
          y: { stacked: true, beginAtZero: true }
        }
      }
    });

    // Gráfico de sucesso
    const ctxSucesso = document.getElementById('chartSucesso').getContext('2d');
    chartSucesso = new Chart(ctxSucesso, {
      type: 'doughnut',
      data: {
        labels: ['Sucesso', 'Falha'],
        datasets: [{
          data: [0, 0],
          backgroundColor: ['#28a745', '#dc3545']
        }]
      },
      options: {
        responsive: true
      }
    });

    carregarMetricas();
    setInterval(carregarMetricas, 30000); // Atualiza a cada 30s
  </script>
</body>
</html>