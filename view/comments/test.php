<html>
<head>
	<title>Test cURL</title>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.livequery.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.swfobject2.2.js');?>"></script>
</head>
<body>
<div class="container" style="width:600px">
	<div class="row">

		<form action="#" method="POST">
			<textarea name="url" id="url" data-url-preview="<?php echo Router::url('comments/preview'); ?>" rows="2" cols="40" placeholder="Speak here"></textarea>
			<input type="hidden" id="commentType" name="type" value='' />
			<input type="hidden" id="commentMedia" name="media" value='' />
			<input type="submit" value="TEST"/>
		</form>
		<div id="preview"></div>
	</div>
	<div class="row" >

<?php 

	$html = 'Bonjour http://www.youtube.com/watch?v=pvc0vuHOkDk\r\n<div class=\"previewMedia\" id=\"preview01\">\r\n		<div class=\"previewMedia-img\">\r\n						<div class=\"previewMedia-totalImage\"><a href=\"\" id=\"prev_thumb\">Previous</a> Total 6 images <a href=\"\" id=\"next_thumb\">Next</a></div>\r\n						<img src=\"http://i.ytimg.com/vi/pvc0vuHOkDk/default.jpg\" alt=\"\" class=\"previewMedia-thumbnail\" data-type=\"video\" data-comid=\"01\" data-url=\"http://www.youtube.com/v/pvc0vuHOkDk?version=3&f=videos&app=youtube_gdata\"><img src=\"http://i.ytimg.com/vi/pvc0vuHOkDk/mqdefault.jpg\" alt=\"\" class=\"previewMedia-thumbnail hide\" data-type=\"video\" data-comid=\"01\" data-url=\"http://www.youtube.com/v/pvc0vuHOkDk?version=3&f=videos&app=youtube_gdata\"><img src=\"http://i.ytimg.com/vi/pvc0vuHOkDk/hqdefault.jpg\" alt=\"\" class=\"previewMedia-thumbnail hide\" data-type=\"video\" data-comid=\"01\" data-url=\"http://www.youtube.com/v/pvc0vuHOkDk?version=3&f=videos&app=youtube_gdata\"><img src=\"http://i.ytimg.com/vi/pvc0vuHOkDk/1.jpg\" alt=\"\" class=\"previewMedia-thumbnail hide\" data-type=\"video\" data-comid=\"01\" data-url=\"http://www.youtube.com/v/pvc0vuHOkDk?version=3&f=videos&app=youtube_gdata\"><img src=\"http://i.ytimg.com/vi/pvc0vuHOkDk/2.jpg\" alt=\"\" class=\"previewMedia-thumbnail hide\" data-type=\"video\" data-comid=\"01\" data-url=\"http://www.youtube.com/v/pvc0vuHOkDk?version=3&f=videos&app=youtube_gdata\"><img src=\"http://i.ytimg.com/vi/pvc0vuHOkDk/3.jpg\" alt=\"\" class=\"previewMedia-thumbnail hide\" data-type=\"video\" data-comid=\"01\" data-url=\"http://www.youtube.com/v/pvc0vuHOkDk?version=3&f=videos&app=youtube_gdata\">			\r\n		</div>\r\n		<div class=\"previewMedia-info\">\r\n			<div class=\"previewMedia-title\"><a href=\"http://www.youtube.com/watch?v=pvc0vuHOkDk\" target=\"_blank\" title=\"[Narkotek] Guigoo - Outta space\">[Narkotek] Guigoo - Outta space</a></div>\r\n			<div class=\"previewMedia-desc\">Guigoo - Outta space [Narkotek Hors Serie 03-(NKTHS03) Vinyl]</div>\r\n			\r\n		</div>\r\n	</div>\r\n	\r\n\r\n\r\n\r\n';


	echo str_replace(array("\\n","\\r"),array("<br />",""),$html);
 ?>
	</div>
</div>


</body>
</html>