<div class="thread">
	<div class="left">
		<div class="thread-user">
			<img src="<?php echo Router::webroot($user[0]->avatar);?>" alt="Avatar" />
			<span class='thread-login'><?php echo $user[0]->login;?></span>
		</div>

		<ul class="thread-user-protests">
			<?php
				foreach($protests as $p){

					echo '<li><a href="'.Router::url("manifs/view/".$p->manif_id).'" >'.$p->nommanif.'</a></li>';

				}
			?>
		</ul>


	</div>

	<div class="middle">

		<div class="thread-timeline">

			<?php
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


						$html = '<div class="thread-protest">You protest <a href="'.Router::url('manifs/view/'.$o->manif_id).'" >'.$o->nommanif.'</a></div>';

						echo $html;
					}

					echo '</div>'; //end thread-content
					echo '</div>'; //end thread-content
				}

			?>

		</div>


	</div>

	<div class="right">

		<div class="rightbox">
			<strong>Welcome to your news feed !</strong>
			<p>You can see last news about the protests your are in, and some others timed information.</p>
			<p><strong>Stay informed.</strong></p>
		</div>
	</div>



</div>


