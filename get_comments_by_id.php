
<?php
  session_start();

  include_once( 'config.php' );
  include_once( 'saetv2.ex.class.php' );

  //var_dump($_SESSION['token']);

  if( isset($_POST['weibo_tweet_id']) ){
  
    $wb_tweet_id = $_POST['weibo_tweet_id'];
    $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
    $wbcl = $c->get_comments_by_sid($wb_tweet_id);
    //$wb_comments = $wbcl['comments'];
    echo json_encode($wbcl);
  }
?>