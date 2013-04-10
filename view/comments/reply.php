<?php

    //Fichier qui contient la fonction qui cré le code du commentaire
    require('html.php');


    //If fail
    if(isset($fail)) {
        echo json_encode(array('fail'=>$fail));
        exit();
    }

    //If there are comments
    if(!empty($coms)){

        //Create the html 
        $html = show_comments($coms,Session::user(),$context,$context_id);
        
        echo json_encode(array('content'=>$html),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT);
    }
    else {

        echo '';
    }

?>