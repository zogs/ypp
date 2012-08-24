<?php

class PagesController extends Controller {


		//===================
		// Permet de rentre une page
		// $param $id id du post dans la bdd

		function view($id){

				//On charge le model
				$this->loadModel('Posts');
				//On utlise la methode findFirst du model
				$page = $this->Posts->findFirst(array(
					'conditions'=> array('id'=>$id,'online'=>1,'type'=>'page') //En envoyant les parametres
					));
				//Si le resultat est vide on dirige sur la page 404
				if(empty($page)){
					$this->e404('Page introuvable');
				}
				//Atttribution de l'objet $page a une variable page
				$this->set('page',$page);

				
		}

		//Permet de recuperer les pages pour le menu
		function getMenu(){

			$this->loadModel('Posts');
			return $this->Posts->find(array(
				'conditions'=> array('online'=>1,'type'=>'page')

				));
		}
}

?>