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


    function html_joinProtest($manif,$cuser){

        return $manif->nommanif;
    }

    //Renvoi le html d'un commenaitre
    //@param $com {objet} du com
    //@param $cuser {objet} de l'user
    function html_comment($com,$cuser){

        ob_start();
        ?>
            <div class="comment <?php echo ($com->reply_to!=0)? 'reply':'';?>" id="<?php echo 'com'.$com->id; ?>">  
                <?php if($com->type=='news'): ?> 
                <img class="comment_avatar" src="<?php echo Router::webroot($com->logoManif) ?>" alt="User image avatar" />             
                <?php else: ?>
                <img class="comment_avatar" src="<?php echo Router::webroot($com->avatar) ?>" alt="User image avatar" />
                <?php endif; ?>
                <div class="comment_content">
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
                                <a class="btn btn-small btn-vote bubbtop" title="Like this comment" data-url="<?php echo Router::url('comments/vote/'.$com->id); ?>" >                      
                                        <span class="badge badge-info" <?php if ($com->note == 0): ?>style="display:none"<?php endif ?>><?php echo $com->note; ?></span>
                                         <i class="icon-thumbs-up"></i>                          
                                </a>
                                <?php if($com->reply_to==0): ?>
                                <a class="btn btn-small btn-comment-reply bubbtop" title="Reply to this comment" data-comid="<?php echo $com->id; ?>" href="<?php echo $com->id; ?>" >Reply</a>
                                <?php else: ?>
                                <a class="btn btn-small btn-comment-reply bubbtop" title="Reply to this discussion" data-comid="<?php echo $com->id; ?>" href="<?php echo $com->reply_to; ?>" >Reply</a>
                                <?php endif; ?>
                                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu"> 
                                    <?php if($com->reply_to!=0): ?> 
                                    <li><a class="btn-comment-reply" data-comid="<?php echo $com->id; ?>" href="<?php echo $com->id;?>">Reply to this comment</a></li>
                                    <?php endif; ?>
                                    <li><a href="">Share</a></li>
                                    <li><a href="">Alert</a></li>
                                </ul>
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