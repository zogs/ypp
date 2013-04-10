<div class="container">
	<?php echo Session::flash(); ?>
	<div id="thread">
		<div class="span3">
			<div class="thread-user">
				<img src="<?php echo Router::webroot($user->getAvatar());?>" alt="Avatar" />
				<span class='thread-login'><?php echo $user->login;?></span>
			</div>
			
			<div class="extrude">
				<div class="extrude-label"><span>My Protests</span></div>
				<ul class="extrude-list">
					<?php

					if(!empty($protests)){
						foreach($protests as $p){

							echo '<li><a href="'.Router::url("manifs/view/".$p->manif_id).'" >'.$p->nommanif.'</a></li>';

						}
					}
					else {
						echo '<li><a href="'.Router::url("manifs/create").'">Want to create a protest ?</a></li>';
					}
					?>
				</ul>

				<div class="extrude-label"><span>Protest for</span></div>
				<ul class="extrude-list">
					<?php

					if(!empty($participations)){
						foreach($participations as $p){

							echo '<li><a href="'.Router::url("manifs/view/".$p->manif_id).'" >'.$p->nommanif.'</a></li>';

						}
					} else {

						echo '<li>No protest yet</li>';						
					}
					?>
				</ul>
			</div>
			
		</div>

		<div class="span8">
			<div class="thread-timeline-header alert alert-info">You will receive here <strong>News</strong> and <strong>Info</strong> about protest you are in</div>
			<div class="thread-timeline">

				<?php

				$auth = $this->request('comments','auth',array(array('context'=>'user','obj'=>$user)));
				$params = array('context'=>'user','context_id'=>$user->user_id,'displayRenderButtons'=>true,'enableInfiniteScrolling'=>true);			
				$params = array_merge($auth,$params);
				
				echo $this->request('comments','show',array($params));
				?>

			</div>
		</div>
	</div>	
</div>



