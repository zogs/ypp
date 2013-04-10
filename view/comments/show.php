<?php

                    if(!empty($flash)){
                        $this->session->setFlash($flash['message'],$flash['type']);
                        echo $this->session->flash();
                    }
                    ?>

                    <?php             
                    
                    //Form is display
                    if(true == $this->displayFormComment ): ?>
                    <form id="commentForm" action="<?php echo Router::url('comments/add'); ?>" method="POST">                        


                        <?php
                        
                        //Si les commentaires sont authorisé OU si c'est l'admin on affiche le formulaire
                        if (Session::user()->isLog() && $this->allowComment == true): ?>    

                        <img class="userAvatarCommentForm" src="<?php echo Router::url(Session::user()->getAvatar()); ?>" />
                        <div class="commentFormFields">
                            <?php 
                                //Si c'est l'admin , il peut mettre un titre a son commentaire
                                if($this->allowTitle):?>
                                <input type="text" name="title" id="commentTitle" placeholder="<?php echo $this->tx_commentTitle;?>" />
                                <?php endif; ?>

                            <textarea name="content" id="commentTextarea" class="formComment" data-url-preview="<?php echo Router::url('comments/preview'); ?>" placeholder="<?php echo $this->tx_commentTextarea;?>"></textarea>
                            <input type="hidden" name="context" value="<?php echo $context; ?>" />
                            <input type="hidden" name="context_id" value="<?php echo $context_id; ?>" />                 
                            <input type="hidden" name="type" id="type" value='com' /> 
                            <input type="hidden" name="lang" value="<?php echo $this->getLang();?>" />           
                            <input type="hidden" name="media" id="media" value='' /> 
                            <input type="hidden" name="media_url" id="media_url" value='' /> 
                            <div class="btn-group" id="commentTextareaButtons">
                                <input type="submit" id="submitComment" class="btn btn-small" value="Envoyer">
                                <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">              
                                <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu"> 
                                <li><a href="">Proposer un slogan</a></li>                          
                                <li><a href="">Créer une nouvelle discussion</a></li>                
                                </ul>
                            </div>                              
                            <div id="commentPreview"></div>
                        </div>
                        <?php else: ?>
                        <img class="userAvatarCommentForm" src="<?php echo Router::url(Session::user()->getAvatar()); ?>" />
                        <textarea class="formComment" disabled="disabled" name="content"><?php echo $this->tx_commentTextareaDisabled;?></textarea>
                        <?php endif; ?>
                    </form> 

                        <div class="btn-toolbar" style="margin-top:0">  

                            <form id="form_slogan" class="form_toggle" data-url="<?php echo Router::url('comments/add'); ?>" method="POST" >
                                <input type="hidden" name="type" value="slogan"/>
                                <input type="hidden" name="manif_id" value=""/>
                                <div id="slogans_avatar">
                                    <div class="slogan_avatar">
                                        <label>
                                        <img src="<?php echo Router::url('img/megaphone/megaphone1.gif'); ?>" alt=""/><br />
                                            <input type="radio" name="speaker" value="1" id="megaphone1" checked="checked"/>
                                            </label>          
                                    </div>
                                    <div class="slogan_avatar">
                                        <label>
                                        <img src="<?php echo Router::url('img/megaphone/megaphone2.gif'); ?>" alt=""/><br />
                                            <input type="radio" name="speaker" value="2" id="megaphone2" />
                                            </label>          
                                    </div>
                                    <div class="slogan_avatar">
                                        <label>
                                        <img src="<?php echo Router::url('img/megaphone/megaphone3.gif'); ?>" alt=""/><br />
                                            <input type="radio" name="speaker" value="3" id="megaphone3" />
                                            </label>          
                                    </div>
                                    <div class="slogan_avatar">
                                        <label>
                                        <img src="<?php echo Router::url('img/megaphone/megaphone4.gif'); ?>" alt=""/><br />
                                            <input type="radio" name="speaker" value="4" id="megaphone4" />
                                            </label>          
                                    </div>
                                    <div class="slogan_avatar">
                                        <label>
                                        <img src="<?php echo Router::url('img/megaphone/megaphone5.gif'); ?>" alt=""/><br />
                                            <input type="radio" name="speaker" value="5" id="megaphone5" />  
                                            </label>      
                                    </div>                                                                                                                                                                          
                                </div>
                                <input type="text" name="slogan" id="slogan" size="60" maxlength="140" />
                                <button class="btn btn-inverse" id="btn_form_slogan">Poster</button>                  
                                <span class="post_callback"></span>
                            </form> 
                        </div>       

                    <?php endif; ?>
                    
                    <div style="float:left;width:100%; height:0px;"></div>  

                <?php if($this->displayRenderButtons): ?>
                    <div id="tri" class="btn-toolbar">

                        <div class="btn-group pull-right">
                            <a class="btn  btn-mini dropdown-toggle bubble-bottom" title="Type of comments" data-toggle="dropdown" href="#">
                            Type
                            <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="type_com" href="?type=com">Commentaires</a></li>
                            <li><a class="type_com" href="?type=img">Images</a></li>
                            <li><a class="type_com" href="?type=video">Vidéo</a></li>
                            <li><a class="type_com" href="?type=all">Tout</a></li>
                            </ul>
                        </div>
                        <div class="btn-group pull-right">
                            <a class="btn btn-mini dropdown-toggle bubble-bottom" title="Ordering comments" data-toggle="dropdown">
                            Ordre
                            <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="type_com" href="?order=datedesc">+ récent</a></li>
                            <li><a class="type_com" href="?order=dateasc">+ ancien</a></li>
                            <li><a class="type_com" href="?order=notedesc">mieux noté</a></li>
                            <li><a class="type_com" href="?order=noteasc">moins bien noté</a></li>
                            </ul>
                        </div>  
                        <div class="btn-group pull-right">
                            <a class="btn btn-mini bubble-bottom" title="Display new comments" >
                                <i class="icon-repeat"></i>  Actualiser <span class="badge badge-inverse hide" id="badge"></span>
                            </a>
                            <span id="ajaxLoader" style="display:none"><img src="<?php echo Router::webroot('img/ajax-loader.gif');?>" alt="Loading" /></span>
                            <a class="btn btn-mini dropdown-toggle hide" data-toggle="dropdown" href="#">              
                            <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="set_refresh" href="600">Toutes les 10 min</a></li>
                            <li><a class="set_refresh" href="300">Toutes les 5 min</a></li>
                            <li><a class="set_refresh" href="120">Toutes les 2 min</a></li>
                            <li><a class="set_refresh" href="60">Toutes les 1 min</a></li>
                            </ul>
                        </div>      
                    </div>
                <?php endif; ?>

                    <div id="comments" data-start="0" data-comments-url="<?php echo Router::url('comments/index/'.$context.'/'.$context_id); ?>" data-url-count-com="<?php echo Router::url('comments/tcheck/'.$context.'/'.$context_id.'/'); ?>" data-comments-config='<?php echo addslashes(json_encode($this->config));?>'>
                        <?php 
                        // load in ajax 
                        ?>                    
                    </div>
                    <div id="bottomComments">
                        <a  id="showMoreComments" href="" ><span class="icon-arrow-down"></span> <?php echo $this->tx_showMoreComments; ?></a>
                        <div id='loadingComments'><span class="ajaxLoader"></span> <?php echo $this->tx_loadingForComments; ?></div>
                        <div id='noMoreComments'><?php echo $this->tx_endOfComments; ?></div>
                        <div id="noCommentYet"><?php echo $this->tx_noCommentForNow; ?></div>                        
                    </div>

                    <?php if($this->allowReply): ?>
                    <div id="hiddenFormReply">
                         <?php if(Session::user()->isLog()):?>
                         <div class="replies" id="formCommentReply" >
                            <form class="formCommentReply" action="<?php echo Router::url('comments/reply'); ?>" method="POST">                
                                <img class="userAvatarCommentForm" src="<?php echo Router::url(Session::user()->getAvatar()); ?>" />
                                <?php if(Session::user()->isLog()):?>
                                <textarea name="content" class="formComment" placeholder="Reply here"></textarea> 
                                <input class="btn btn-small" type="submit" name="" value="Send">
                                <?php else: ?>
                                 <textarea disabled='disabled' name="content" placeholder="Log in to comment"></textarea> 
                                <input disabled='disabled' class="btn btn-small" type="submit" name="" value="Send">
                                <?php endif;?>
                                <input type="hidden" name="context" value="<?php echo $context; ?>"  />
                                <input type="hidden" name="context_id" value="<?php echo $context_id; ?>"/>
                                <input type="hidden" name="lang" value="<?php echo $this->getLang();?>" />
                                <input type="hidden" name="type" value="com" />                            
                                <input type="hidden" name="reply_to" />                                                       
                            </form>
                         </div>                        
                        <?php endif ;?>
                    </div>
                    <?php endif; ?>
                  