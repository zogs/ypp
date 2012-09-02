                    
<div style="position:absolute;left:0;width:100%;">
	<div id="flash"></div>
</div>
<div id="manif">
    <div class="info">
        <header>
            <div class="logo"><?php if($manif->logo): ?><img src="<?php echo Router::webroot($manif->logo); ?>" /><?php else: ?><div class="nologo"></div><?php endif; ?></div>
            <div class="meta">
                <h1><?php echo $manif->nommanif; ?></h1>
                <div class="by">
                    by
                    <a class="user"><?php echo $manif->user; ?></a>
                    <span class="date"><?php echo $manif->date_creation; ?></span>
                </div>
            </div>
            <div class="actions">
                <div class="btn-toolbar">
                    <?php if($this->session->user()): ?>
                    <a class="btn btn-large btn-inverse btn-protest" id="btn-protest-<?php echo $manif->id;?>" data-manif_id="<?php echo $manif->id; ?>" href="<?php echo Router::url('manifs/addUser');?>" <?php if($manif->pid>0) echo 'style="display:none"'; ?>><i class="icon-plus-sign icon-white"></i> <strong>Protest</strong> </a>
                    <button class="btn btn-large btn-red btn-cancel" id="btn-cancel-<?php echo $manif->id;?>" data-manif_id="<?php echo $manif->id; ?>" href="<?php echo Router::url('manifs/removeUser');?>" <?php if($manif->pid==0) echo 'style="display:none"'; ?>> <strong>You Protest!</strong> </button>
                    <?php else: ?>
                    <a class="btn btn-large btn-inverse callModal" href="<?php echo Router::url('users/login');?>" ><i class="icon-user icon-white"></i> <strong>Connexion</strong> </a>
                    <?php endif; ?>
                    <a class="btn btn-large btn-share"><i class="icon-heart"></i> <strong>Partager</strong></a> 
                    <?php if(isset($manif->isadmin)): ?>
                      <a class="btn btn-large btn-info bubble-bottom" href="<?php echo Router::url('manifs/create/'.$manif->id.'/'.$manif->slug); ?>" data-original-title="Admin your protest"><i class="icon-wrench icon-white"></i> <strong>Admin</strong></a>
                    <?php endif;?>        
                </div>
            </div>
        </header>
    </div>


    
    <div class="manifeste"> 
        <div class="description expandable" data-maxlength="500" data-expandtext=" read more..." data-collapsetext=" reduce">
            <?php echo $manif->description ?>
        </div>               
    </div>

    <div class="wall">
        <div class="onglets">
            <ul class="nav nav-tabs" id="ypTab">
                <li><a href="#commentaires" data-toggle="pill">Discussion</a></li>
                <li><a href="#statistics" data-toggle="pill">Statistics</a></li>
                <li><a href="#diffuse" data-toggle="pill">Diffuse</a></li>
            </ul>
            <div class="tab-content">

                <!-- Mur de discussion -->
                <div class="tab-pane active" id="commentaires">

                    <?php $this->request('comments','show',$manif); ?>
                    
                </div>

                <!-- Statistics -->
                <div class="tab-pane" id="statistics">...</div>

                <!-- Diffuse -->
                <div class="tab-pane" id="diffuse">...</div>

            </div>
        </div>    
    </div>
</div>



<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.swfobject2.2.js');?>"></script>
<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.slabtext.min.js');?>"></script>

<script type="text/javascript" src="http://konami-js.googlecode.com/svn/trunk/konami.js"></script>
<script type="text/javascript">
$(document).ready(function(){ 



        nameProtesters = ['Barry White','Mike Jagger','Elvis Presley','Joe Coker','John Lennon','Johny Cash','Jon Baez','Bob Dylan','Bob Marley','Jimmy Hendrix'];
        intervalRoutine = false;


        $('.btn-share').toggle(function(){            
            intervalRoutine = setInterval(addBonhomToManif,Math.floor(Math.random()*1000));
        },
        function(){
            clearInterval(intervalRoutine);   
        });




        /*
        *   Onglets
        */

        $('#ypTab a:first').tab('show');
        $('#ypTab a:first').on('shown', function (e) {
          e.target // activated tab
          e.relatedTarget // previous tab
        })
  








        //end========
        //=====








    //LOL
     hidden1 = new Konami();
    hidden1.pattern = "191686578676913";
    hidden1.code = function() {                       
        document.getElementById('manifflash').HiddenCode1();
        };
    hidden1.load();

    hidden2 = new Konami();
    hidden2.pattern = "191677976798213";
    hidden2.code = function() {                       
        document.getElementById('manifflash').HiddenCode2();
        };
    hidden2.load();

    hidden3 = new Konami();
    hidden3.pattern = "1916582778913";          
    hidden3.code = function() {              
        document.getElementById('manifflash').HiddenCode3();
        };
    hidden3.load();

    hidden4 = new Konami();
    hidden4.pattern = "19179766513";          
    hidden4.code = function() {           
        document.getElementById('manifflash').HiddenCode4();
        };
    hidden4.load();

    hidden5 = new Konami();
    hidden5.pattern = "1918765866913";          
    hidden5.code = function() {              
        document.getElementById('manifflash').HiddenCode5();
        };
    hidden5.load();

    debug1 = new Konami();
    debug1.pattern = "191677985788413";          
    debug1.code = function() { 
        alert('Kcode');             
        document.getElementById('manifflash').debugBonhomCount();
        };
    debug1.load();







	var ScreenWidth = $(window).width();
	var ScreenHeight = $(window).height();

	swfobject.embedSWF("<?php echo Router::webroot('fl/yppp.swf');?>","flash","100%","500px","9.0.0","<?php echo Router::webroot('/fl/expressInstall.swf');?>",
    {
    	screenWidth:ScreenWidth,
    	screenHeight:500,
        manifNumerus:'<?php echo $manif->numerus; ?>',
        manifName:"<?php echo $manif->nommanif; ?>",
        manifId:'<?php echo $manif->id; ?>',
        manifBackgroundColor:"0xEEEEEE",
        userLogin:"Pumtchak",
        userBonhom:'bonhom_2',
        userLogged:'<?php if($this->session->user()) echo "true"; else echo "false";?>',
        userParticipe:'<?php if($manif->pid==0) echo "false"; else echo "true";?>',
        userLang:'fr',
        onlyBonhom:''
        
    },
    {
        quality:"best",
        scale:'showAll',
        salign:'LT',
        wmode:"opaque",
        allowscriptaccess:"always",
        allowfullscreen:"true",
        allownetworking:"all"
    }, {
        id:"manifflash",
        name:"manifflash"
    },callBackSwf
    );
    function callBackSwf(e){
	    if(e.success==false) alert('load failed');	
		else{
					
		}
	}

    

});



    function addUserFromFlash( ){

        alert('add user please');
    }

</script>




