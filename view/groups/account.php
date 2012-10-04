<div id="groups-register">

	<div class="formulaire">
		
		<?php echo $this->session->flash(); ?>
		<?php debug($group);?>
		<form action="<?php echo Router::url('groups/register'); ?>" method="POST" enctype="multipart/form-data">

			<div class="form-block">

				<?php echo $this->Form->input('name','Group name',array('required'=>'required','placeholder'=>"Nom du groupe",'data-url'=>Router::url('groups/check'))) ?>
				<?php echo $this->Form->input('mail',"Contact Email",array('type'=>'email',"required"=>"required","placeholder"=>"Contact Email",'data-url'=>Router::url('groups/check'))) ?>
				<?php echo $this->Form->input('password','Password',array('type'=>"password",'required'=>'required','placeholder'=>'Password')) ?>
				<?php echo $this->Form->input('confirm','Confirm', array('type'=>'password','required'=>'required','placeholder'=>'Confirm password')) ?>
				<div class="submit">
					<input class="btn btn-info" type="submit" value="<?php echo $submit_tx; ?>"/>
					<p class="help-inline helper"> By clicking i accept and understand the <a href="">Terms of Use</a></p>
				</div>
			</div>

			<div class="form-block">

				<div class="control-group">
					<label for="cat2" class="control-label">Category</label>
					<p class='label-helper'>Choose a category or a sub-category</p>
					<div class="controls">
						<input type="hidden" id="submit-category" data-url="<?php echo Router::url('manifs/getCategory'); ?>">
						<?php echo $this->Form->generateSelect('cat2','btn',$cats2,'id','name',$group->cat2,'onchange="showCategory(this.value,3)"'); ?>
						<?php if($group->cat3): ?>
						<?php echo $this->Form->generateSelect('cat3','btn',$cats3,'id','name',$group->cat3,''); ?>
						<?php endif; ?>
					</div>					
				</div>


				<div class="control-group">
					<label for="" class="control-label">Localisation</label>
					<p class='label-helper'>Locate your protest at country/state/city level</p>
					<div class="controls">
						<input type="hidden" id="submit-state" data-url="<?php echo Router::url('world/getStates'); ?>">
						<?php 


							foreach($states as $key=>$array){	
								
								if(!empty($array)){
									if($key!='city')							
										echo $this->Form->generateSelect($key,'btn geo-select',$array,'code','name',$group->$key,'onchange="showRegion(this.value,\''.$key.'\')"');
									else
										echo $this->Form->generateSelect($key,'btn geo-select',$array,'code','name',$group->$key,'');
								}
							}
						 ?>						 
					</div>					
				</div>

				<?php echo $this->Form->input('logo','Logo',array('type'=>'file','info'=>'height & width :150px, max-weight: 100ko')); ?>								
				<?php echo $this->Form->input('banner','Banner',array('type'=>'file','info'=>'width :960px, max-weight: 200ko')); ?>								

				<div class="submit">
					<input class="btn btn-info" type="submit" value="<?php echo $submit_tx; ?>"/>
				</div>


			</div>
		</form>
	</div>

</div>
<script type="text/javascript">
	
	$(function(){

		//Format select form	     
	    $(".geo-select").select2();
	    $("#CC1").select2({ formatResult: addCountryFlagToSelectState, formatSelection: addCountryFlagToSelectState});

	});
</script>