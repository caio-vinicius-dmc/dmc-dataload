<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Cadastro de Rotina</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
  <div class="container">
    <h2>Cadastro de Rotina</h2>
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
