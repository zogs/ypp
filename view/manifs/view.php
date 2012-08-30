<div style="position:absolute;left:0;width:100%;">
	<div id="flash"></div>
</div>
<div id="manif">
    <div class="info">
        <header>
            <div class="logo"><?php if($manif->logo): ?><img src="<?php echo Router::webroot($manif->logo); ?>" /><?php else: ?><div class="nologo"></div><?php endif; ?></div>
            <div class="meta">
                <h1><?php echo $manif->nommanif; ?></h1>
                <div class="by">
                    by
                    <a class="user"><?php echo $manif->user; ?></a>
                    <span class="date"><?php echo $manif->date_creation; ?></span>
                </div>
            </div>
            <div class="actions">
                <div class="btn-toolbar">
                    <?php if($this->session->user()): ?>
                    <a class="btn btn-large btn-inverse btn-protest" id="btn-protest-<?php echo $manif->manif_id;?>" data-manif_id="<?php echo $manif->manif_id; ?>" href="<?php echo Router::url('manifs/addUser');?>" <?php if($manif->pid>0) echo 'style="display:none"'; ?>><i class="icon-plus-sign icon-white"></i> <strong>Protest</strong> </a>
                    <button class="btn btn-large btn-red btn-cancel" id="btn-cancel-<?php echo $manif->manif_id;?>" data-manif_id="<?php echo $manif->manif_id; ?>" href="<?php echo Router::url('manifs/removeUser');?>" <?php if($manif->pid==0) echo 'style="display:none"'; ?>> <strong>You Protest!</strong> </button>
                    <?php else: ?>
                    <a class="btn btn-large btn-inverse callModal" href="<?php echo Router::url('users/login');?>" ><i class="icon-user icon-white"></i> <strong>Connexion</strong> </a>
                    <?php endif; ?>
                    <a class="btn btn-large btn-share"><i class="icon-heart"></i> <strong>Partager</strong></a> 
                    <?php if(isset($manif->isadmin)): ?>
                      <a class="btn btn-large btn-info bubble-bottom" href="<?php echo Router::url('manifs/create/'.$manif->manif_id.'/'.$manif->slug); ?>" data-original-title="Admin your protest"><i class="icon-wrench icon-white"></i> <strong>Admin</strong></a>
                    <?php endif;?>        
                </div>
            </div>
        </header>
    </div>


    
    <div class="manifeste"> 
        <div class="description expandable">
            <?php echo $manif->description ?>
        </div>               
    </div>

    <div class="wall">
        <div class="onglets">
            <ul class="nav nav-tabs" id="ypTab">
                <li><a href="#commentaires" data-toggle="pill">Discussion</a></li>
                <li><a href="#statistics" data-toggle="pill">Statistics</a></li>
                <li><a href="#diffuse" data-toggle="pill">Diffuse</a></li>
            </ul>
            <div class="tab-content">

                <!-- Mur de discussion -->
                <div class="tab-pane active" id="commentaires">

                    <?php

                    if(isset($manif->isadmin)){
                        $this->session->setFlash('Your are <strong>creator</strong> of this protest. You could <strong>spread a News</strong> by filling the title form.','warning');
                        echo $this->session->flash();
                    }
                    ?>

                    <?php if ($this->session->user()): ?>
                    <form id="smartForm" class="form-ajax" action="<?php echo Router::url('comments/add'); ?>" method="POST">
                        <?php if(isset($manif->isadmin)): ?>
                        <input type="text" name="head" id="head" placeholder="Enter a title to your news" />
                        <?php endif; ?>
                        <textarea name="content" id="smartTextarea" data-url-preview="<?php echo Router::url('comments/preview'); ?>" placeholder="Type text / Paste a link here"></textarea>
                        <input type="hidden" name="manif_id" value="<?php echo $manif->manif_id; ?>" />
                        <input type="hidden" name="type" id="type" value='com' />            
                        <input type="hidden" name="media" id="media" value='' /> 
                        <input type="hidden" name="media_url" id="media_url" value='' /> 
                        <div class="btn-group" id="smartSubmitGroup">
                            <a id="smartSubmit" class="btn btn-small">
                                <i class="icon-envelope"></i> Envoyer
                            </a>
                            <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">              
                            <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a href="">Proposer un slogan</a></li>
                            <li><a href="">Créer une nouvelle discussion</a></li>                
                            </ul>
                        </div>           
                        <div id="commentSmartPreview"></div>
                    </form>
                    
                    <div class="btn-toolbar" style="margin-top:0">  

                        <form id="form_slogan" class="form_toggle" data-url="<?php echo Router::url('comments/add'); ?>" method="POST" >
                            <input type="hidden" name="type" value="slogan"/>
                            <input type="hidden" name="manif_id" value="<?php echo $manif->manif_id; ?>"/>
                            <div id="slogans_avatar">
                                <div class="slogan_avatar">
                                    <label>
                                    <img src="<?php echo Router::url('img/megaphone/megaphone1.gif'); ?>" alt=""/><br />
                                        <input type="radio" name="speaker" value="1" id="megaphone1" checked="checked"/>
                                        </label>          
                                </div>
                                <div class="slogan_avatar">
                                    <label>
                                    <img src="<?php echo Router::url('img/megaphone/megaphone2.gif'); ?>" alt=""/><br />
                                        <input type="radio" name="speaker" value="2" id="megaphone2" />
                                        </label>          
                                </div>
                                <div class="slogan_avatar">
                                    <label>
                                    <img src="<?php echo Router::url('img/megaphone/megaphone3.gif'); ?>" alt=""/><br />
                                        <input type="radio" name="speaker" value="3" id="megaphone3" />
                                        </label>          
                                </div>
                                <div class="slogan_avatar">
                                    <label>
                                    <img src="<?php echo Router::url('img/megaphone/megaphone4.gif'); ?>" alt=""/><br />
                                        <input type="radio" name="speaker" value="4" id="megaphone4" />
                                        </label>          
                                </div>
                                <div class="slogan_avatar">
                                    <label>
                                    <img src="<?php echo Router::url('img/megaphone/megaphone5.gif'); ?>" alt=""/><br />
                                        <input type="radio" name="speaker" value="5" id="megaphone5" />  
                                        </label>      
                                </div>                                                                                                                                                                          
                            </div>
                            <input type="text" name="slogan" id="slogan" size="60" maxlength="140" />
                            <button class="btn btn-inverse" id="btn_form_slogan">Poster</button>                  
                            <span class="post_callback"></span>
                        </form> 
                    </div>                
                    <?php endif ?>
                    
                    <div style="float:left;width:100%; height:10px;"></div>  

                    <div class="btn-toolbar">
                                    
                        <div class="btn-group pull-right">
                            <a class="btn  btn-small dropdown-toggle bubble-top" title="Type of comments" data-toggle="dropdown" href="#">
                            Type
                            <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="show_com" href="?type=com">Commentaires</a></li>
                            <li><a class="show_com" href="?type=slogan">Slogans</a></li>
                            <li><a class="show_com" href="?type=img">Images</a></li>
                            <li><a class="show_com" href="?type=video">Vidéo</a></li>
                            <li><a class="show_com" href="?type=all">Tout</a></li>
                            </ul>
                        </div>
                        <div class="btn-group pull-right">
                            <a class="btn btn-small dropdown-toggle bubble-top" title="Ordering comments" data-toggle="dropdown">
                            Ordre
                            <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="show_com" href="?order=datedesc">+ récent</a></li>
                            <li><a class="show_com" href="?order=dateasc">+ ancien</a></li>
                            <li><a class="show_com" href="?order=notedesc">mieux noté</a></li>
                            <li><a class="show_com" href="?order=noteasc">moins bien noté</a></li>
                            </ul>
                        </div>  
                        <div class="btn-group pull-right">
                            <a class="btn btn-small bubble-top" title="Display new comments" href="<?php echo Router::url('comments/view/'.$manif->manif_id); ?>" id="refresh_com" data-url-count-com="<?php echo Router::url('comments/tcheck/'.$manif->manif_id.'/'); ?>">
                                <i class="icon-repeat"></i>  Actualiser <span class="badge badge-inverse hide" id="badge"></span>
                            </a>
                            <a class="btn  btn-small dropdown-toggle" data-toggle="dropdown" href="#">              
                            <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="set_refresh" href="600">Toutes les 10 min</a></li>
                            <li><a class="set_refresh" href="300">Toutes les 5 min</a></li>
                            <li><a class="set_refresh" href="120">Toutes les 2 min</a></li>
                            <li><a class="set_refresh" href="60">Toutes les 1 min</a></li>
                            </ul>
                        </div>      
                    </div>

                    <div id="comments" data-start="0" style="float:left;width:100%;margin-top:10px;">
                        <!-- Requete Ajax -->
                    </div>
                    <div id="reply2Comment">
                        <form id="formReply" class="formReply" action="<?php echo Router::url('comments/reply'); ?>" method="POST">                
                            <textarea name="content" placeholder="Reply here"></textarea>                
                            <input type="hidden" name="manif_id" value="<?php echo $manif->manif_id; ?>"/>
                            <input type="hidden" name="type" value="com" />
                            <input type="hidden" name="reply_to" />
                            <input class="btn btn-small" type="submit" name="" value="Répondre">
                        </form>
                    </div>
                    <div id="bottomComments">
                        <div id="loadingComments">
                        Wait for <span id="numCommentsLeft"></span> comments more
                        </div>
                        <div id="noMoreComments" class="hide">End of comments</div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="tab-pane" id="statistics">...</div>

                <!-- Diffuse -->
                <div class="tab-pane" id="diffuse">...</div>

            </div>
        </div>    
    </div>
</div>



<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.swfobject2.2.js');?>"></script>
<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.slabtext.min.js');?>"></script>
<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.expander.min.js');?>"></script>
<script type="text/javascript" src="http://konami-js.googlecode.com/svn/trunk/konami.js"></script>
<script type="text/javascript">
$(document).ready(function(){ 


        $('div.expandable').expander({
            slicePoint:       500,  // default is 100
            expandPrefix:     ' ', // default is '... '
            expandText:       'Lire la suite', // default is 'read more'
            userCollapseText: 'Réduire',
            userCollapsePrefix: ' ',            
          });

        nameProtesters = ['Barry White','Mike Jagger','Elvis Presley','Joe Coker','John Lennon','Johny Cash','Jon Baez','Bob Dylan','Bob Marley','Jimmy Hendrix'];
        intervalRoutine = false;


        $('.btn-share').toggle(function(){
            intervalRoutine = setInterval(addBonhom,Math.floor(Math.random()*1000));
        },
        function(){
            clearInterval(intervalRoutine);   
        });


        /*
        *   Bouton protest
        */
        $(".btn-protest").livequery('click',function(){


            var btn = $(this);
            var manif_id = $(this).attr('data-manif_id');
            var url = $(this).attr('href');
            var user_id  = $("body").attr('data-user_id');;

            $.ajax({
                type:'GET',
                url: url,
                data: { manif_id : manif_id, user_id : user_id},
                success : function(data){
                    if( data.success ){
                        btn.css('display','none');
                        $("#btn-joined-"+manif_id).css('display','inline-block');
                        addBonhom(data.bonhom, data.login);

                    }
                    else
                        alert( data.error );
                },
                dataType: 'json'
            });
            return false;
        });

        /*
        *   Call Flash method
        */
        function addBonhom(bonhom,name){

            if(bonhom==undefined) bonhom = 'bonhom_'+Math.floor(Math.random()*10);
            if(name==undefined) name = nameProtesters[Math.floor(Math.random()*10)];
            document.getElementById('manifflash').addHimToManif(bonhom,name);
        }


        /*
        *   Onglets
        */

        $('#ypTab a:first').tab('show');
        $('#ypTab a:first').on('shown', function (e) {
          e.target // activated tab
          e.relatedTarget // previous tab
        })
  


        /*
        *   Hover comments
        */

        $(".comment").livequery(function(){ 
            $(this) 
                .hover(function() { 
                    $(this).find('.actions').css('visibility','visible'); 
                }, function() { 
                    $(this).find('.actions').css('visibility','hidden'); 
                }); 
            }, function() {                 
                $(this) 
                    .unbind('mouseover') 
                    .unbind('mouseout'); 
        }); 

        $(".btn-repondre").livequery('click',function(){

            var form = $('#formReply');
            var url = form.attr('data-url');
            var reply_to = $(this).attr('href');
            var comment_id = $(this).attr('data-comid');
            form.find('input[name=reply_to]').val(reply_to);
            form.appendTo($("#com"+comment_id));      

            return false;
        });        

        $(".formReply").livequery('submit',function(){

            var url = $(this).attr('action');
            var datas = $(this).serialize();
            var parent_id = $(this).find('input[name=reply_to]').val();            

            $.ajax({
                type:'post',
                url: url,
                data: datas,
                success: function( com ){

                   if(!com.fail){
                    
                    $("#formReply").appendTo("#reply2Comment");
                    var html = $('<div />').html(com.content).text(); //Jquery trick to decode html entities
                    $("#com"+parent_id).next('.replies').remove();
                    $("#com"+parent_id).replaceWith(html);

                   }
                   else {
                        alert( com.fail );
                   }
                },
                dataType:'json'
                });
            
            return false;


        });


        //Variable globale
		comments_url ='';
		comments_params = {};
        comments_refresh_interval = false;
        comments_tcheck_interval = 60;
        comments_tcheck_offset = 0;
        tcheck = setInterval(tcheckcomments,comments_tcheck_interval*1000);
        refresh = false;
        loading_comment = false;        
        page_comments = 1;
        top_id = 0;



        //Interactions
        $(window).bind('load',function(){
            comments_url = $("#refresh_com").attr('href');    
            show_comments('clear');            
        });
        $("a#refresh_com").on('click',function(){            
            clean_params('page','order','type','newer','bottom');
            page_comments = 1;
            show_comments('clear');
            return false;
        });
        $("a.show_com").bind('click',function(){
            $("a.show_com").each(function(){ $(this).removeClass('dropdown_active'); });
            $(this).addClass('dropdown_active');
            var param = $(this).attr('href');            
            construct_params(param);
            construct_params('?page=1');
            page_comments=1;
            show_comments('clear');
            return false;            
        });
        $("a.set_refresh").bind('click',function(){
            $("a.set_refresh").each(function(){ $(this).removeClass('dropdown_active'); });
            $(this).addClass('dropdown_active');
            var second = $(this).attr('href');
            setIntervalRefresh(second);
            return false;
        });

        //Affiche les commentaires
        //utilise les parametres contenus dans comments_params[]
        //@arg[0] string clear/newer/bottom  
		function show_comments(){

            var arg = (arguments[0]) ? arguments[0] : 'clear';

            clean_params('newer','start'); 

            if(arg=='new')
                 construct_params("?newer="+top_id);
            if(arg=='bottom')
                construct_params("?start="+top_id);    

			$.ajax({
			  type: 'GET',
			  url: comments_url,
			  data: arrayParams2string(comments_params),
			  success: function( coms ) 
              {
                console.log( 'count:'+coms.count+'   remain:'+coms.remain+'   total:'+coms.total+'   nbpage:'+coms.nbpage);
                //Set id of the top comment
                if(top_id==0) top_id = coms.first_id;

                //Jquery trick to decode html entities
                var html = $('<div />').html(coms.content).text(); 

                //Display the number of remaining comments
                $("#numCommentsLeft").empty().html(coms.remain);


                                                                
                if(arg=='new') {
                    $("#badge").empty().hide();
                    $('#comments').prepend(html);
                    comments_tcheck_offset = 0;
                }                        
                else if(arg=='bottom') {                           
                    $('#comments').append(html);                       
                }
                else if(!arg || arg=='clear'){
                    $("#badge").empty().hide();                        
                    $('#comments').empty().append(html);
                    comments_tcheck_offset = 0;  
                }


                loading_comment = false;
                infiniteComment();
                
                if(coms.remain<=0)
                {
                    console.log('count <= 0');
                    $("#loadingComments").hide();
                    $("#noMoreComments").show();                    
                }
                else {
                    $("#loadingComments").show();
                    $("#noMoreComments").hide();   
                }
                    
				
			},
			  dataType: 'json'
			});
			return;

		}

        //Lance le chargement de la suite des commentaires
        //Si le scroll atteint le bas des commentaires
        //On defini la page suivante 
        //Et on appelle la fonction show_comments
        var infiniteComment = function() {

            $(window).scroll(function(){
                
                var ylastCom = $("#loadingComments").offset();                 
                if( (ylastCom.top <= parseInt($(window).scrollTop()+$(window).height())  ) && loading_comment===false && $("#numCommentsLeft").html()  > 0 ) 
                {   

                    loading_comment = true;
                    new_page        = page_comments+1;
                    page_comments   = new_page;
                    construct_params("?page="+new_page);                    
                    show_comments('bottom'); 
                }

            });
        }

        // Construit le tableau des parametres des commentaires a afficher
        //@param : string ?param=value
		function construct_params(param){
			if(param!=''){
				var p = [];
				if(strpos(param,'?',0)==0){
					param = str_replace('?','',param);
					p = explode('=',param);
					comments_params[p[0]] = p[1];	
				}
				else alert('href doit commencer par ?');                
				return param;
			}
		}

        
        function clean_params(){
            for(var key in arguments) {   
                for(var cle in comments_params){                    
                    //console.debug(' key:'+arguments[key]+'    cle:'+cle+'   value:'+comments_params[cle]);
                    if(arguments[key]==cle){
                        comments_params[cle] = 0;
                    }                    
                }
            }                         
        }


        function arrayParams2string(array){            
            var str ='';
            for(key in array){  

                    str += key+'='+array[key]+'&';
                    
            }
            str = str.substring(0,str.length-1);
            return str;
        }

        function setIntervalRefresh(second){

            if(refresh!=false) clearInterval(refresh);            
            refresh = setInterval( function() { show_comments('new');} ,second*1000);        
        }

        function setIntervalTcheck(second){

            if(tcheck!=undefined) clearInterval(tcheck);            
            tcheck = setInterval(tcheckcomments,second*1000);        
        }
        function tcheckcomments(){

            
            var obj = $('#refresh_com');
            var badge = obj.find('#badge');
            var url = obj.attr('data-url-count-com');
            comments_tcheck_offset = Number(comments_tcheck_offset) + Number(comments_tcheck_interval);
            var second = comments_tcheck_offset;

            url += second;

            $.ajax({
                type: 'GET',
                url: url,
                success: function(data){
                    //$('#manifeste').empty().html(data);
                    if(is_numeric(data.count)){
                        if(data.count>0){
                            badge.empty().html(trim(data.count));
                            badge.show();
                        }
                        else {
                            badge.hide();
                        }
                    }
                    else alert(data);

                },
                dataType: 'json'
            });
        }


        $(".btn-vote").livequery('click',function(){ 

            var badge = $(this).find('.badge');
            var id = $(this).attr('data-id');
            var url = $(this).attr('data-url');
                
            $.post(url,{id:id},function(data){ 

                if(is_numeric(data.note)){
                    badge.html(data.note);
                    badge.show();
                }
                else{
                    alert(data.erreur);
                }
            },'json');
        });

        //end========
        //=====



    //===================================================================
    //Soumission du commentaire avec systeme de preview des media
    //===================================================================

    $("#smartSubmit").on('click',function(){

        var form = $("#smartForm");
        var url = form.attr('action');
        var textarea = $("#smartTextarea");
        var text = textarea.val();
        var preview = $("#commentSmartPreview");
        var media = $("input#media");
        var media_url = $('input#media_url');

        if(preview.html()!="") {

            preview.find(".previewMedia-totalImage").remove();
            preview.find(".previewMedia-thumbnail.hide").remove();
            preview.find(".previewMedia-close").remove();
            media.val(preview.html());
            media_url.val(CurrentUrlPreview);
            text.replace(CurrentUrlPreview,'');
            textarea.val(text);            
        }

        if( trim(text) != "") {
            var data = form.serialize();
            $.ajax({type:"POST", data: data, url:url,
                  success: function(data){
                    
                    if(data.id){
                        show_comments();
                        textarea.val('');  
                        preview.empty();                          
                    }   
                    else {
                        alert(data.fail);
                    }                                                                   
                     
                  },                    
                   dataType: 'json'
            });
           } 
            return false;

    });

    $("#smartTextarea").on('focus',function(){ $(this).css('height','80px'); });

    CurrentUrlPreview = '';
    $('#smartTextarea').on('blur keyup',function(){

        var content = $(this).val();        
        var previewURL = $(this).attr('data-url-preview');

        if(event.type=='keyup')
            var pattern = new RegExp("http\:\/\/[a-zA-Z0-9\-\.\_]+\.[a-zA-Z]{2,4}(\/\\S*)?\\s$","g");                   
        if(event.type=='blur')
            var pattern = new RegExp("http\:\/\/[a-zA-Z0-9\-\.\_]+\.[a-zA-Z]{2,4}(\/\\S*)?$","g");                  
        var matches = pattern.exec(content); 


        if(matches!=null && matches[0]!=CurrentUrlPreview){

            $("#commentSmartPreview").empty().html('loading...');

            var url = matches[0];
            CurrentUrlPreview = url;       

            $.ajax({
                type : 'GET',
                url : previewURL,
                data : {url:url},
                success: function( data ){

                    var decoded = $('<div />').html(data.content).text(); //Jquery trick to decode html entities
                    $("#commentSmartPreview").empty().html(decoded);
                    $("input#media").val(data.content);
                    $("input#type").val(data.type);

                },
                dataType : 'json'
            });
            

        }
        if(matches == null) {
            $("#commentSmartPreview").empty();
        }
        

    });

    $(".previewMedia-close").livequery('click',function(){

        $("#commentSmartPreview").empty();
        $("input#media").val('');
        $("input#type").val('com');

    });
        
    $('#next_thumb').livequery("click", function(){
        
        var img = $('#commentSmartPreview .previewMedia-img').find('img:visible');
        var next = img.next('img');
        if(next.length>0) {
            img.addClass('hide');
            next.removeClass('hide');
        }
        return false;
        }); 

    $('#prev_thumb').livequery("click", function(){
        
        var img = $('#commentSmartPreview .previewMedia-img').find('img:visible');
        var prev = img.prev('img');     
        if(prev.length>0){
            prev.removeClass('hide');   
            img.addClass('hide');
        } 
        return false;
        });

    $(".previewMedia-thumbnail").livequery('click',function() {

        var id = $(this).attr('data-comid');
        var url = $(this).attr('data-url');
        var type = $(this).attr('data-type');

        if(type=='video'){

            var place = $(this).parent();
            place.attr("id",Math.floor(Math.random()*100000))
            id = place.attr('id');

            var flashvars = {};
            var params = {};
            var attributes = {};
            swfobject.embedSWF(url, id, "450", "366", "9.0.0","expressInstall.swf", flashvars, params, attributes,callBackSwf);

        }
        if(type=='img'){
            window.open(url,'_newtab');
        }
        if(type=='url'){
            window.open(url,'_newtab');
        }
        
        

    });





    //LOL
     hidden1 = new Konami();
    hidden1.pattern = "191686578676913";
    hidden1.code = function() {                       
        document.getElementById('manifflash').HiddenCode1();
        };
    hidden1.load();

    hidden2 = new Konami();
    hidden2.pattern = "191677976798213";
    hidden2.code = function() {                       
        document.getElementById('manifflash').HiddenCode2();
        };
    hidden2.load();

    hidden3 = new Konami();
    hidden3.pattern = "1916582778913";          
    hidden3.code = function() {              
        document.getElementById('manifflash').HiddenCode3();
        };
    hidden3.load();

    hidden4 = new Konami();
    hidden4.pattern = "19179766513";          
    hidden4.code = function() {           
        document.getElementById('manifflash').HiddenCode4();
        };
    hidden4.load();

    hidden5 = new Konami();
    hidden5.pattern = "1918765866913";          
    hidden5.code = function() {              
        document.getElementById('manifflash').HiddenCode5();
        };
    hidden5.load();

    debug1 = new Konami();
    debug1.pattern = "191677985788413";          
    debug1.code = function() { 
        alert('Kcode');             
        document.getElementById('manifflash').debugBonhomCount();
        };
    debug1.load();







	var ScreenWidth = $(window).width();
	var ScreenHeight = $(window).height();

	swfobject.embedSWF("<?php echo Router::webroot('fl/yppp.swf');?>","flash","100%","500px","9.0.0","<?php echo Router::webroot('/fl/expressInstall.swf');?>",
    {
    	screenWidth:ScreenWidth,
    	screenHeight:500,
        manifNumerus:'<?php echo $manif->numerus; ?>',
        manifName:"<?php echo $manif->nommanif; ?>",
        manifId:'<?php echo $manif->manif_id; ?>',
        manifBackgroundColor:"0xEEEEEE",
        userLogin:"Pumtchak",
        userBonhom:'bonhom_2',
        userLogged:'<?php if($this->session->user()) echo "true"; else echo "false";?>',
        userParticipe:'<?php if($manif->pid==0) echo "false"; else echo "true";?>',
        userLang:'fr',
        onlyBonhom:''
        
    },
    {
        quality:"best",
        scale:'showAll',
        salign:'LT',
        wmode:"opaque",
        allowscriptaccess:"always",
        allowfullscreen:"true",
        allownetworking:"all"
    }, {
        id:"manifflash",
        name:"manifflash"
    },callBackSwf
    );
    function callBackSwf(e){
	    if(e.success==false) alert('load failed');	
		else{
					
		}
	}

    

});



    function addUserFromFlash( ){

        alert('add user please');
    }

</script>




