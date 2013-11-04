
<?php
  session_start();

  include_once( 'config.php' );
  include_once( 'saetv2.ex.class.php' );

  //var_dump($_SESSION['token']);

  function retrieveWBMsg( $start_date, $end_date, $page = 1, $allTwts = array()) {
    $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
    $utl = $c->user_timeline_by_id(NULL, $page, 100, 0, 0, 0, 0, 0);
    $tweets = $utl['statuses'];
    
    //$firstTwt = array_shift($tweets);
    reset($tweets);
    $firstTwt = current($tweets);
    /* var_dump($firstTwt['created_at']); */
    $firstTwt_ts = strtotime($firstTwt['created_at']);	
//    reset($tweets);
    $lastTwt = end($tweets);
    $lastTwt_ts = strtotime($lastTwt['created_at']);

    /* 
      end_date: 06-01 
      start_date: 05-01 
      firstTwt_ts: 07-05
      lastTwt_ts: 04-24
    */
    if( $end_date >= $firstTwt_ts && $start_date <= $lastTwt_ts )
    {
      $incre_array = array_merge($allTwts, $tweets);
      return retrieveWBMsg( $start_date, $end_date, ++$page, array_merge($allTwts, $tweets));
    } else if( $end_date < $firstTwt_ts && $start_date <= $lastTwt_ts )
    {
    // error_log("---------------- 2 ----------------------- / end_date= ". date("jS F, Y", $end_date) . " - firstTwt_ts=" . date("jS F, Y", $firstTwt_ts) . " || start_date=".date("jS F, Y", $start_date) . " / lastTwt_ts=".date("jS F, Y", $lastTwt_ts) );    
      foreach( $tweets as $tweet )
      {
        $created_ts = strtotime($tweet['created_at']);
        if ( $created_ts >= $start_date && $created_ts <= $end_date )
        {
          array_push($allTwts, $tweet);	
        }			
      }	
      return retrieveWBMsg( $start_date, $end_date, ++$page, $allTwts);
      
    } else if ( $end_date < $firstTwt_ts && $start_date > $lastTwt_ts )
    {
      foreach( $tweets as $tweet )
      {
        $created_ts = strtotime($tweet['created_at']);
        if ( $created_ts >= $start_date && $created_ts <= $end_date )
        {
          array_push($allTwts, $tweet);	
        }			
      }
      return $allTwts;		
    } else if ( $end_date >= $firstTwt_ts && $start_date > $lastTwt_ts )
    {
      foreach( $tweets as $tweet )
      {
        $created_ts = strtotime($tweet['created_at']);
        if ( $created_ts >= $start_date && $created_ts <= $end_date )
        {
          array_push($allTwts, $tweet);	
        }			
      }
      return $allTwts;
      
    } else if($end_date < $lastTwt_ts)
    {
      return $allTwts;
    } else if ( $start_date > $firstTwt_ts) 
    {  
      return $allTwts;
    } else {
      return $allTwts;
    }
  }

  function convertToHtml($TwtArray) {
    $tweetshtml = "<div id=\"weibo_tweets_body\">";
    
    if( is_array($TwtArray) ){
    
      $twt_cnt = count($TwtArray);
      $tweetshtml .= "<h2>共获取 " . count($TwtArray) . " 条微博</h2>";

      foreach( $TwtArray as $item ){
        //$tweetshtml .= "<li>" . sprintf("%01.2f", $item['id']) . "<br />" .  $item['text']  . "</li>";
      
        $tweetshtml .= "<div class=\"wb_tweet well well-lg\">";
        $created_ts = strtotime($item['created_at']); 
        $created = date('Y-m-d H:i:s', $created_ts); 
        $tweetshtml .= "<b class=\"wb_tweet_id\">" .  sprintf("%1.0f", $item['id']) . "</b>";
        $tweetshtml .= "<div class=\"wb_tweet_content\">" . makeLinks($item['text']) . "</div>";
        $tweetshtml .= "<div class=\"wb_date label label-default\"><span class=\"icon glyphicon glyphicon-time\"></span>" . $created . "</div>";        
        $ori_image = $item['original_pic'];
        if($item['original_pic'])
        {
          $tweetshtml .= "<div class=\"wb_tweet_img\"><a href='".$ori_image."'> <img data-content='" . $ori_image ."' src='".$item['thumbnail_pic']."'></a></div>";	
        }

        if($item['retweeted_status'])
        {
          $rt_tweet = $item['retweeted_status'];
          $rt_user = $rt_tweet['user'];
          $rt_user_name = $rt_user['name'];
          $rt_user_url = $rt_user['profile_url'];
          $rt_text = makeLinks($rt_tweet['text']);
          $rt_created_by = date('Y-m-d H:i:s', strtotime($rt_tweet['created_at'])); 
          
          $tweetshtml .= "<div class=\"wb_tweet_rt well well-lg\">";
          $tweetshtml .= "<div class=\"wb_rt_text\">" . $rt_text . "</div>";
          
          $tweetshtml .= "<div class=\"wb_rt_tags\">";
            $tweetshtml .= "<span class=\"wb_rt_user\"><a class=\"label label-primary\" target=\"_blank\" href=\"". $rt_user_url . "\"><span class=\"icon glyphicon glyphicon-user\"></span>" . $rt_user_name . "</a></span>";
            $tweetshtml .= "<div class=\"wb_rt_date wb_date label label-success\"><span class=\"icon glyphicon glyphicon-time\"></span>" . $rt_created_by . "</div>";
          $tweetshtml .= "</div>";
          if($rt_pic = $rt_tweet['original_pic'])
          {
            $tweetshtml .= "<div class=\"wb_rt_img\"><a href='".$rt_pic."'> <img src='".$rt_tweet['thumbnail_pic']."'></a></div>";	
          }
          $tweetshtml .= "</div>";
          
        }
        
        $tweetshtml .= "</div>";
      }

    }
    $tweetshtml	.= "</div>";
    return $tweetshtml;	
  }

  function makeLinks($str) {
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    $urls = array();
    $urlsToReplace = array();
    if(preg_match_all($reg_exUrl, $str, $urls)) {
      $numOfMatches = count($urls[0]);
      $numOfUrlsToReplace = 0;
      for($i=0; $i<$numOfMatches; $i++) {
        $alreadyAdded = false;
        $numOfUrlsToReplace = count($urlsToReplace);
        for($j=0; $j<$numOfUrlsToReplace; $j++) {
          if($urlsToReplace[$j] == $urls[0][$i]) {
            $alreadyAdded = true;
          }
        }
        if(!$alreadyAdded) {
          array_push($urlsToReplace, $urls[0][$i]);
        }
      }
      $numOfUrlsToReplace = count($urlsToReplace);
      for($i=0; $i<$numOfUrlsToReplace; $i++) {
        $str = str_replace($urlsToReplace[$i], "<a href=\"".$urlsToReplace[$i]."\">".$urlsToReplace[$i]."</a> ", $str);
      }
      return $str;
    } else {
      return $str;
    }
  }
  
  if(isset($_POST['start_day']) && isset($_POST['end_day'])){
    $start_day = strtotime($_POST['start_day']);
    //$end_day = strtotime($_POST['end_day']);
    $end_day = strtotime("+1 day", strtotime($_POST['end_day']));
    //$start_day = strtotime("2013-08-01");
    //$end_day = strtotime("2013-08-20");    
    
    $final_array = retrieveWBMsg( $start_day, $end_day, 1, array());
    //$final_html = convertToHtml($final_array);
    //echo  $final_html;
    echo json_encode($final_array);
  }

?>