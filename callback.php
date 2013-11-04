<?php
session_start();

include_once( 'config.php' );
include_once( 'saetv2.ex.class.php' );

$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

// echo "Test return page1: " . $_REQUEST['return_page'] . "<br>";

if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	}
}

if ($token) {
	$_SESSION['token'] = $token;
	setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
	//echo "授权完成. Redirect to Result Page.";
		echo "授权完成. 跳转至我的微博记录页面.";
	    header("refresh:3; url=".WB_RESULT_URL);		


    //header( "refresh:3;url=".$_SESSION[back_url]);
?>

<?php
} else {
?>
授权失败。
<?php
}
?>
