<!DOCTYPE html>
<html lang="<?php echo $this->getLang();?>">
<head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />           
        <?php $this->loadCSS();?>
        <?php $this->loadJS();?>

        <title><?php echo isset($title_for_layout)?$title_for_layout : 'Ypp';?></title>
        
</head>
<body data-user_id="<?php echo Session::user()->getID(); ?>">


        <div class="navbar navbar-fixed-top navbar-inverse">
          <div class="navbar-inner">
            <div class="container">
                <a class="brand" href="#">
                <img class="nav-logo" src="<?php echo Router::webroot('img/logo_yp.png'); ?>" />                                
                        </a>
                        <form class ="navbar-search pull-left" action="<?php echo Router::url('manifs/index'); ?>" method="get">
                        <input type ="text" class="search-query nav-search" name="rch" placeholder="Why You Protest?">
                        </form>

                        <ul class="nav">
                                <li><a href="<?php echo Router::url('manifs/index');?>">Protests.</a></li>
                                <li><a href="<?php echo Router::url('create');?>">Create.</a></li>

                                
                        </ul>
                
                        <?php //debug($this);?>

                        <ul class="nav pull-right">

                                <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="flag flag-<?php echo $this->getFlagLang();?>"></i><b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                                <?php 
                                                foreach (Conf::$languageAvailable as $lang => $name) {
                                                        
                                                        echo '<a href="?lang='.$lang.'" ><i class="flag flag-'.$this->getFlagLang($lang).'"></i>'.$name.'</a>';
                                                }

                                                ?>
                                        </ul>
                                </li>

                                
                                <?php if (Session::user()->isLog()): ?>
                                        <li><a href="<?php echo Router::url('users/thread');?>">
                                                        <img class="nav-avatar" src="<?php echo Router::webroot(Session::user()->getAvatar()); ?>" />   
                                                        <span class="nav-login"><?php echo Session::user()->getLogin(); ?></span>
                                        </a></li>
                                        <li class="dropdown">   
                        
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                        <b class="caret"></b>
                                                </a>
                                                <ul class="dropdown-menu">
                                                        <li><a href="<?php echo Router::url('users/logout'); ?>">Déconnexion</a></li>
                                                        <li class="divider"></li>
                                                        <li><a href="<?php echo Router::url('users/account'); ?>">Mon Compte</a></li>
                                                        <li><a href="#">Mes manifs</a></li>                                                     
                                                </ul>
                                        </li>
                                <?php else: ?>

                                        <form class="loginForm" action="<?php echo Router::url('users/login'); ?>" method='post'>
                                                <input type="login" name="login" required="required" placeholder="Login or email" autofocus="autofocus" value="pumtchak"/>
                                                <input type="password" name="password" required="required" placeholder="Password" value="fatboy" />
                                                <input type="hidden" name="token" value="<?php echo Session::token();?>" />
                                                <input type="submit" value="OK" />
                                        </form>
                                        <li><a href="<?php echo Router::url('users/login');?>">Login</a></li>   
                                        <li><a href="<?php echo Router::url('users/register');?>" >Inscription</a></li>


                                <?php endif ?>

                                
                        </ul>
                </div>
          </div>
        </div>    

        <div class="container-fluid mainContainer"> 

            <div class="container-fluid subNavbar">
                <div class="container">

                        <ul class="nav">
                                <li><a href="<?php echo Router::url('admin/super/board');?>">Board</a></li>
                                <li><a href="<?php echo Router::url('admin/super/reports');?>">Reports</a></li>

                                
                        </ul>                        
                 
                </div>          
            </div>
                

            <?php echo $content_for_layout;?>
        </div>


</body>

<script type="text/javascript" src="<?php echo Router::webroot('js/tinymce/tiny_mce.js'); ?>"></script>
 <script type="text/javascript">

        /*===========================================================
                Set security token
        ============================================================*/
        var CSRF_TOKEN = '<?php echo Session::token(); ?>';


        /*===========================================================
                Language of the page
        ============================================================*/
        var Lang = '<?php echo $this->getLang();?>';


        /*===========================================================
                GOOGLE FONTS
        ============================================================*/
      WebFontConfig = {
        google: { families: [ 'Bangers','Squada One','Oswald:300,400,700' ] },      
        fontinactive: function(fontFamily, fontDescription) { /*alert('Font '+fontFamily+' is currently not available'); */}
      };

      (function() {
        var wf = document.createElement('script');
        wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
        wf.type = 'text/javascript';
        wf.async = 'true';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(wf, s);
      })();




        tinyMCE.init({
                // General options
                mode : "specific_textareas",
                editor_selector : "wysiwyg",
                theme : "advanced",
                plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

                // Theme options
                theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : true,

                // Skin options
                skin : "o2k7",
                skin_variant : "silver",

                // Example content CSS (should be your site CSS)
                content_css : "css/example.css",

                // Drop lists for link/image/media/template dialogs
                template_external_list_url : "js/template_list.js",
                external_link_list_url : "js/link_list.js",
                external_image_list_url : "js/image_list.js",
                media_external_list_url : "js/media_list.js",

                // Replace values for the template plugin
                template_replace_values : {
                        username : "Some User",
                        staffid : "991234"
                }
        });
</script>

</html>