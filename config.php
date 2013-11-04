<?php

header('Content-Type: text/html; charset=UTF-8');

define( "WB_AKEY" , '4134809802' );
define( "WB_SKEY" , 'be2ff2026d522d7aa59b4431007b3a44' );
define( "WB_CALLBACK_URL" , "http://weibo.hfknight.com/callback.php" );
define( "WB_RESULT_URL" , "index.php" );
define( "WB_HOME_URL" , "http://".$_SERVER["HTTP_HOST"]."/index.php" );


/*
header('Content-Type: text/html; charset=UTF-8');

define( "WB_AKEY" , '4134809802' );
define( "WB_SKEY" , 'be2ff2026d522d7aa59b4431007b3a44' );
define( "WB_CALLBACK_URL" , "http://hfknight.com/weibo/callback.php" );
define( "WBBK_PLUGIN_PAGE", "fei_weibo_backup");
define( "WB_RESULT_URL" , 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?page=".WBBK_PLUGIN_PAGE );
define( "WB_HOME_URL" , "index.php" );
*/

?>