<div class="container">
	

	<div class="span12">

		<form action="<?php echo Router::url('admin/super/reports/');?>" method="GET">
			
			<?php echo $this->Form->_select('lang',Conf::$languageAvailable,array('default'=>$this->getLang(),'style'=>"width:auto;padding:0;margin:0")); ?>
			<input type="submit" value='OK'>
		</form>

		<h2>Reported alerts</h2>
		<?php 
		$alerts = $this->request('report','admin_index',array('fr'));		
		?>


		<table class="table table-striped table-condensed table-bordered table-hover">
			<thead>	
			<th>Context</th>
			<th>Authors</th>
			<th>Content</th>
			<th>Action</th>			
			</thead>
			<tbody>
			<?php foreach ($alerts as $key => $alert):?>
				<tr class="<?php

					if($alert->recurrence==1) echo 'info';
					if($alert->recurrence>=2) echo 'warning';
					if($alert->recurrence>=5) echo 'danger';
					if($alert->treated==1) echo 'success';
				?>">
					<td>
						<?php echo $alert->context.'<br><small>(id: '.$alert->content->getID().') <br/>(nb:'.$alert->recurrence.')</small>'; ?>
					</td>
					<td>
						<strong>Author: </strong><?php echo $alert->author->getLinkedLogin(); ?>
						<br/>
						<strong>Reporter: </strong><?php echo $alert->reporter->getLinkedLogin(); ?>
						<br/>
						<strong>Date: </strong><br /><?php echo $alert->date; ?>
					</td>
					<td>
						<p>
							<?php echo '<strong>'.$alert->about.'</strong><i> '.$alert->description.' </i>'; ?>
						</p>						
						<?php echo $alert->content->debugContent(); ?>
					</td>
					<td>
						<p><a class="btn btn-info" href="<?php echo Router::url('admin/report/clear/'.$alert->id);?>">Clear</a></p>
						<p><a class="btn btn-warning" href="<?php echo Router::url('admin/report/moderate/'.$alert->id);?>">Moderate</a></p>
						<p><a class="btn btn-link" href="<?php echo Router::url('admin/report/warnReporters/'.$alert->id);?>">Warning All Reporters</a></p>
						<p><a class="btn btn-link" href="<?php echo Router::url('admin/report/warnReporter/'.$alert->id);?>">Warning Report</a></p>
						<p><a class="btn btn-danger" href="<?php echo Router::url('admin/report/deleteContent/'.$alert->id);?>">Delete</a></p>

					</td>
				</tr>
			<?php endforeach; ?>
				
				
			</tbody>
		</table>
	</div>
</div>