<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<link rel="stylesheet" style="text/css" href="http://www.manifeste.info/css/bootstrap/css/bootstrap.css" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />

	<title><?php echo isset($title_for_layout)?$title_for_layout : 'Administration MVC';?></title>
	
</head>
<body>

	<div class="navbar" style="position:static">
	  <div class="navbar-inner">
	    <div   class="container">
	      <a class="brand" href="<?php echo Router::url('admin/posts/index'); ?>">
	  			Administration en MVC
			</a>
			<form class ="navbar-search pull-left">
			<input type ="text" class="search-query" placeholder="Search">
			</form>

			<ul class="nav">
				<li><a href="<?php echo Router::url('/'); ?>">Voir le site</a></li>
				<li><a href="<?php echo Router::url('admin/posts/index'); ?>">Article</a></li>
				<li><a href="<?php echo Router::url('admin/pages/index'); ?>">Pages</a></li>
                                <li><a href="<?php echo Router::url('users/logout'); ?>">Deconnexion</a></li>
				
			</ul>
		</div>
	  </div>
	</div>

	<div class="container">

		<?php echo $this->session->flash();?>
		<?php echo $content_for_layout;?>
	</div>
</body>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo Router::webroot('js/tinymce/tiny_mce.js'); ?>"></script>
<script type="text/javascript">
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