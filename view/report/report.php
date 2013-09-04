<div class="container-fluid band-stripped-left">
	<span class="adaptive-title">
		<span class="title-container">
            <h1>REPORT</h1>                                      
        </span> 
	</span>
</div>

<div class="container">

	<?php echo $this->session->flash(); ?>
	
	<div id="report-form">
		<form class="form form-row w50 centered " action="" method="post">
		
		<?php echo $this->Form->input('token','hidden',array('value'=>$this->session->token())); ?>

		<?php echo $this->Form->radio('about','What is it about?',array('violence'=>'Insultes, Haines, violences','racism'=>'Racisme, Discrimination','spam'=>'Spam, pub, fraude'),array()); ?>
		<?php echo $this->Form->input('description','What you think about it ?',array('type'=>'textarea','rows'=>3,'cols'=>30,'style'=>'width:70%')); ?>
		<?php echo $this->Form->radio('lol','Are you mad?',array('yes'=>'yes','no'=>'no'),array('class'=>'control-inline')); ?>

		<?php echo $this->Form->input('REPORT','submit',array('class'=>'btn btn-large btn-inverse','onclick'=>'confirm("Are you sure?")')); ?>

		</fom>
	</div>
</div>

	



