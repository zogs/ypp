<div id="groups">
	<div class="groupBanner"><?php if(isset($group->banner) && !empty($group->banner)) echo '<img src="'.Router::webroot('media/group/banner/'.$group->banner).'" />'; ?></div>
	<div class="groupHead">
		
		<div class="groupName">
			<?php echo $group->name; ?>
			<div class="groupMeta">
				<ul>
					<li>France</li>
					<li>Internet</li>
				</ul>
			</div>	
					
		</div>
		<div class="groupSocial">
			Facebook - Twitter
		</div> 
	</div>
	<div class="groupLogo"><img src="<?php echo Router::webroot('media/group/logo/'.$group->logo);?>" /></div>
	<div class="leftColomn">
		<div class="groupDescription expandable" data-maxlength="500" data-expandtext=" read more..." data-collapsetext=" reduce">
			<?php echo $group->description; ?>

		</div>

		<div>


			<?php $this->request('comments','show',$group); ?>
		</div>
		
	</div>
	<div class="rightColomn">
		<div class="rightBlock">
			<h1>Protests</h1>
			<div class="cont">
				<ul>
					<li>STOPPONS la chasse à la baleine</li>
					<li>SAUVONS le tigre de Sibérie</li>
					<li>CONTRE l'exploitation animale</li>
					<li>INTERDIRE le gavage des oies</li>
				</ul>
			</div>
		</div>
		<div class="rightBlock">
			<h1>événements</h1>
			<div class="cont">
				<ul>
					<li>Mardi 3 Novembre - Chambourcy - Action de protection de la marmotte tachetée</li>
					<li>Samedi 14 Novembre - Saint Denis - Manifestation de soutiens à GreenPeace</li>
				</ul>
			</div>
		</div>
		
	</div>
</div>