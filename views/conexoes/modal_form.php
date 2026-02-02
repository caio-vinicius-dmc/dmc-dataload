<div class="modal" id="modalConexao" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Conexão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="form-conexao">
          <div class="row">
            <div class="col-md-6 mb-2">
              <label>Tipo de Banco</label>
              <select name="tipo_banco" class="form-select">
                <option value="postgres">Postgres</option>
                <option value="mysql">MySQL</option>
                <option value="oracle">Oracle</option>
                <option value="sqlserver">SQL Server</option>
                <option value="odbc">ODBC</option>
              </select>
            </div>
            <div class="col-md-6 mb-2">
              <label>Nome Conexão</label>
              <input name="nome_conexao" class="form-control">
            </div>
            <div class="col-md-6 mb-2">
              <label>Host</label>
              <input name="host" class="form-control">
            </div>
            <div class="col-md-2 mb-2">
              <label>Porta</label>
              <input name="porta" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
              <label>Banco / SID</label>
              <input name="nome_banco" class="form-control">
            </div>
            <div class="col-md-6 mb-2">
              <label>Usuário</label>
              <input name="usuario" class="form-control">
            </div>
            <div class="col-md-6 mb-2">
              <label>Senha</label>
              <input name="senha" type="password" class="form-control">
            </div>
            <div class="col-12 mt-2">
              <button type="button" id="btnTestar" class="btn btn-outline-primary">Testar Conexão</button>
              <button type="submit" id="btnSalvar" class="btn btn-primary" disabled>Salvar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
