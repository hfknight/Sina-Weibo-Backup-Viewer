<?php
	session_start();
	
	include_once( 'config.php' );
	include_once( 'saetv2.ex.class.php' );
	
	function retrieveWBMsg( $start_date, $end_date, $page = 1, $allTwts = array()) {
		$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
		$utl = $c->user_timeline_by_id(NULL, $page, 100, 0, 0, 0, 0, 0);
		$tweets = $utl['statuses'];
		
		$firstTwt = array_shift($tweets);
		/* var_dump($firstTwt['created_at']); */
		$firstTwt_ts = strtotime($firstTwt['created_at']);	
		reset($tweets);
		$lastTwt = end($tweets);
		$lastTwt_ts = strtotime($lastTwt['created_at']);
		echo "<br> ---- end_date:" . date('Y-m-d H:i:s', $end_date) . " / firstTwt_ts:" . date('Y-m-d H:i:s', $firstTwt_ts);
		echo "<br> ---- start_date:" . date('Y-m-d H:i:s', $start_date) . " / lastTwt_ts:" . date('Y-m-d H:i:s', $lastTwt_ts);
	
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
		
	$tweetshtml = "<div id=\"weibo_tweets_body\"";	
	if( is_array($TwtArray) ){
		$tweetshtml .= "<h2>" . count($TwtArray) . "</h2>";

		foreach( $TwtArray as $item ){
			$tweetshtml .= "<div class=\"wb_tweet\">";
			$tweetshtml .= "<div class=\"wb_tweet_content\">" . $item['text'] . "</div>";
			$created_ts = strtotime($item['created_at']); 
			$created = date('Y-m-d H:i:s', $created_ts); 
			// $tweetshtml .= "<br><em>WEIBO ID:  " . intval($item['id']) . "</em>";
			$tweetshtml .= "<div class=\"wb_tweet_date\">发文时间： " . $created . "</div>";
			 	$ori_image = $item['original_pic'];
			 	if($item['original_pic'])
			 	{
					$tweetshtml .= "<div class=\"wb_tweet_img\"><a href='".$ori_image."'> <img src='".$item['thumbnail_pic']."'></a></div>";	
			 	}
			 	if($item['retweeted_status'])
			 	{
					$tweetshtml .= "<div class=\"wb_tweet_rt\">" . $item['retweeted_status']['text'] . "</div>";	
			 	}
			 	$tweetshtml .= "</div>";
		}
	
	}
	$tweetshtml	.= "</div>";
	return $tweetshtml;	
	}

?>
