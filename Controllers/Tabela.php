<?php

	class Tabela
	{	

		protected function listaAlunos ()
		{
			$json = file_get_contents(MAIN_FOLDER . '/data/alunos.json');
			return json_decode($json, true);
		}

		function buscaDados ($request)
		{
			$listaAlunos = [];

			foreach ($this->listaAlunos() as $id => $dados)
			{
				//Adicionado tratamento para exibir de forma correta o sexo do aluno

				if ($dados['sexo'] == 'm' OR $dados['sexo'] == 'Masculino' OR $dados['sexo'] == 'masculino'){
					$listaAlunos[] =
						[
							"id" => $id,
							"nome" => $dados['nome'],
							"turma" => $dados['turma'],
							"sexo" => "Masculino"
						];
				}else{
					$listaAlunos[] =
						[
							"id" => $id,
							"nome" => $dados['nome'],
							"turma" => $dados['turma'],
							"sexo" => "Feminino"
						];
				}
			}

			return $listaAlunos;
		}

		function salvaDados ($request)
		{
			$alunos = $this->listaAlunos();

			foreach ($request as $aluno)
			{
				//Tratamento para adicionar um id a novos alunos para que não fique como null
				if ($aluno['id'] == null OR $aluno['id'] == "") {
					$aluno['id'] = count($request);
				}

				$id = $aluno['id'];
				$alunos[$id]['nome'] = $aluno['nome'];
				$alunos[$id]['turma'] = $aluno['turma'];
				$alunos[$id]['sexo'] = $aluno['sexo'];
			}

			file_put_contents(MAIN_FOLDER . '/data/alunos.json', json_encode($alunos));
		}
	}