<?php

class Conf {


	static $debug = 1;
	static $adminPrefix = 'banane';
	static $Website = 'YouProtest.net';
	static $lang = 'fr';
	static $pays = 'FR';
	static $databases = array(
			
		'default'  => array(
			'host'     => 'localhost',
			'database' => 'ypp',
			'login'    => 'root',
			'password' => ''
			)
		);


	


}

//Prefixe
Router::prefix(Conf::$adminPrefix,'admin');

//Connect
Router::connect('','manifs/index'); //RAcine du site ( à laisser en premiere regle !)
//Router::connect('banane','banane/posts/index');

Router::connect('m/:slug-:id','manifs/create/id:([0-9]+)/slug:([a-zA-Z0-9\-]+)');
Router::connect('create','manifs/create');
//Router::connect('blog/:slug-:id','posts/view/id:([0-9]+)/slug:([a-zA-Z0-9\-]+)');
//Router::connect('blog/*','posts/*');

//Router::connect('pages/:slug-:id','pages/view/id:([0-9]+)/slug:([a-zA-Z0-9\-]+)');

?>