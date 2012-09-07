<?php 

    //Fichier qui contient la fonction qui cré le code du commentaire
    require('html.php');



    //If fail
    if(isset($fail)) {
        echo json_encode(array('fail'=>$fail));
        exit();
    }

    //Initialisation
    $html='';

    //If there are comments
    if(!empty($coms)){

        //Create the html 
        $html = show_comment_or_replies($coms,$this->session->user('obj'));


    }
   

    

    //
    $html = utf8_decode($html); // json_encode ne fonctionne qu'avec des donnees utf8
    //$html = htmlentities($html);// json_encode ne fonctionne pas avec les balises html


    echo $html;


?>


