<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <title>Cadastro de Rotina</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .page-header-modern {
      background: white;
      padding: 1.75rem 2rem;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 1.5rem;
      flex-wrap: wrap;
    }
    .page-icon-modern {
      width: 70px;
      height: 70px;
      border-radius: 16px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: white;
      box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
      flex-shrink: 0;
    }
    .page-title-modern {
      font-size: 2rem;
      font-weight: 700;
      margin: 0 0 0.25rem 0;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .page-subtitle-modern {
      color: #64748b;
      margin: 0;
      font-size: 1rem;
    }
  </style>
</head>
<body class="p-4">
  <div class="container">
    <div class="page-header-modern">
      <div class="page-icon-modern">
        <i class="bi bi-plus-square"></i>
      </div>
      <div>
        <h1 class="page-title-modern">Cadastro de Rotina</h1>
        <p class="page-subtitle-modern">Criar nova rotina de processamento de dados</p>
      </div>
    </div>
    <form id="form-rotina" method="post" action="/rotinas/salvar">
      <div class="mb-3">
        <label class="form-label">Nome</label>
        <input name="nome" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Conexão</label>
        <select name="id_conexao" class="form-select">
          <option value="">-- selecione --</option>
        </select>
      </div>

      <h4>Blocos</h4>
      <div id="blocos">
        <!-- blocos adicionados dinamicamente -->
      </div>

      <button type="button" id="add-bloco" class="btn btn-secondary">+ Adicionar Bloco</button>
      <button type="submit" class="btn btn-primary">Salvar Rotina</button>
    </form>
  </div>

  <script>
    $('#add-bloco').on('click', function(){
      const idx = Date.now();
      $('#blocos').append(`\
        <div class="card mb-2">\
          <div class="card-body">\
            <input name="bloco_codigo[]" class="form-control mb-2" placeholder="Código do bloco">\
            <select name="tipo_bloco[]" class="form-select mb-2">\
              <option>SELECT</option><option>INSERT</option><option>UPDATE</option><option>DELETE</option><option>DDL</option>\
            </select>\
            <textarea name="script_sql[]" class="form-control" rows="6" placeholder="SQL..."></textarea>\
          </div>\
        </div>
      `);
    });
  </script>
</body>
</html>
