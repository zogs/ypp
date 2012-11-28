<?php 
class Form{


	public $controller;
	private $errors = array();

	public function __construct($controller){

		$this->controller = $controller;

	}

	public function setErrors($errors){

		$this->errors = $errors;
	}

	public function input($name,$label,$options = array()){

		//Gestion des erreurs
		$error = false;
		$classError ='';

		//Si il y a une erreur qui correspond a ce champ
		if(isset($this->errors[$name])){
			$error = $this->errors[$name]; // recup l'erreur
			$classError = 'error'; // défini la class css d'une erreur
		}

		
		if(isset($options['value'])){
			$value = $options['value'];
		}
		elseif(!isset($this->controller->request->data->$name)){ //Si le champ n'a pas de valeur pré-rempli
			$value='';
		}
		else{ //Sinon on recupere la valeur dans les données passé en post dans l'objet request
			$value = $this->controller->request->data->$name;
		}

		if($label=='submit'){

			if(isset($options['class'])) $class = $options['class'];
			else $class = '';
			return '<input type="submit" value="'.$name.'" class="'.$class.'" />';
		}

		if($label=='hidden'){

			return '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
		}

		$html ='<div class="control-group '.$classError.'">';

		if($label!=""){

			$html .= '<label class="control-label" for="input'.$name.'">'.$label.'</label>';
		}

		if(isset($options['info']) && $options['info']!="") {

			$html .= '<p class="label-helper">'.$options['info'].'</p>';
		}


		$html .='<div class="controls">';
			//Attribut html à rajouter en fin de champs
			$attr='';
			foreach ($options as $k => $v) {
				if($k!='type' && $k!='icon'){
					$attr .= " $k=\"$v\"";
				}
			}

			//Icone du bootstrap a afficher avant le champ
			if(isset($options['icon'])){

				$html .= "<i class='".$options['icon']." icon-form'></i>";
			}
		
			if(!isset($options['type'])){
				$html .= '<input type="text" id="input'.$name.'" name="'.$name.'" value="'.$value.'" '.$attr.'>';
			}
			elseif($options['type']=='email'){
				$html .= '<input type="email" id="input'.$name.'" name="'.$name.'" value="'.$value.'" '.$attr.'>';
			}
			elseif($options['type']=='url'){
				$html .= '<input type="url" id="input'.$name.'" name="'.$name.'" value="'.$value.'" '.$attr.'>';
			}
			elseif($options['type']=='textarea'){
				$html .= '<textarea id="input'.$name.'" name="'.$name.'" '.$attr.'>'.$value.'</textarea>';
			}
			elseif($options['type']=='checkbox'){
				$html .= '<input type="hidden" name="'.$name.'" value="0"><input type="checkbox" name="'.$name.'" value="1" '.(!empty($value)? 'checked' : '').' '.$attr.' />';
			}
			elseif($options['type']=='file'){
				$html .= '<input type="file" class="input-file" id="input'.$name.'" name="'.$name.'" '.$attr.'>';

				if( !empty($options['src']) && $options['src']!=''){

					$html .= '<div class="input-file-thumbnail"><img src="'.$options['src'].'" /></div>';
				}
			}
			elseif($options['type']=='password'){
				$html .= '<input type="password" id="input'.$name.'" name="'.$name.'" value="'.$value.'" '.$attr.'>';
			}

			if(isset($options['helper'])){
				$html .= '<p class="help-inline helper">'.$options['helper'].'</p>';
			}

			if($error) {
				$classError = "help-inline";
				$textError = $error;
			}
			else {
				$classError = "help-inline hide";
				$textError = '';
			}
			$html .= '<p class="'.$classError.'">'.$textError.'</p>';
			
			$html .='</div>';
			$html .='</div>';
			
			return $html;
	}

	/** SELECT
	* Genere un code html <select...> avec <option></option...>
	*
	* @param $id l'id du champ select
	* @param $class la class du champ select
	* @param $options tableaux des options : array('code'=>'name')
	* @param $selected valeur dont le champ est selected
	* @param $javascript (facultatif)
	* @return $html code html
	*/ 
	public function Select($id,$title,$class,$options, $selected = null, $javascript = null){

		

		if(!empty($options))
		{

			$html ='<select id="'.$id.'" name="'.$id.'" class="'.$class.'" '.$javascript.' >';
			$html .= '<option value=" ">'.$title.'</options>';
			
			if(is_object($options)) $options = (array) $options;

			foreach ($options as $line) {
					
					if(is_object($line)) $line = (array) $line;

					if(is_array($line)){

						$keys = array_keys($line);
						$value = $line[$keys[0]];
						$name = $line[$keys[1]];
					}
					else {
						$value = $line;
						$name = $line;
					}
				
					if(isset($selected)&&$selected==$value) $if_selected = 'selected="selected"';
					else $if_selected = '';

					$html .='<option value="'.$value.'" '.$if_selected.'>'.utf8_encode($name).'</option>';								
				
			}
			$html .='</select>';
			return $html;
		}
		else return false;


	}

	/** OPTIONS
	* Genere un code html <select...> avec <option></option...>
	*
	* @param $options tableaux associatifs des options : array('code'=>14,'nom'=>Cal)
	* @param $value nom de la valeur propre au tableau
	* @param $name nom de l'option propre au talbeau
	* @param $selected valeur dont le champ est selected
	* @return $html code html ou flase
	*/ 	
	public function Options($options, $value, $name, $selected){

		if(!empty($options))
		{
			$html = '<option value="">- Toutes -</options>';
			$attr = 'selected="selected"';
			if(is_object($options)) $options = get_objet_vars($options);

			foreach ($options as $line) {
				if(is_object($line)) {				
					$html .='<option value="'.$line->$value.'" '.($line->$value === $selected ? $attr:'').'>'.utf8_encode($line->$name).'</option>';								
				}				
				elseif(is_array($line))	{
					if($line[$value] == $selected) $attr='selected="selected';		
					$html .= '<option value='.$line[$value].' '.($line->$value === $selected ? $attr:'').'>'.$line[$name].'</option>';
				}
			}
			return $html;
		}
		else return false;
	}

	public function SelectYear( $id, $title, $class, $start = null, $end = null, $selected = null, $javascript = null){

		if(isset($start)) $start = $start;
		else $start = 2006;

		if(isset($end)) $end = $end;
		else $end = 1950;

		$years = array();

		if($start<$end) {
			for( $i=$start; $i<=$end; $i++){

				$years[$i] = $i;
			}
		}
		elseif($start>$end) {
			for( $i=$start; $i>=$end; $i--){

				$years[$i] = $i;
			}
		}
		return $this->Select($id,$title,$class,$years,$selected);

	}


}

 ?>