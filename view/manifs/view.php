<article>
    <header>
        <div class="row-fluid band-stripped-left">
            <span class="adaptive-title title-dark">
                <img class="logo" src="<?php echo Router::webroot($manif->getLogo());?>" />
                <span class="title-container">
                    <h1><?php echo $manif->getTitle();?></h1>
                    <div class="undertitle">
                        <div class="author"><?php echo $manif->getCreator()->getLogin();?></div>
                        <div class="date">Since <?php echo Date::datefr($manif->getCreateDate());?></div>
                    </div>
                    <div class="actions">
                       
                        <div class="switchProtest">
                            <input type="checkbox" <?php echo ($manif->isUserProtesting())? 'checked="checked"' : '';?> class="btn-switch-protested" data-protest-id="<?php echo $manif->getID();?>" data-url-protest="<?php echo Router::url('manifs/addUser');?>" data-url-cancel="<?php echo Router::url('manifs/removeUser');?>">
                            <label><i></i></label>
                        </div>

                        <div class="btn-toolbar">
                            <?php if(!Session::user()->isLog()): ?>                    
                            <a class="btn btn-dark btn-large btn-inverse callModal" href="<?php echo Router::url('users/login');?>" ><i class="icon-user icon-white"></i> <strong>Connexion</strong> </a>
                            <?php endif; ?>
                            <a class="btn btn-dark btn-large btn-share"><i class="icon-heart icon-white"></i> Partager</a> 
                            <?php if($manif->isUserAdmin( Session::user()->getID())): ?>
                              <a class="btn btn-dark btn-large btn-info bubble-bottom" href="<?php echo Router::url('manifs/create/'.$manif->getID().'/'.$manif->getSlug()); ?>" data-original-title="Admin your protest"><i class="icon-wrench icon-white"></i> <strong>Admin</strong></a>
                            <?php endif;?>        
                        </div>
                    </div>
                </span>            
            </span>
        </div>
    </header>
   
    <div class="protest-container">
    	<div class="protest-flash" id="protest-flash" data-manif-id="<?php echo $manif->getID();?>">
            <div id="flash">
              
            </div>
        </div>
    </div>


    <div class="container">

        <div class="protest-sheet">

            <?php echo Session::flash() ;?>

            <div class="sections">       

                <section>                    
                    <div class="section sectionActive sectionFixed" style="margin-top:0"> 
                        <a href="#" class="sectionTitle">Description</a>
                        <div class="sectionContent">
                            <div class="description">                                
                                <div class="expandable" data-maxlength="500" data-expandtext=" read more..." data-collapsetext=" reduce"><?php echo $manif->getDescription(); ?></div>               
                            </div>
                            <div class="actions">
                                
                                <?php if(Session::user()->isLog()): ?>
                                    <div class="switchProtest mini">
                                        <input type="checkbox" <?php echo ($manif->isUserProtesting())? 'checked="checked"' : '';?> class="btn-switch-protested" data-protest-id="<?php echo $manif->getID();?>" data-url-protest="<?php echo Router::url('manifs/addUser');?>" data-url-cancel="<?php echo Router::url('manifs/removeUser');?>">
                                        <label><i></i></label>
                                    </div>                              
                                <?php endif; ?>
                                <ul>
                                    <li><a href="">Partager</a></li>
                                    <?php if($manif->isUserAdmin( Session::user()->getID())): ?>
                                    <li><a href="<?php echo Router::url('manifs/create/'.$manif->getID().'/'.$manif->getSlug());?>">Administrer</a></li>
                                    <?php endif; ?>
                                    <?php if(Session::user()->isLog()): ?>
                                    <li><a href="<?php echo Router::url('report/report/protest/'.$manif->getID());?>">Signaler</a></li>
                                    <?php endif; ?>
                                </ul>

                                <ul>
                                    <?php foreach($translationAvailable as $translate):?>
                                    <li><a href="?lang=<?php echo $translate->lang;?>"><i class="flag flag-<?php echo $this->getFlagLang($translate->lang);?>"></i><?php echo Conf::$languageAvailable[$translate->lang];?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="section">
                        <a href="#" class="sectionTitle">Authors</a>
                        <div class="sectionContent">
                            <div id="authors">
                                <div class="author">
                                    <img class="logo fleft" src="<?php echo Router::webroot($manif->creator->getAvatar());?>" alt="">
                                    <a class="login" href="<?php echo Router::url('users/thread/'.$manif->creator->getID());?>"><?php echo $manif->creator->getLogin(); ?></a>
                                    <br/>
                                    <span><?php echo $manif->creator->getFullName(); ?> ( <?php echo $manif->creator->getAge();?> ans )</span>
                                    <br>
                                    <span><?php echo $manif->creator->getFullLocateString(); ?></span>                                    
                                </div>
                            </div>                            
                        </div>
                    </div>
                </section>

                 <section>
                    <div class="section">
                        <a href="#" class="sectionTitle">More protest</a>
                        <div class="sectionContent">
                            <div class="moreProtests">
                                <h3>Similar protests</h3>
                                <ul>
                                    <?php foreach ($similarProtests as $c):?>
                                      <li><a href="<?php echo Router::url('manifs/view/'.$c->getID().'/'.$c->getSlug());?>"><?php echo $c->getTitle(); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="moreProtests">
                                <h3>Same creator</h3>
                                <ul>
                                    <?php foreach ($sameCreatorProtests as $c):?>
                                      <li><a href="<?php echo Router::url('manifs/view/'.$c->getID().'/'.$c->getSlug());?>"><?php echo $c->getTitle(); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>                                
                            </div>
                            <div class="moreProtests">
                                <h3>Random protests</h3>
                                <ul>
                                    <?php foreach ($randomProtests as $c):?>
                                      <li><a href="<?php echo Router::url('manifs/view/'.$c->getID().'/'.$c->getSlug());?>"><?php echo $c->getTitle(); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                 <section>
                    <div class="section">
                        <a href="#" class="sectionTitle" id="menuStat" data-url="<?php echo Router::url('manifs/statistics/'.$manif->getID());?>">Statistics</a>
                        <div class="sectionContent">
                            <div id="statistics"><img src="<?php echo Router::webroot('img/loader.gif');?>" alt="">  Chargement...</div>
                        </div>
                    </div>
                </section>


                <section>
                    <div class="section sectionActive">
                        <a href="#" class="sectionTitle">Wall</a>
                        <div class="sectionContent">
                            <div id="commentaires">
                                <?php 
                    

                                $auth = $this->request('comments','auth',array(array('context'=>'manif','obj'=>$manif)));
                                $params = array('context'=>'manif','context_id'=>$manif->getID(),'displayRenderButtons'=>true,'enableInfiniteScrolling'=>true);
                                $params = array_merge($auth,$params);
                                $this->request('comments','show',array($params)); 


                                ?>
                            </div>
                        </div>
                    </div>
                </section>



            </div>
        </div>

    </div>
    
</article>


<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.swfobject2.2.js');?>"></script>
<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.slabtext.min.js');?>"></script>
<script type="text/javascript" src="<?php echo Router::webroot('js/jqplot/jquery.jqplot.min.js');?>"></script>
<script type="text/javascript" src="<?php echo Router::webroot('js/jqplot/plugins/jqplot.dateAxisRenderer.min.js');?>"></script>
<script type="text/javascript" src="http://konami-js.googlecode.com/svn/trunk/konami.js"></script>
<script type="text/javascript" src="http://localhost:1337/socket.io/socket.io.js"></script>
<script type="text/javascript">


$(document).ready(function(){ 
            



        // Button share 
        intervalRoutine = false;
        $('.btn-share').toggle(function(){            
            intervalRoutine = setInterval(addBonhomToManif,Math.floor(Math.random()*1000));
        },
        function(){
            clearInterval(intervalRoutine);   
        });

        //
        $(".sectionTitle").not(".sectionFixed").toggle(function(){

            $(this).parent('.section').addClass('sectionActive');
        },
        function(){

            $(this).parent('.section').removeClass('sectionActive');
        });


        //Synchornise swithProtestButton
        $('.btn-switch-protested').click(function(){
            
            if($(this).attr('checked')=='checked'){
                $('.btn-switch-protested').attr('checked','checked');
            }
            else {
                $('.btn-switch-protested').removeAttr('checked');
            }
        })


        //Statistics
        $('#menuStat').one('click',function(){

            $.ajax({
                type:'GET',
                url:$(this).attr('data-url'),
                dataType:'html',
                success: function(html){

                    $('#statistics').empty().html(html);
                }
            });
            return false;
        })

        /*
        *   Onglets       
        $('#ypTab a:first').tab('show');
        $('#ypTab a:first').on('shown', function (e) {
          e.target // activated tab
          e.relatedTarget // previous tab
        });
        */



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
        manifNumerus:'<?php echo $manif->getNumerus(); ?>',
        manifName:"<?php echo $manif->getTitle(); ?>",
        manifId:'<?php echo $manif->getID(); ?>',
        manifBackgroundColor:"0xF3F3F3",
        userID:'<?php if(Session::user()->getID()) echo Session::user()->getID(); else echo ""; ?>',
        userLogin:'<?php if(Session::user()->getLogin()) echo Session::user()->getLogin(); else echo ""; ?>',
        userBonhom:'<?php if(Session::user()->getBonhom()) echo Session::user()->getBonhom(); else echo ""; ?>',
        userLogged:'<?php if(Session::user()->isLog()) echo "true"; else echo "false";?>',
        userParticipe:'<?php if($manif->isUserProtesting()) echo "true"; else echo "false";?>',
        userLang:'<?php if(Session::user()->getLang()) echo Session::user()->getLang(); else echo $this->getLang(); ?>',
        onHoverColorBonhom:'<?php echo $manif->onHoverColorBonhom();?>',
        onlyBonhom:'<?php echo $manif->getUniqueBonhom(); ?>',
        perCentColored:'<?php echo $manif->perCentColorBonhom();?>',
        bonhomRaining:'<?php echo $manif->enableBonhomRaining();?>',
        signsProtest:'<?php echo $manif->displaySignsProtest();?>'
        
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




F