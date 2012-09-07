<div id="groups">
	<div class="groupBanner"><?php if(isset($group->banner) && !empty($group->banner)) echo '<img src="'.Router::webroot('media/group/banner/'.$group->banner).'" />'; ?></div>
	<div class="groupHead">
		
		<div class="groupName">
			<?php echo $group->name; ?>
			<div class="groupMeta">
				<ul>
					<li><span class="icon-white icon-globe"></span>  <?php 
					echo (isset($group->CC1) && $group->CC1!='')? $group->CC1.' - ' : '';
					echo (isset($group->ADM1) && $group->ADM1!='')? $group->ADM1.' - ' : '';
					echo (isset($group->ADM2) && $group->ADM2!='')? $group->ADM2.' - ' : '';
					echo (isset($group->ADM3) && $group->ADM3!='')? $group->ADM3.' - ' : '';
					echo (isset($group->ADM4) && $group->ADM4!='')? $group->ADM4.' - ' : '';
					echo (isset($group->city) && $group->city!=0)? $group->city.' - ' : '';

					 ?></li>
					 <li><span class="icon-white  icon-briefcase"></span>  <?php echo $group->motsclefs; ?></li>
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
			<?php echo utf8_encode($group->description); ?>

		</div>

		<div>

			<?php 
			// CALL the comment system
			echo $this->request('comments','show',array('group',$group->id)); 
			//
			?>

		</div>
		
	</div>
	<div class="rightColomn">
		<div class="rightBlock">
			<h1>Protests</h1>
			<div class="cont">
				<ul class="styleFirstWords">
					<li>stoppons la chasse à la baleine</li>
					<li>sauvons le tigre de Sibérie</li>
					<li>contre l'exploitation animale</li>
					<li>interdire le gavage des oies</li>
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

<script type="text/javascript">
	
(function($)
{ 
	$.fn.styleFirstWords=function(options)
	{

		var defaults = {
			'class' : 'your_class',
			'nbwords' : 1,			
		}
		var parameters = $.extend(defaults, options);

	   return this.each(function()
	   {
	   			//If the element is a list UL or OL , then call the plugin for each child
			   	if($(this).get(0).tagName.toLowerCase()=='ul' || $(this).get(0).tagName.toLowerCase()=='ol'){

					$(this).children().styleFirstWords(options);						
				}
				else {
					//get the text 
					var str = $(this).text();
					//split the word
					str = str.split(' ');
					//open close tags
					var openSpan = '<span class="'+parameters.class+'">';
					var closeSpan = '</span>';
					//add open tag on the first element of the array
					str.unshift(openSpan); 
					//add close tag a the n position
					str.splice( parameters.nbwords + 1, 0, closeSpan);
					//join the pieces
					str = str.join(' ');
					//append the content into the element
					$(this).html( str );

				   }
		});						   
	};
})(jQuery);


$(".styleFirstWords").styleFirstWords({'class':'uppercase'});


</script>