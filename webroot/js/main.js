
/*===========================================================
	JQUERY 
============================================================*/
$(document).ready(function(){
	

	/*===========================================================
		Tooltip bootstrap
	============================================================*/
	$('a.bubble-top').livequery(function(){

		$(this).tooltip( { delay: { show: 500, hide: 100 }} );
	});
	$('a.bubble-bottom').livequery(function(){

		$(this).tooltip( { placement : 'bottom', delay: { show: 2000, hide: 100 }} );
	});
	

	/*===========================================================
		EXPANDABLE
		@param data-maxlenght
		@param data-expandtext
		@param data-collapsetext
	============================================================*/
    $('.expandable').livequery(function(){

    	$(this).expander({
    		slicePoint: $(this).attr('data-maxlength'),
    		expandPrefix: ' ',
    		expandText: $(this).attr('data-expandtext'),
    		userCollapseText: $(this).attr('data-collapsetext'),
    		userCollapsePrefix: ' ',
    	});
    });

    /*===========================================================
        On PROTEST button , add a protester in bdd & SHOW cancel button
    ============================================================*/
    $(".btn-protest").livequery('click',function(){


        var btn      = $(this);
        var url      = btn.attr('href');
        var manif_id = btn.attr('data-manif_id');
        var user_id  = $("body").attr('data-user_id');
                    

        $.ajax({
            type:'GET',
            url: url,
            data: { manif_id : manif_id, user_id:user_id},
            success : function(data){
                if( data.success ){
                    btn.css('display','none');
                    $("#btn-cancel-"+manif_id).css('display','inline-block');
                    $('#numerus'+manif_id).text( parseInt( $('#numerus'+manif_id).text() ) + 1);
                    addBonhomToManif(data.bonhom, data.login);
                }
                else
                    alert( data.error );
            },
            dataType: 'json'
        });
        return false;
    });



	/*===========================================================
		ON CANCEL button, SHOW and CANCEL
	============================================================*/
		$('.btn-cancel').livequery(function(){

			 $(this).hover(function() {
			 	$(this).removeClass('btn-red').addClass('btn-inverse').html('<i class="icon-remove icon-white"></i> Cancel');
			 }, function() {
			 	$(this).removeClass('btn-inverse').addClass('btn-red').html('<i class="icon-user icon-white"></i> You Protest');
			 });

			 $(this).click(function(){

				var btn      = $(this);
				var url      = btn.attr('href');
				var manif_id = btn.attr('data-manif_id');
				var user_id  = $("body").attr('data-user_id');

			 	$.ajax({
			 		type:'GET',
			 		url:url,
			 		data:{ manif_id : manif_id, user_id: user_id},
			 		success : function(data){
			 			if(data.success){
			 				
			 				btn.css('display','none');
				 			$("#btn-protest-"+manif_id).show();
				 			$('#numerus'+manif_id).text( parseInt( $('#numerus'+manif_id).text() ) - 1);			 				
			 			}
			 			else 
			 				alert(data.error);
			 		},
			 		dataType:'json'
			 	});
			 	return false;
			 });
		 }, function() {
		     $(this).unbind('mouseover').unbind('mouseout');
	 	});
	 	//-- 

		
	
    /*===========================================================
    	ON WINDOW LOAD
    ============================================================*/
        $(window).bind('load',function(){

        	/*===========================================================
        		IF COMMENT SYSTEM
        	============================================================*/
            if($("a#refresh_com")){

                Global_showComments_url = $("#refresh_com").attr('href');                  
				Global_showComments_params = {};
		        Global_refreshComments = false;
		        Global_refreshComments_interval = false;
		        Global_tcheckComments_interval = 60;
		        Global_tcheckComments_offset = 0;
		        Global_tcheckComments = setInterval(tcheckcomments,Global_tcheckComments_interval*1000);
		        Global_loadingComments = false;        
		        Global_pageComments = 1;
		        Global_newerCommentId = 0;
		        //show_comments('clear');


		        /*===========================================================
		        	CLICK REFRESH
		        ============================================================*/
		        $("a#refresh_com").on('click',function(){            
		            clean_params('page','order','type','newer','bottom');
		            Global_pageComments = 1;
		            show_comments('clear');
		            return false;
		        });
		        /*===========================================================
		        	CHOOSE TYPE
		        ============================================================*/
		        $("a.type_com").bind('click',function(){
		            $("a.type_com").each(function(){ $(this).removeClass('dropdown_active'); });
		            $(this).addClass('dropdown_active');
		            var param = $(this).attr('href');            
		            construct_params(param);
		            construct_params('?page=1');
		            Global_pageComments=1;
		            show_comments('clear');
		            return false;            
		        });
		        /*===========================================================
		        	SET REFRESH INTERVAL
		        ============================================================*/
		        $("a.set_refresh").bind('click',function(){
		            $("a.set_refresh").each(function(){ $(this).removeClass('dropdown_active'); });
		            $(this).addClass('dropdown_active');
		            var second = $(this).attr('href');
		            setIntervalRefresh(second);
		            return false;
		        });


		        /*===========================================================
		        	HOVER COMMENTS
		        ============================================================*/
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

		        /*===========================================================
		        	SHOW REPLY FORM
		        ============================================================*/
		        $(".btn-comment-reply").livequery('click',function(){

		            var form = $('#formCommentReply');
		            var url = form.attr('data-url');
		            var reply_to = $(this).attr('href');
		            var comment_id = $(this).attr('data-comid');
		            form.find('input[name=reply_to]').val(reply_to);
		            form.appendTo($("#com"+comment_id));      

		            return false;
		        });

		        /*===========================================================
		        	SUBMIT A REPLY
		        ============================================================*/
		        $(".formCommentReply").livequery('submit',function(){

		            var url = $(this).attr('action');
		            var datas = $(this).serialize();
		            var parent_id = $(this).find('input[name=reply_to]').val();            

		            $.ajax({
		                type:'post',
		                url: url,
		                data: datas,
		                success: function( com ){
		                   if(!com.fail){		                    
		                    $("#formCommentReply").appendTo("#hiddenFormReply");
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


		        /*===========================================================	        
		        SHOW COMMENTS
		        @param use params in Global_showComments_params[]
		        @param use $arguments[] , string clear,newer,start
		        ============================================================*/ 
				function show_comments(){

					$("#loadingComments").show();

		            var arg = (arguments[0]) ? arguments[0] : 'clear';

		            clean_params('newer','start'); 

		            if(arg=='new')
		                 construct_params("?newer="+Global_newerCommentId);
		            if(arg=='bottom')
		                construct_params("?start="+Global_newerCommentId);    

					$.ajax({
					  type: 'GET',
					  url: Global_showComments_url,
					  data: arrayParams2string(Global_showComments_params),
					  success: function( html ) 
		              {
		                //console.log( 'count:'+coms.count+'   remain:'+coms.remain+'   total:'+coms.total+'   nbpage:'+coms.nbpage);

		                //Jquery trick to decode html entities
		                //var html = $('<div />').html(coms.content).text();
		                                                                
		                if(arg=='new') {
		                    $("#badge").empty().hide();
		                    $('#comments').prepend(html);
		                    Global_tcheckComments_offset = 0;
		                }                        
		                else if(arg=='bottom') {                           
		                    $('#comments').append(html);                       
		                }
		                else if(!arg || arg=='clear'){
		                    $("#badge").empty().hide();                        
		                    $('#comments').empty().append(html);
		                    Global_tcheckComments_offset = 0; 
		                    Global_newerCommentId = 0; 
		                }

		                //Get id of the first comment
		                var first_id = $(html).first('.comment').attr('id');
		                first_id = first_id.replace('com','');
		                if(Global_newerCommentId==0) Global_newerCommentId = first_id;
		                	//console.log('first_id='+first_id);


		                Global_loadingComments = false;
		                infiniteComment();
		                

	                    $("#loadingComments").hide();                    

		                    
						
					},
					  dataType: 'html'
					});
					return;

				}

				/*===========================================================	        
				INFINITE SCROLL
				if scroll to the bottom of page
				increment page and call show_comments
				==========================================================*/								
		        var infiniteComment = function() {

		            $(window).scroll(function(){
		                
		                var ylastCom = $("#loadingComments").offset();                 
		                if( (ylastCom.top <= parseInt($(window).scrollTop()+$(window).height())  ) && Global_loadingComments===false && $("#numCommentsLeft").html()  > 0 ) 
		                {   

		                    Global_loadingComments = true;
		                    new_page        = Global_pageComments+1;
		                    Global_pageComments   = new_page;
		                    construct_params("?page="+new_page);                    
		                    show_comments('bottom'); 
		                }

		            });
		        }


		        /*===========================================================
		        	CONSTRUCT PARAMS
		        	@param string ?param=value
		        ============================================================*/
				function construct_params(param){
					if(param!=''){
						var p = [];
						if(strpos(param,'?',0)==0){
							param = str_replace('?','',param);
							p = explode('=',param);
							Global_showComments_params[p[0]] = p[1];	
						}
						else alert('href doit commencer par ?');                
						return param;
					}
				}

		        /*===========================================================
		        	CLEAN PARAMS
		        ============================================================*/
		        function clean_params(){
		            for(var key in arguments) {   
		                for(var cle in Global_showComments_params){                    
		                    //console.debug(' key:'+arguments[key]+'    cle:'+cle+'   value:'+Global_showComments_params[cle]);
		                    if(arguments[key]==cle){
		                        Global_showComments_params[cle] = 0;
		                    }                    
		                }
		            }                         
		        }

		        /*===========================================================
		        	??
		        ============================================================*/
		        function arrayParams2string(array){            
		            var str ='';
		            for(key in array){  

		                    str += key+'='+array[key]+'&';
		                    
		            }
		            str = str.substring(0,str.length-1);
		            return str;
		        }

		        /*===========================================================
		        	SET INTERVAL REFRESH
		        ============================================================*/
		        function setIntervalRefresh(second){

		            if(Global_refreshComments!=false) clearInterval(Global_refreshComments);            
		            Global_refreshComments = setInterval( function() { show_comments('new');} ,second*1000);        
		        }
		        /*===========================================================
		        	SET INTERVAL TCHECK
		        ============================================================*/
		        function setIntervalTcheck(second){

		            if(Global_tcheckComments!=undefined) clearInterval(Global_tcheckComments);            
		            Global_tcheckComments = setInterval(tcheckcomments,second*1000);        
		        }

		        /*===========================================================
		        	TCHECK COMMENTS
		        ============================================================*/
		        function tcheckcomments(){

		            
		            var obj = $('#refresh_com');
		            var badge = obj.find('#badge');
		            var url = obj.attr('data-url-count-com');
		            Global_tcheckComments_offset = Number(Global_tcheckComments_offset) + Number(Global_tcheckComments_interval);
		            var second = Global_tcheckComments_offset;

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


		        /*===========================================================
		        	VOTE COMMENT
		        ============================================================*/
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




			    /*===========================================================	        
			    SMART PREVIEW SUBMIT
			    ============================================================*/
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
			    $('#smartTextarea').bind('keyup',function(e){

			        var content = $(this).val();        
			        var previewURL = $(this).attr('data-url-preview');
  
			        if(event.type=='keyup')
			            var pattern = new RegExp("http\:\/\/[a-zA-Z0-9\-\.\_]+\.[a-zA-Z]{2,4}\/?/\\S*\\s*","gi");                                    
			        var matches = pattern.exec(content); 

			       //console.log('event'+event.type+' content='+content);
			       //console.log('match= --'+matches+'--');
			       //console.log('currenturl= --'+CurrentUrlPreview+'--');

			        if(matches!=null && trim(matches[0])!=trim(CurrentUrlPreview)){

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


            }           
        });

	
	/*===========================================================
		FORM AJAX
	============================================================*/
	$('form.form-ajax').livequery('submit',function(){

		var url = $(this).attr('action');
		var params = $(this).serialize();

		$.ajax({
			type : 'POST',
			url : url,
			data : params,
			contentType: 'multipart/form-data',
			success : function( data ){
				$('#myModal').empty().html( data );
			},
			dataType: 'html'
		});
		return false;
	});


	/*===========================================================
		MODAL BOX
	============================================================*/
  	$('a.callModal').livequery('click',function(){
	        
	        var href = $(this).attr('href');
	        callModalBox(href);  	        
	        return false;
	  });
  	//===============================

});


/*===========================================================
	JAVASCRIPT
============================================================*/


/*===========================================================
    ADD a bonhom to the protest
============================================================*/
nameProtesters = ['Barry White','Mike Jagger','Elvis Presley','Joe Coker','John Lennon','Johny Cash','Jon Baez','Bob Dylan','Bob Marley','Jimmy Hendrix'];
function addBonhomToManif(bonhom,name){

    if(bonhom==undefined) bonhom = 'bonhom_'+Math.floor(Math.random()*10);
    if(name==undefined) name = nameProtesters[Math.floor(Math.random()*10)];

    document.getElementById('manifflash').addHimToManif(bonhom,name);
}

/*==============================================================*/
function addUserFromFlash( data ){

    var btn      = $(".btn-protest:first");
    var url      = btn.attr('href');
    var manif_id = btn.attr('data-manif_id');
    var user_id  = $("body").attr('data-user_id');
    
    alert(user_id);
    alert(data.user_id);
    alert(manif_id);
    alert(data.manif_id);

    if(user_id==data.user_id && manif_id==data.manif_id){            

        $.ajax({
            type:'GET',
            url: url,
            data: { manif_id : manif_id, user_id:user_id},
            success : function(data){
                if( data.success ){
                    btn.css('display','none');
                    $("#btn-cancel-"+manif_id).css('display','inline-block');                    
                }
                else
                    alert( data.error );
            },
            dataType: 'json'
        });
    }
}


/*===========================
	MODAL BOX
============================*/

modalBox = $("#myModal");

modalBox.modal({
        backdrop:true,
        keyboard: true,
        show:false
});

	
function callModalBox(href){

	var modal = $("#myModal");
	$.get(href,function(data){ $(modal).empty().html(data)},'html');
	$(modal).modal('show');
}



/*============================
	SELECTION GEOGRAPHIQUE
=============================*/
CC1 = ''; 
ADM1=''; 
ADM2=''; 
ADM3=''; 
ADM4='';
function showRegion(value,region)
{

	$("#"+region).nextAll('select').empty().remove();
	$("#"+region).next('.select2-container').nextAll('.select2-container').empty().remove();

	if(value!='')
	{		
		CC1 = $("#CC1").val();
		if(region=='ADM1') { ADM1 = value; ADM2=''; ADM3=''; ADM4=''; }
		if(region=='ADM2') { ADM2 = value; ADM3 = ''; ADM4 = ''; }
		if(region=='ADM3') { ADM3 = value; ADM4 = ''; }
		if(region=='ADM4') { ADM4 = value; }
		if(region=='city') return false;		

		var url = $('#submit-state').attr('data-url');

		$.ajax({
			type : 'GET',
			url : url,
			data : { parent:value, ADM: region, CC1:CC1, ADM1:ADM1, ADM2:ADM2, ADM3:ADM3, ADM4:ADM4 },
			dataType: 'json',
			success: function(data){
				
				if(trim(data)!='empty'){ 				
					$('#'+region).next('.select2-container').after(data.SelectELEMENT);
					$("#"+data.SelectID).select2();
				}
			}
		});
	}
}

//Function for select2 plugin
function addCountryFlagToSelectState(state) {
	
	return "<img class='flag flag-"+state.id.toLowerCase()+"' />"+state.text;
}

/*============================
	SELECTION CATEGORY
=============================*/
function showCategory(parent,level){

	var url = $('#submit-category').attr('data-url');

	$.ajax({
		type:'GET',
		url:url,
		data: { parent:parent, level:level},
		success: function(data){
			//alert(data);
			if(trim(data)!='empty'){
				$('#cat'+level).empty().remove();
				$('#cat'+(level-1)).after(data);
			}
		}
	});
}



//=============================
//    LOCAL STORAGE
//============================

jQuery(function($){

	$.fn.formBackUp = function(){

		if(!localStorage){
			return false;
		}

		var forms = this;
		var datas = {};
		var ls = false;
		datas.href = window.location.href;

		if(localStorage['formBackUp']){
			ls = JSON.parse(localStorage['formBackUp']);
			if(ls.href = datas.href){
				for( var id in ls){
					if(id != "href"){
						$("#"+id).val(ls[id]);
						datas[id] = ls[id];
					}
				}
			}
		}

		forms.find('input,textarea').keyup(function(){
			datas[$(this).attr('id')] = $(this).val();
			localStorage.setItem('formBackUp',JSON.stringify(datas));
		});

		forms.submit(function(e){
			localStorage.removeItem('formBackUp');
		});
	}

});