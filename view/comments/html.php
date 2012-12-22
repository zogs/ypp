 <?php 

    function show_comments($coms,$user,$context,$context_id){

        $html='';
        if(!is_array($coms)) $coms = array($coms);
        foreach ($coms as $com) {            

            if(isset($com->thread )){

                if($com->thread == 'manifNews'){

                    $html .= html_comment( $com , $user);
                }

                if($com->thread == 'joinProtest'){

                    $html .= html_joinProtest( $com , $user);
                }
            }

            else {

                $html.= html_comment($com, $user);

                if($com->haveReplies()){

                    //$html.= show_replies($com,$user,$context,$context_id);
                }
            }
        }

        return $html;
    }


    function show_replies($com,$user,$context,$context_id){
        
        $html = '<div class="replies">';     

        $html .= show_comments($com->replies,$user,$context,$context_id);

        if($user->user_id!=0){

                    $html.= "<form class='formCommentReply' action='".Router::url('comments/reply')."' method='POST'>                
                                <img class='userAvatarCommentForm' src='".Router::webroot($user->getAvatar())."' />
                            ";
                        if($user->user_id!=0){
                        $html .= "<textarea name='content' class='formComment' placeholder='Reply here'></textarea> 
                                    <input class='btn btn-small' type='submit' name='' value='Send'>";
                        }
                        else {
                        $html .= "<textarea disabled='disabled' name='content' placeholder='Log in to comment'></textarea>
                                    <input disabled='disabled' class='btn btn-small' type='submit' name='' value='Send'>";
                        }
                    
                    $html .= "  <input type='hidden' name='context' value='".$context."' />
                                <input type='hidden' name='context_id' value='".$context_id."'/>
                                <input type='hidden' name='type' value='com' />
                                <input type='hidden' name='reply_to' value='".$com->reply_to."' />                            
                                
                            </form>" ;
        }

        $html .= '</div>';  

        return $html;

    }
    

    function html_joinProtest($protester,$cuser){

        ob_start();
        ?>
        <div class="thread post post_info">
            <img class="logo" src="<?php echo Router::webroot($protester->logo)?>" alt="Logo" />
            <div class="content">
                <abbr class="date" title="<?php echo $protester->date;?>"><?php echo $protester->date;?></abbr>
                <div>
                    <span class="user"><?php echo $protester->login ?> </span>
                    protest
                    <a href="<?php echo Router::url('manifs/view/'.$protester->manif_id.'/'.$protester->slug); ?>"><?php echo $protester->nommanif; ?></a>
                </div>
            </div>
        </div>
        <?php

        $html = ob_get_contents();
        ob_end_clean();

        return $html; 
    }

    //Renvoi le html d'un commenaitre
    //@param $com {objet} du com
    //@param $cuser {objet} de l'user
    function html_comment($com,$cuser){
        
        ob_start();
        ?>
            <div class="thread post <?php echo ($com->reply_to!=0)? 'reply':'';?> <?php echo 'type_'.$com->type;?>" id="<?php echo 'com'.$com->id; ?>">  
                <?php if($com->type=='news'): ?> 
                <img class="logo" src="<?php echo Router::webroot($com->logoManif) ?>" alt="image avatar" />             
                <?php else: ?>
                <img class="logo" src="<?php echo Router::webroot($com->user->getAvatar()) ?>" alt="image avatar" />
                <?php endif; ?>
                <div>                    
                    <div class="user"><?php echo $com->user->getLogin();?></div>
                    <abbr class="date" title="<?php echo $com->date;?>"><?php echo $com->date;?></abbr>
                    <?php if($com->type=='news'): ?>
                    <div class="title"><?php echo $com->head; ?></div>                                
                    <?php endif; ?>
                    <div class="content comment_<?php echo $com->type;?>">
                        <?php if ($com->type == 'com'): ?>
                        <div><?php echo str_replace("\\","",$com->content); ?></div>   
                        <?php elseif($com->type == 'slogan'): ?>
                        <div class="slogan"><div><?php echo $com->content; ?></div><img src="<?php echo Router::webroot('img/megaphone/megaphone'.$com->speaker.'.gif'); ?>" alt="" /></div>
                        <?php elseif($com->type == 'img'): ?>
                        <div><?php echo str_replace("\\","",$com->content); ?></div>
                        <?php elseif($com->type == 'video'): ?>
                        <div><?php echo str_replace("\\","",$com->content); ?></div>
                        <?php elseif($com->type == 'url'): ?>
                        <div><?php echo str_replace("\\","",$com->content); ?></div>
                        <?php elseif($com->type == 'news'): ?>                       
                        <div><?php echo str_replace("\\","",$com->content); ?></div> 
                        <?php endif; ?>   
                        <?php if ($cuser): ?>
                        <div class="actions">                                 
                            <div class="btn-group pull-left">
                                <?php if($cuser->user_id!=0): ?>
                                    <a class="btn-vote bubbtop" title="Like this comment" data-url="<?php echo Router::url('comments/vote/'.$com->id); ?>" >                      
                                            <span class="badge badge-info" <?php if ($com->note == 0): ?>style="display:none"<?php endif ?>><?php echo $com->note; ?></span>
                                        Like                         
                                    </a>              
                                    <a class="btn-comment-reply" data-comid="<?php echo $com->id; ?>" href="<?php echo $com->id;?>">Reply</a>                                
                                    <a href="<?php echo Router::url('comments/view/'.$com->id); ?>" target="_blank">Share</a>
                                    <a href="">Alert</a>
                                <?php else: ?>
                                    <span>Log in to reply</span>
                                <?php endif;?>
                            </div>                    
                        </div>
                        <?php endif; ?>
                    </div> 
                </div>             
            </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }


?>