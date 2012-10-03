 <?php 


    //Fonction récursive qui affiche les commentaires avec les réponses
    function show_comment_or_replies($coms,$user){

        $html = '';
        foreach ($coms as $com)
        {   

            //Si c'est un objet , c'est un commentaire donc on affiche un commentaire
            if(is_object($com)){                                                       

                if(!isset($com->thread)){

                    $html .= html_comment( $com, $user);
                }

                if(isset($com->thread) && $com->thread == 'manifNews'){

                    $html .= html_comment( $com , $user);
                }

                if(isset($com->thread) && $com->thread == 'joinProtest'){

                    $html .= html_joinProtest( $com , $user);
                }
                   
            }         
            //Si cest un tableau , c'est les réponses a un commentaire , on ouvre une div replies et on affiche les commentaires dedans
            if(is_array($com)){
                
                $html .= '<div class="replies">';
                $html .= show_comment_or_replies($com,$user);
                $html .= '</div>';
            }        
        }
        return $html; 
    }


    function html_joinProtest($protester,$cuser){

        ob_start();
        ?>
        <div class="thread thread_info">
            <img class="thread_logo" src="<?php echo Router::webroot($protester->logo)?>" alt="Logo" />
            <div class="thread_content">
                <abbr class="comment_date" title="<?php echo $protester->date;?>"><?php echo $protester->date;?></abbr>
                <div>
                    <span class="comment_user"><?php echo $protester->login ?> </span>
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
            <div class="thread comment <?php echo ($com->reply_to!=0)? 'reply':'';?>" id="<?php echo 'com'.$com->id; ?>">  
                <?php if($com->type=='news'): ?> 
                <img class="thread_logo" src="<?php echo Router::webroot($com->logoManif) ?>" alt="image avatar" />             
                <?php else: ?>
                <img class="thread_logo" src="<?php echo Router::webroot($com->avatar) ?>" alt="image avatar" />
                <?php endif; ?>
                <div class="thread_content">
                    <?php if($com->type!='news'): ?>
                    <h6 class="comment_user"><?php echo $com->login;?></h6>
                    <abbr class="comment_date" title="<?php echo $com->date;?>"><?php echo $com->date;?></abbr>
                    <?php else: ?> 
                    <div class="news-head"><?php echo $com->head; ?></div>
                    <abbr class="comment_date" title="<?php echo $com->date;?>"><?php echo $com->date;?></abbr>                     
                    <?php endif; ?>
                    <div class="comment_txt comment_<?php echo $com->type;?>">
                        <?php if ($com->type == 'com'): ?>
                        <div><?php echo str_replace("\\","",$com->content); ?></div>   
                        <?php elseif($com->type == 'slogan'): ?>
                        <div class="content type_slogan"><div><?php echo $com->content; ?></div><img src="<?php echo Router::webroot('img/megaphone/megaphone'.$com->speaker.'.gif'); ?>" alt="" /></div>
                        <?php elseif($com->type == 'img'): ?>
                        <div><?php echo str_replace("\\","",$com->content); ?></div>
                        <?php elseif($com->type == 'video'): ?>
                        <div><?php echo str_replace("\\","",$com->content); ?></div>
                        <?php elseif($com->type == 'url'): ?>
                        <div><?php echo str_replace("\\","",$com->content); ?></div>
                        <?php elseif($com->type == 'news'): ?>                       
                        <div class="content news_comment"><?php echo str_replace("\\","",$com->content); ?></div> 
                        <?php endif; ?>   
                        <?php if ($cuser): ?>
                        <div class="actions">                                 
                            <div class="btn-group pull-left">
                                <a class="btn-vote bubbtop" title="Like this comment" data-url="<?php echo Router::url('comments/vote/'.$com->id); ?>" >                      
                                        <span class="badge badge-info" <?php if ($com->note == 0): ?>style="display:none"<?php endif ?>><?php echo $com->note; ?></span>
                                    Like                         
                                </a>
                                <?php if($com->reply_to==0): ?>
                                <a class="btn-comment-reply bubbtop" title="Reply to this comment" data-comid="<?php echo $com->id; ?>" href="<?php echo $com->id; ?>" >Reply</a>
                                <?php else: ?>
                                <a class="btn-comment-reply bubbtop" title="Reply to this discussion" data-comid="<?php echo $com->id; ?>" href="<?php echo $com->reply_to; ?>" >Reply</a>
                                <?php endif; ?>
                                <?php if($com->reply_to!=0): ?> 
                                <a class="btn-comment-reply" data-comid="<?php echo $com->id; ?>" href="<?php echo $com->id;?>">Reply this comment</a>
                                <?php endif; ?>
                                <a href="<?php echo Router::url('comments/view/'.$com->id); ?>" target="_blank">Share</a>
                                <a href="">Alert</a>
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