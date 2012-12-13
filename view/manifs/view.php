<div style="position:absolute;left:0;top:40px;width:100%;">
	<div id="flash"></div>
</div>
<div id="manif">

    <?php echo $this->session->flash() ;?>

    <div class="info">
        <header>
            <div class="logo"><img src="<?php echo ($manif->logo)? Router::webroot($manif->logo) : Router::webroot('img/logo_yp.png');?>" /></div>
            <div class="meta">
                <h1><?php echo $manif->nommanif; ?></h1>
                <div class="by">                    
                    <span><a class="user">par <strong><?php echo $manif->user; ?></strong></a></span>
                    <span><a class="category"></a></span>
                    <span class="date">Since <?php echo datefr($manif->date_creation); ?></span>
                </div>

                <div class="actions">
                    
                    <div class="switchProtest">
                        <input type="checkbox" <?php echo (!empty($manif->doesUserProtest))? 'checked="checked"' : '';?> class="btn-switch-protested" data-protest="<?php echo $manif->id;?>" data-url-protest="<?php echo Router::url('manifs/addUser');?>" data-url-cancel="<?php echo Router::url('manifs/removeUser');?>">
                        <label><i></i></label>
                    </div>

                    <div class="btn-toolbar">
                        <?php if(!$this->session->user()): ?>                    
                        <a class="btn btn-dark btn-large btn-inverse callModal" href="<?php echo Router::url('users/login');?>" ><i class="icon-user icon-white"></i> <strong>Connexion</strong> </a>
                        <?php endif; ?>
                        <a class="btn btn-dark btn-large btn-share"><i class="icon-heart icon-white"></i> Partager</a> 
                        <?php if(isset($manif->doesUserAdmin)): ?>
                          <a class="btn btn-dark btn-large btn-info bubble-bottom" href="<?php echo Router::url('manifs/create/'.$manif->id.'/'.$manif->slug); ?>" data-original-title="Admin your protest"><i class="icon-wrench icon-white"></i> <strong>Admin</strong></a>
                        <?php endif;?>        
                    </div>
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

                    <?php $this->request('comments','show',array('manif',$manif->id)); ?>
                    
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
        });
  






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
        userID:'<?php if($this->session->user("user_id")) echo $this->session->user("user_id"); else echo ""; ?>',
        userLogin:'<?php if($this->session->user("login")) echo $this->session->user("login"); else echo ""; ?>',
        userBonhom:'<?php if($this->session->user("bonhom")) echo $this->session->user("bonhom"); else echo ""; ?>',
        userLogged:'<?php if($this->session->user()) echo "true"; else echo "false";?>',
        userParticipe:'<?php if(empty($manif->doesUserProtest)) echo "false"; else echo "true";?>',
        userLang:'<?php if($this->session->getLang()) echo $this->session->user(); else echo ""; ?>',
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



    

</script>




