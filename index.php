<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="您可以查看您过去发布的微博，支持按日期查询的功能。并且可以导出HTML代码方便复制粘贴到本地或者博客等其他地方。Welcome to use Sina Weibo HistoryTool, You can search your past tweets by period of time.">
    <meta name="keywords" content="新浪微博, 备份, 工具, 归档, 历史记录, 时间查询, sina, weibo, tool, weibo history">
    <meta name="author" content="Fei Hu">
    <link rel="shortcut icon" href="../../assets/ico/favicon.png">

    <title>Fei.Think - 新浪微博查看备份工具</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">

    <!-- Custom styles for this template -->
    <link rel="stylesheet" type="text/css" media="all" href="style.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
    <link href="http://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

  
<?php
  session_start();
  include_once( 'config.php' );
  include_once( 'saetv2.ex.class.php' );
?>
<!--

 	<?=$user_message['screen_name']?>,您好！
	<h2 align="left">发送新微博</h2>
	<form action="" >
		<input type="text" name="text" style="width:300px" />
		<input type="submit" />
	</form>
-->

  
  

  <div class="container">

      <!-- Jumbotron -->
      <div class="jumbotron">
        <h1 class="page_title">新浪微博查看备份工具</h1>
        <?php

          // check token validation
          if(!isset($_SESSION['token'])){
               if(!isset($_REQUEST['code'])){
                  //Get unauthorized request token
                  $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
                  $auth_url = $o->getAuthorizeURL( WB_CALLBACK_URL );      
        ?>
        <div class="container index_auth">
          <p>“记忆里剩下什么， 我们就变成什么样的人。”</p>
          <p><a class="btn btn-danger" href="<?=$auth_url?>">请先连接新浪微博授权此应用读取您的微博。</a></p>
        </div>
        <?php        
              }
            } else {

            if($_POST['ap_hidden'] == 'Y') {
              //start date
              $start_day = strtotime($_POST['ap_start_date']);
              $end_day = strtotime($_POST['ap_end_date']);
            } else {

              $start_day = strtotime("-1 month");	
              $end_day = strtotime("now");
            }
            $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
            $uid_get = $c->get_uid();
            $uid = $uid_get['uid'];  
            $user_message = $c->show_user_by_id( $uid);//根据ID获取用户等基本信息
          ?>
          <a class="profile_avatar" href=".">
            <img alt="<?=$user_message['screen_name']?>" src="<?=$user_message['avatar_large']?>" class="img-circle">
          </a>
          <div class="greeting">
            <h2>欢迎你,  <span><?=$user_message['screen_name']?></span></h2>
            <p class="lead">您可以查看您过去发布的微博， 并且可以导出HTML代码方便复制粘贴到本地或者博客等其他地方。</p>
          </div>          
          <div class="wrap">  
              <form role="form" class="form-inline" name="autopost_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
                  <input type="hidden" name="ap_hidden" value="Y">  
                  
                  <p>请选择你想浏览的微博的起止日期</p>
                  <div class="form-group">

                    <input class="form-control" type="text" id="ap_start_date" name="ap_start_date" size="10" placeholder="Enter Start Date">
                  </div>
                  <div class="form-group">                  

                    <input class="form-control" type="text" id="ap_end_date" name="ap_end_date" size="10"  placeholder="Enter End Date">
                  </div>
                  <div class="form-group">                   
                    <button id="wb_submit" type="submit" class="btn btn-default">开始查询</button>
                    <button id="gen_to_clipboard" type="button"  data-toggle="modal" data-target="#copy_board" class="hide btn btn-success">导出代码</button>
                  </div>
              </form> 
          </div>        
        
      </div>
      
      <!-- Modal -->
      <div class="modal fade" id="copy_board" tabindex="-1" role="dialog" aria-labelledby="Generated Html" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Copy to Clipboard</h4>
            </div>
            <div class="modal-body">
              <textarea id="clipboard_textarea" rows="3" autofocus class="form-control generated">
              </textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button id="copy_action" type="button" class="btn btn-primary"  data-complete-text="已复制到剪贴板">复制代码</button>              
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

      
      <div class="weibo_rs">
        <?php  

          //$final_array = retrieveWBMsg( $start_day, $end_day, 1, array());
          //$final_html = convertToHtml($final_array);
          //echo  $final_html;
        }
        ?>      
      </div>
      <!-- Site footer -->
      <div id="footer">
        <div class="container">
          <p class="text-muted credit">Powered by &copy; <a href="http://fei.io">FEI.IO</a> 2013.</p>
        </div>
      </div>

    </div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/ZeroClipboard.min.js"></script>    
    <script src="js/bootstrap.min.js"></script>
    <script src="custom.js"></script>
    
    <script type="text/javascript">
    
    jQuery(document).ready(function() {
        jQuery('#ap_start_date').datepicker({
            dateFormat : 'yy-mm-dd',
            defaultDate: "-1m"
        }).datepicker('setDate', '-1m');
        jQuery('#ap_end_date').datepicker({
            dateFormat : 'yy-mm-dd'
        }).datepicker('setDate', new Date());

        //var start_date = $("#ap_start_date").val();
        //var end_date = $("#ap_end_date").val();
        $("#wb_submit").buildeGetWB();
        
        // $(".jumbotron").prepend("<a class=\"profile_avatar\" href=\" <?=$user_message['url']?>\"><img alt=\"<?=$user_message['screen_name']?>\" src=\"<?=$user_message['avatar_large']?>\" class=\"img-circle\"></a>");
       // $(".profile_avatar").after("<div class=\"greeting\"><h2>Welcome <?=$user_message['screen_name']?></h2><p>want to read your past SINA weibo?</p></div>");
        // $('.jumbotron > h1').hide();

    });
    
    </script> 
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-44775312-1', 'hfknight.com');
      ga('send', 'pageview');

    </script>  
  </body>
</html>  