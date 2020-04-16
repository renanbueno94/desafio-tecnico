<!DOCTYPE html>
<html>
<head>
	<title>Lista de alunos</title>
	<script type="text/javascript" src="public/js/knockout-3.5.1.js"></script>
	<script type="text/javascript" src="public/js/request.js"></script>
	<link rel="stylesheet" href="public/css/bootstrap.min.css">
	<link rel="stylesheet" href="public/css/bootstrap-grid.min.css">
	<style>
		
		body{
			position: relative;
			margin:0 auto;
			font-family: sans-serif;
		}
		h1{
			margin: 45px auto;
    		text-align: center;
		}
		.container{
			width: 100%;
			max-width: 1040px;
			margin: 0 auto;
		}

	</style>
</head>
<body>
	<div id="tabela" class="container">

		<h1>Lista de alunos</h1>
		<br>
		<p data-bind="hidden: alunos().length > 0">Carregando...</p>
		<table id="tbl" data-bind="hidden: alunos().length == 0" class="table">
			<thead  class="thead-light">
				<tr>
					<th>Nome</th>
					<th>Turma</th>
					<th>Sexo</th>
					<th></th>
				</tr>
			</thead>
			<tbody data-bind="foreach: alunos">
				<tr>
					<td>
						<span data-bind="text: nome, hidden: editando"></span>
						<input class="form-control" type="text" data-bind="hidden: !editando(), value: nome">
					</td>
					<td>
						<span data-bind="text: turma, hidden: editando"></span>
						<input class="form-control" type="text" data-bind="hidden: !editando(), value: turma">
					</td>
					<td>
						<span data-bind="text: sexo, hidden: editando"></span>
						<input class="form-control" type="text" data-bind="hidden: !editando(), value: sexo">
					</td>
					<td>
						<button data-bind="click: editar, hidden: editando()" class="btn btn-light">Editar</button>
					</td>
				</tr>
			</tbody>
		</table>
		<center>
			<button data-bind="click:salvar" class="btn btn-success">Salvar alterações</button>
			<!-- Botão para novo aluno-->
			<button data-bind="click:addAluno" class="btn btn-info" >Novo Aluno</button>
		</center>
		<br>
	</div>
	<script type="text/javascript">
		
		Aluno = function (dados)
		{
			//adicionado o campo sexo para que o mesmo seja exibido e possa ser alterado
			let self = this;
			self.nome = ko.observable(dados.nome ? dados.nome : '');
			self.turma = ko.observable(dados.turma ? dados.turma : '');
			self.sexo = ko.observable(dados.sexo ? dados.sexo : '');
			self.id = ko.observable(dados.id ? dados.id : null);

			console.log(dados.id);
			
			self.editando = ko.observable(false);

			self.editar = function()
			{
				self.editando(true)
			}
		}

		//Função criada para quando o botão novo aluno for utilizado os inputs já aparecerem editaveis na tabela
		NovoAluno  = function ()
		{
			let self = this;
			self.nome = ko.observable('');
			self.turma = ko.observable('');
			self.sexo = ko.observable('');
			self.id = ko.observable(null);
			
			self.editando = ko.observable(true);
		}

		TabelaModel = function ()
		{
			let self = this;

			self.alunos = ko.observableArray();

			self.carregar = function ()
			{
				callback = function (resposta)
				{
					if(resposta.status != 200)
						return alert("Houve um erro ao carregar!");

					let alunos = ko.utils.arrayMap(resposta.data, function(aluno){
						return new Aluno(aluno);
					});
					self.alunos(alunos);
				}
				request('Tabela', 'buscaDados', callback);
			}

			//Função que adiciona uma nova linha na tabela e chama a função que libera os campos para edição
			self.addAluno = function() {
		        self.alunos.push(new NovoAluno(""));
		    }

			self.salvar = function ()
			{
				//adicionado o campo sexo para que o mesmo possa ser salvo no json
				listaAlunos = ko.utils.arrayMap(self.alunos(), function(aluno){
					return {
						nome: aluno.nome(),
						turma: aluno.turma(),
						sexo: aluno.sexo(),
						id: aluno.id()
					}		
				});

				self.alunos([]);

				callback = function (resposta)
				{
					if(resposta.status != 200)
						return alert("Houve um erro ao editar!");

					self.carregar();
				}
				request('Tabela', 'salvaDados', callback, listaAlunos);
			}
		}

		var tabela = new TabelaModel();
		ko.applyBindings(tabela, document.getElementById('tabela'));
		tabela.carregar();

	</script>
</body>
</html>