$(document).ready(function(){
	

	$('a.bubble-top').livequery(function(){

		$(this).tooltip( { delay: { show: 500, hide: 100 }} );
	});
	$('a.bubble-bottom').livequery(function(){

		$(this).tooltip( { placement : 'bottom', delay: { show: 2000, hide: 100 }} );
	});
	


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


	//========= Call Modal Box =====
  	$('a.callModal').livequery('click',function(){
	        
	        var href = $(this).attr('href');
	        callModalBox(href);  	        
	        return false;
	  });
  	//===============================

});



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