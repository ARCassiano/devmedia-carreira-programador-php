<?php 
	/**
	 *	Arquivo principal da aplicação
	 */


	/**
	 *	Verirficar se o IP está em nossa blocklist
	 */
	$ipsbloqueados	= array();

	foreach ($ipsbloqueados as $ip) {
		if($ip == $_SERVER["REMOTE_ADDR"]){
			header("Location: ./negado.php");
			exit();
		}
	}

	/**
	 *	Arquivos essenciais
	 */
	require_once("libs/funcoes.php");
	require_once("application.php");

	/**
	 *	Previnir o cache nas páginas
	 */
	header("Expires: Mon, 21 Out 1999 00:00:00 GMT");
	header("Cache-control: no-cache");
	header("Pragma: no-cache");


	/**
	 *	Controle de acesso ao módulo ($modulo)
	 */
	$modulo = (isset($_GET["m"])) ? tStr($_GET["m"]) : "inicial";

	/**
	 *	Controle do Front-end
	 *		- Páginal Inicial
	 *		- Post
	 *		- Contato
	 */
	switch ($modulo) {
		case 'post':
			# Controle do módulo inicial (Posts) - Por URL amigável
			$app 	= new App();
			$site	= $app->loadModel("Site");
			
			# URL amigável do post
			$url	= isset($_GET["id"]) ? tStr($_GET["url"]) : "";

			# Buscar os dados do post, utilizando como parâmetro a URL amigável
			$post 	= $site->getPos($app->conexao, $codpost = null, $url);

			# Buscar as imagens do post
			$obj		= $site->listaImagensPost($app->conexao, $post->postid, "");
			$imagens 	=$obj->fetchAll(PDO::FETCH_ASSOC);


			# Carregar array com as categorias do blog
			$obj			= $site->listaCategorias($app->conexao);
			$categorias 	= $obj->fetchAll(PDO::FETCH_ASSOC);


			# Renderizar o model
			renderizaVerPost($app, $categorias, $posts, $imagens);
			break;
		case 'categoria':
			# Controle do módulo inicial (Posts) - Por categoria
			$app 	= new App();
			$site	= $app->loadModel("Site");
			
			# Carregar array com os posts do blog desbloquados e de uma determinda categoria
			$obj	= $site->listaPosts($app->conexao, 0, (int)$_GET["id"]);
			$posts 	= $obj->fetchAll(PDO::FETCH_ASSOC);

			# Carregar array com as categorias do blog
			$obj			= $site->listaCategorias($app->conexao);
			$categorias 	= $obj->fetchAll(PDO::FETCH_ASSOC);

			# Renderizar o model
			renderizaPaginaInicial($app, $categorias, $posts);
			break;
		
		default:
			# Controle do módulo inicial (Posts)
			$app 	= new App();
			$site	= $app->loadModel("Site");
			
			# Carregar array com os posts desbloquados do blog
			$obj	= $site->listaPosts($app->conexao, 0);
			$posts 	= $obj->fetchAll(PDO::FETCH_ASSOC);

			# Carregar array com as categorias do blog
			$obj			= $site->listaCategorias($app->conexao);
			$categorias 	= $obj->fetchAll(PDO::FETCH_ASSOC);

			# Renderizar o model
			renderizaPaginaInicial($app, $categorias, $posts);
			break;
	}



	/**
	 *	Função que irá recolher os dados e chamar a View do Site
	 */
	function renderizaPaginaInicial($app, $categorias, $posts){
		# Dados que devem ser carregados pela view
		$param	= array(
							"titulo"	=>	$app->site_titulo,
							"pagina"	=>	"inicial",
							"inicial"	=>	array(
												"posts"			=> $posts,
												"categorias"	=> $categorias
											)
						);

		# Chamada da view Site
		$app->loadView("Site", $param);
	}

	/**
	 *	Função que irá recolher os dados e chamar a View do Site, exibindo apenas um post
	 */
	function renderizaVerPost($app, $categorias, $posts, $imagens){
		# Dados que devem ser carregados pela view
		$param	= array(
							"titulo"	=>	$app->site_titulo,
							"pagina"	=>	"verpost",
							"verpost"	=>	array(
												"post"			=> $post,
												"categorias"	=> $categorias,
												"imagens"		=> $imagens
											)
						);

		# Chamada da view Site
		$app->loadView("Site", $param);
	}