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
        $html = utf8_decode(show_comment_or_replies($coms,$this->session->user('obj')) );

        echo $html;
    }
    else {

        echo '';
    }
    



?>


