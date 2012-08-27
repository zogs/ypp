<?php /*
<div class="modal-header">
    <a class="close" data-dismiss="modal">x</a>
    <h1>Log In</h1>
</div>

<form class="form-yp" id="form_login" action="<?php echo Router::url('users/login'); ?>" method='post'>
<div class="modal-body">

		
		<?php echo $this->Form->input('login','Identifiant',array('required'=>'required','placeholder'=>'Pseudo ou E-mail','icon'=>'icon-user')); ?>
		<?php echo $this->Form->input('password','Mot de passe',array('type'=>'password','required'=>'required','placeholder'=>'Mot de passe','icon'=>'icon-lock')); ?>		
</div>

<div class="modal-footer">
	<input type="submit" class="btn btn-large btn-inverse" value="Se connecter"/>
    <a href="#" class="btn btn-large" onclick="$('#myModal').modal('hide');">Close</a>
</div>
</form>	
<script type="text/javascript">
	
	modalBox = $("#myModal");

	<?php if($this->session->user('login')): ?>
	modalBox.modal('hide').on('hidden', function () { location.reload(); });
	<?php endif; ?>


	$('#form_login').submit(function(){

		var login = $("#inputlogin").val();
		var pass = $("#inputpassword").val();
		var url = $(this).attr('action');

	$.ajax({
	  type: 'POST',
	  url: url,
	  data: {login:login, password:pass},
	  success: function( data ) {
		    modalBox.empty().html(data);
		},
	  dataType: 'html'
	});

		return false;

	});
</script>
*/ ?>