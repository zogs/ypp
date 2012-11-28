
<div class="register">
<?php echo $this->session->flash();?>
	<form class="form-yp" id="form_register" autocomplete="on" action="<?php echo Router::url('users/register'); ?>" method="post" <?php echo (isset($Success))? 'class="hide"':''; ?>>
		<h1>Join the Protest</h1>

		<?php echo $this->Form->input('login','',array('icon'=>'icon-user','required'=>'required','placeholder'=>"Votre pseudo",'data-url'=>Router::url('users/check'))) ?>
		<?php echo $this->Form->input('email',"",array('type'=>'email', 'icon'=>"icon-envelope","required"=>"required","placeholder"=>"Votre email",'data-url'=>Router::url('users/check'))) ?>
		<?php echo $this->Form->input('password','',array('type'=>"password",'icon'=>'icon-lock','required'=>'required','placeholder'=>'Votre mot de passe')) ?>
		<?php echo $this->Form->input('confirm','', array('type'=>'password','icon'=>'icon-lock','required'=>'required','placeholder'=>'Confirmer votre mot de passe')) ?>

		<div class="control-group">
			<div class="controls">
				<?php 
					$dir = "img/bonhom/";
					$extension = ".gif";
					$files = glob($dir.'*'.$extension);

					for ($i=0; $i<count($files); $i++){

						$path = $files[$i];
						$name = str_replace($dir,'',$path);
						$name = str_replace($extension,'',$name);
						
						echo '<label class="label_bonhom">';
						echo '<img src="'.Router::webroot($path).'" alt="'.$name.'">';
						echo '<input type="radio" name="bonhom" value="'.$name.'"';
						if($i==0) echo "checked='checked' ";
						echo ' >';
						echo '</label>';
					}
				?>
			</div>
		</div>		

		<a id="click_me">Ne cliquez pas ici</a>
		<div id="show_me">
			<?php echo $this->Form->input('prenom',"Prénom",array('icon'=>'icon-user')) ?>
			<?php echo $this->Form->input('nom',"Nom",array('icon'=>'icon-user')) ?>
			<div class="control-group">
				<label for="pays" class="control-label">Pays</label>
				<div class="controls">
					<i class="icon-home icon-form"></i>
					<select name="pays">
						<option value=""></option>				
						<?php 
						$pays = $this->request('world','getCountry');
						foreach( $pays as $p):?>
						<option value="<?php echo $p->code; ?>"><?php echo $p->name; ?></option>
						<?php endforeach;?>	
		
					</select>
				</div>
			</div>	
		</div>	
		<div class="actions">
			<?php echo $this->Form->input('token','hidden',array('value'=>$this->session->token())) ;?>			
			<input class="btn btn-large btn-inverse" type="submit" value="C'est parti !" />
			<p>En validant l'inscription j'accepte les conditions d'utilisations</p>
		</div>

	</form>
	
</div>	
<div class="modal-footer">
    <a href="#" class="btn" onclick="$('#myModal').modal('hide');">Close</a>
</div>


<script type="text/javascript">
	

	$('#form_register').submit(function(){

		var data = $(this).serialize();
		var url = $(this).attr('action');

		$.ajax({
		  type: 'POST',
		  url: url,
		  data: data,
		  success: function( data ) {
			    $("#myModal").empty().html(data);			    
			},
		  dataType: 'html'
		});	
			return false;

	});

	
</script>