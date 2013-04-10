<?php

class Routes {

	public function __construct(){

		//Prefixe
		Router::prefix(Conf::$adminPrefix,'admin');

		//Connect
		Router::connect('','manifs/index'); //RAcine du site ( à laisser en premiere regle !)
		Router::connect('banane','banane/super/board');

		Router::connect('m/:slug-:id','manifs/create/id:([0-9]+)/slug:([a-zA-Z0-9\-]+)');
		Router::connect('create','manifs/create');
		Router::connect('groups/create','groups/account');
		//Router::connect('blog/:slug-:id','posts/view/id:([0-9]+)/slug:([a-zA-Z0-9\-]+)');
		//Router::connect('blog/*','posts/*');

		//Router::connect('pages/:slug-:id','pages/view/id:([0-9]+)/slug:([a-zA-Z0-9\-]+)');
	}
}

?>