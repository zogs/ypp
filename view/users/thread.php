
<div class="thread">
	<div class="left">
		<div class="thread-user">
			<img src="<?php echo Router::webroot($user->avatar);?>" alt="Avatar" />
			<span class='thread-login'><?php echo $user->login;?></span>
		</div>

		<div class="thread-user-protests">
			<span class="thread-left-title">Protests</span>
			<ul>
				<?php
					foreach($protests as $p){

						echo '<li><a href="'.Router::url("manifs/view/".$p->manif_id).'" >'.$p->nommanif.'</a></li>';

					}
				?>
			</ul>
		</div>


	</div>

	<div class="middle">

		<div class="thread-timeline">

			<?php

			//$this->request('comments','show',$user);


			
				require('/../comments/html.php');
				foreach ($thread as $t) {
					

					if(!empty($t['RLOGO'])) $logo = $t['RLOGO'];
					else $logo = 'img/logo_yp.png';

					echo '<div class="thread-unit">';
					echo '<div class="thread-date">'.$t['DATE'].'</div>';
					echo '<div class="thread-logo"><img src="'.Router::webroot($logo).'"></div>';
					echo '<div class="thread-content">';
					echo '<div class="thread-header">'.$t['RNAME'].'</div>';
					

					if($t['TYPE'] == 'COMMENT'){

						//COMMENT HTML
						echo show_comment_or_replies($t['OBJ'],$this->session->user('obj'));
					}

					if($t['TYPE'] == 'NEWS'){

						//THREAD HEADER
						
						//NEWS HTML CODE
						echo show_comment_or_replies($t['OBJ'],$this->session->user('obj'));
					}

					if($t['TYPE'] == 'PROTEST'){

						$o = $t['OBJ'][0];
						$html = '<div class="thread-protest">You protest <a href="'.Router::url('manifs/view/'.$o->manif_id.'/'.$o->slug).'" >'.$o->nommanif.'</a></div>';

						echo $html;
					}

					echo '</div>'; //end thread-content
					echo '</div>'; //end thread-content
				}
			
			?>

		</div>


	</div>

	<div class="right">

	</div>



</div>


