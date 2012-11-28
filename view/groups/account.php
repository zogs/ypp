<div id="groups-register">

	<div class="formulaire">
		
		<?php echo $this->session->flash(); ?>
		
		<form action="<?php echo Router::url('groups/account'); ?>" method="POST" enctype="multipart/form-data">

			<div class="form-block">

				<?php echo $this->Form->input('name','Group name',array('value'=>$group->name,'required'=>'required','placeholder'=>"Nom du groupe",'data-url'=>Router::url('groups/check'))) ?>
				<?php echo $this->Form->input('email',"Contact Email",array('value'=>$group->email,'type'=>'email',"required"=>"required","placeholder"=>"Contact Email",'data-url'=>Router::url('groups/check'))) ?>
				
				<?php if($action=='create'): ?>
				<?php echo $this->Form->input('password','Password',array('type'=>"password",'required'=>'required','placeholder'=>'Password')) ?>
				<?php echo $this->Form->input('confirm','Confirm', array('type'=>'password','required'=>'required','placeholder'=>'Confirm password')) ?>
				<?php endif; ?>
				<?php if($action=='change'): ?>
				<div class="control-group">
					<div class="controls"><a href="">Click here to change password</a></div>
				</div>
				
				<?php endif; ?>


				<?php echo $this->Form->input('group_id','hidden',array('value'=>$group->group_id)); ?>
				<?php echo $this->Form->input('user_id','hidden',array('value'=>$group->user_id)); ?>
				<div class="submit">
					<input class="btn btn-info" type="submit" value="<?php echo $submit_tx; ?>"/>
					<p class="help-inline helper"> By clicking i accept and understand the <a href="">Terms of Use</a></p>
				</div>
			</div>

			<div class="form-block">
				
				<div class="control-group">
					<label for="description" class="control-label">Description</label>
					<div class="controls">
							<script type="text/javascript" src="<?php echo Router::webroot('js/ckeditor/ckeditor.js');?>"></script>
							<script type="text/javascript" src="<?php echo Router::webroot('js/ckeditor/adapters/jquery.js');?>"></script>
							<script type="text/javascript">
								$(document).ready(function(){
									$( '#description').ckeditor();
								});
							</script>
						<textarea name="description" id="description"><?php echo utf8_encode($group->description); ?></textarea>
					</div>
				</div>
				<div class="submit">
					<input class="btn btn-info" type="submit" value="<?php echo $submit_tx; ?>"/>
					
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

				<?php echo $this->Form->input('logo','Logo',array('type'=>'file','info'=>'height & width :150px, max-weight: 100ko','src'=>(!empty($group->logo))? Router::webroot($group->logo):'')); ?>								
				<?php echo $this->Form->input('banner','Banner',array('type'=>'file','info'=>'width :960px, max-weight: 200ko','src'=>(!empty($group->banner))? Router::webroot($group->banner):'')); ?>								

				<div class="submit">
					<?php echo $this->Form->input('token','hidden',array('value'=>$this->session->token())) ;?>
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