<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
  <title>Gerenciador de Conexões</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body class="p-4">
  <div class="container">
    <h2>Conexões</h2>
    <button class="btn btn-primary mb-2" id="btnNovo">+ Nova Conexão</button>
    <table id="tbl" class="display" style="width:100%">
      <thead><tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Host</th><th>Usuário</th><th>Ações</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>

  <?php include __DIR__ . '/../../views/conexoes/modal_form.php'; ?>

  <script>
    const baseUrl = '<?= defined("BASE_URL") ? BASE_URL : "" ?>';
    
    function loadTable(){
      $.getJSON(baseUrl + '/conexoes/list', function(res){
        const tbl = $('#tbl').DataTable();
        tbl.clear();
        res.data.forEach(function(r){
          tbl.row.add([r.id, r.nome_conexao, r.tipo_banco, r.host, r.usuario, '<button class="btn btn-sm btn-secondary btn-edit" data-id="'+r.id+'">Editar</button> <button class="btn btn-sm btn-danger btn-del" data-id="'+r.id+'">Excluir</button>']);
        });
        tbl.draw();
      });
    }

    $(function(){
      $('#tbl').DataTable({columns:[{}, {}, {}, {}, {}, {}]});

      $('#btnNovo').on('click', function(){
        $('#modalConexao').show();
      });

      $(document).on('click', '.btn-edit', function(){
        const id = $(this).data('id');
        $.getJSON(baseUrl + '/conexoes/get/' + id, function(r){
          $('#modalConexao [name="nome_conexao"]').val(r.nome_conexao);
          $('#modalConexao [name="host"]').val(r.host);
          $('#modalConexao [name="porta"]').val(r.porta);
          $('#modalConexao [name="nome_banco"]').val(r.nome_banco);
          $('#modalConexao [name="usuario"]').val(r.usuario);
          $('#modalConexao').show();
        });
      });

      $(document).on('click', '.btn-del', function(){
        if(!confirm('Excluir conexão?')) return;
        const id = $(this).data('id');
        $.post(baseUrl + '/conexoes/delete/' + id, function(){ loadTable(); });
      });

      $('#btnTestar').on('click', function(){
        const data = $('#form-conexao').serialize();
        $.post(baseUrl + '/conexoes/test', data, function(res){
          alert(res.mensagem || JSON.stringify(res));
          if (res.sucesso) $('#btnSalvar').prop('disabled', false);
        }, 'json');
      });

      $('#form-conexao').on('submit', function(e){
        e.preventDefault();
        const data = $(this).serialize();
        $.post(baseUrl + '/conexoes/salvar', data, function(res){
          alert(res.mensagem || JSON.stringify(res));
          $('#modalConexao').hide();
          loadTable();
        }, 'json');
      });

      // simples show/hide modal
      $('.btn-close').on('click', function(){ $('#modalConexao').hide(); });

      loadTable();
    });
  </script>
</body>
</html>
