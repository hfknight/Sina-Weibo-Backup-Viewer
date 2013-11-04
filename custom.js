$( "form" ).submit(function( event ) {
  event.preventDefault();
  //dataString = $(form).serialize();

});

/* replace url text with links */
function replaceURLWithHTMLLinks(text) {
    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return text.replace(exp,"<a target='_blank' href='$1'>$1</a>"); 
}

/* format date in json string to be more friendly readable*/
function parseJsonDate(jsonDate) {
  return new Date(Date.parse(jsonDate)).toLocaleString();
}

/*  load comment list by weibo tweet id */
function getCommentListById(bindObj, tweetId) {
      var reqUrl = "get_comments_by_id.php";
      var request = $.ajax({
        url: reqUrl,
        type: "post",
        data: { weibo_tweet_id: tweetId },
        dataType: "json",
        beforeSend: function(){
          // $("<span class=\"comments_loading_bar\"><div class=\"progress progress-striped active\"><div class=\"progress-bar\"  role=\"progressbar\" aria-valuenow=\"100\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 100%\"><span class=\"sr-only\">loading</span></div></div></span>").insertAfter($(bindObj));
          $(bindObj).find("em").hide();
          $(bindObj).append("<em class=\"comment_loading\">获取评论中</em>");
          //$(".weibo_rs").prepend(sp_spinner);
          //$(".weibo_rs").html("Loading Weibo ...");
        },
        complete: function(){
          // $(".comments_loading_bar").remove();
          $(bindObj).find(".comment_loading").remove();
          $(bindObj).find("em").show();          
        }
      });    

      request.done(function(data) {
        var comments = data['comments'];
        var $comment_group = $("<ul>", {"class": "list-group"});
        $.each( comments, function(i, item) {
          var $comment_list = $("<li>", {"class": "list-group-item"});
         
          var comment_created = parseJsonDate(item['created_at']);
          var comment_text = item['text'];
          var comment_user = item['user'];
          var clhtml = "<a href=\"http://weibo.com/" + comment_user['profile_url'] + "\"><span class=\"comment_username\">" + comment_user['screen_name']+ "</span></a> : ";
          // var reply_comment;
          // if( reply_comment = item['reply_comment']) {
            // clhtml += "<a href=\"http://weibo.com/" + reply_comment['user']['profile_url'] + "\"><span class=\"comment_username\">@" + reply_comment['user']['screen_name']+ "</span></a> : ";
          // }
          clhtml += "<span class=\"comment_text\">" + comment_text + "</span>";
          clhtml += "<span class=\"comment_date label label-primary\">" + comment_created + "</span>";
          
          $comment_list.html(clhtml);
          $comment_group.append($comment_list);
        });
        
        $(bindObj) .popover({ "content" : "<ul class=\"list-group\">" + $comment_group.html() + "</ul>", "placement" : "right", "html" : true, "trigger" : "click" }).popover("show");
      });
      request.fail(function(jqXHR, textStatus) { // ajax call failed
        //alert( "Request failed: " + textStatus );
        return ("Request Failed: " + textStatus);
      });     
}

/* generate html codes for copy */
function generateHtmltoClipboard(arrObj){

  var wbListHtml = "<h2>我的微博 " + $('#ap_start_date').val() + " - " + $('#ap_end_date').val() + "</h2>";
  $.each(arrObj, function(i, item){
  wbListHtml += "<li>";
    wbListHtml += "<p>" + parseJsonDate(item['created_at']) + "</p>";
    wbListHtml += replaceURLWithHTMLLinks(item['text']);
  if(item['thumbnail_pic']) {
      wbListHtml += "<p><a href=\"" +item['original_pic'] +"\" target=\"_blank\"><img src='" + item['thumbnail_pic'] +"'></a></p>";
  }
  
   if(item['retweeted_status']) {
   
      var rt_tweet = item['retweeted_status'];
      var rt_user = rt_tweet['user'];
      var rt_user_name = (typeof rt_user === "undefined" ? "" : rt_user['name']);   
      wbListHtml += "//RT@" + rt_user_name + ": " + replaceURLWithHTMLLinks(rt_tweet['text']);
      if(rt_tweet['thumbnail_pic']) {
      wbListHtml += "<p><a href=\"" +rt_tweet['original_pic'] +"\" target=\"_blank\"><img src='" + rt_tweet['thumbnail_pic'] +"'></a></p>";
      }
   }
     wbListHtml += "</li>";
  });  
  
    var outputHtml = "<ol>" + wbListHtml + "</ol>";
    // outputHtml = outputHtml.replace(/([uE000-uF8FF]|uD83C[uDF00-uDFFF]|uD83D[uDC00-uDDFF])/g, '');
    $('#copy_board textarea.generated').val(outputHtml);
    
    ZeroClipboard.setDefaults( { moviePath: 'http://weibo.hfknight.com/js/ZeroClipboard.swf' } );
    //create client
    var clip = new ZeroClipboard($("#copy_action"));
    
    clip.on( 'dataRequested', function (client, args) {
      client.setText(outputHtml);
    });    
    clip.on( 'complete', function ( client, args ) {
      //alert("Copied text to clipboard: " + args.text );
      $("#copy_action").button('complete');
    } );
}

/* load weibo tweets */
(function($) {
  $.fn.buildeGetWB = function() {
    return $(this).click(function(){
    
      $('#copy_action').button('reset');
      var reqUrl = "get_weibo_tweets_php.php";
      var request = $.ajax({
        url: reqUrl,
        type: "post",
        data: { start_day: $("#ap_start_date").val(), end_day: $("#ap_end_date").val() },
        dataType: "json",
        beforeSend: function(){
          //var sp_spinner = $("<div>", {"id": "sp_spinner", "class": "sp_loading_spinner", "text" : "Loading Weibo ..."});
          //$(".weibo_rs").prepend(sp_spinner);
          $(".weibo_rs").html("<span class=\"comments_loading_bar\"><div class=\"progress progress-striped active\"><div class=\"progress-bar\"  role=\"progressbar\" aria-valuenow=\"100\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 100%\"><span class=\"sr-only\">loading</span></div></div></span>");
          $("#gen_to_clipboard").addClass("hide");
        },
        complete: function(){
          //$("#sp_spinner").remove();
          
        }
      });    

      request.done(function(data) { // ajax call succeeded
        var serializedData = JSON.stringify(data);
        localStorage.setItem('wbarr', serializedData); //set local storage
        $(".weibo_rs").html("<h4>共获取" + data.length + "条微博。</h4>");

        $.each( data, function(i, item) {
          // console.log(i);
          // console.log(item);
          var $sWbItem = $("<div>", {"class": "wb_tweet well well-lg"});
          var $sWbItem_body = $("<div>", {"id": item['idstr'], "class": "wb_tweet_content"});
          var $sWbItem_tag = $("<div>", { "class": "wb_tweet_tags" });
          $sWbItem_body.html(replaceURLWithHTMLLinks(item['text']));
          var $sWbItem_created_date = $("<div>", {"class": "wb_date label label-default"});
          $sWbItem_created_date.prepend("<span class=\"icon glyphicon glyphicon-time\"></span>");
          $sWbItem_created_date.append(parseJsonDate(item['created_at']));
          $sWbItem.append($sWbItem_body);
          $sWbItem_tag.append($sWbItem_created_date);
          
          var $reposts_cnt = $("<div>", {"class": "wb_reposts label label-info"});
          $reposts_cnt.prepend("<span class=\"icon glyphicon glyphicon-retweet\"></span>");
          $reposts_cnt.append("<em>转发: " + item['reposts_count'] + "</em>");
          $sWbItem_tag.append($reposts_cnt);
          
          var $comments_cnt;
          $comments_cnt = $("<div>", {"class": "wb_comments label label-info"});
          if( parseInt(item['comments_count']) > 0 ) {
          
            // $comments_cnt = $("<button>", {"type": "button", "class": "wb_comments btn btn-info btn-xs"});
            $comments_cnt.addClass("view_comments");
            $comments_cnt.on("click", function(){
              var el = $(this);
              el.unbind("click");
              getCommentListById(this, item['idstr']);
            });

          } 
          $comments_cnt.prepend("<span class=\"icon glyphicon glyphicon-comment\"></span>");
          
          $comments_cnt.append("<em>评论: " + item['comments_count'] + "</em>");
          $sWbItem_tag.append($comments_cnt);
          
          $sWbItem.append($sWbItem_tag);
          
          if(item['pic_urls'] && (item['pic_urls']).length > 1) {
            $.each(item['pic_urls'], function(i, s_url){
              var $sWbItem_image_wrapper= $("<div>", {"class": "wb_tweet_img"});            
              $sWbItem_image_wrapper.append("<img src='" + s_url['thumbnail_pic'] +"'>");
              $sWbItem.append($sWbItem_image_wrapper);
            });          
          } else if(item['original_pic']) {
            var $sWbItem_image_wrapper= $("<div>", {"class": "wb_tweet_img"});
            var $sWbItem_image =  $("<a>", {"href": "#" + item['idstr'] + "_orig_img", "data-toggle" : "modal", "data-backdrop": true});
            $sWbItem_image.append("<img src='" + item['thumbnail_pic'] +"'>");
            
            $sWbItem_image_wrapper.append($sWbItem_image);
            var lightbox = "<div id=\"" + item['idstr'] + "_orig_img\" class=\"modal fade\"  tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"OriginalImageLabel\"  aria-hidden=\"true\"><div class=\"modal-dialog\"><div class=\"modal-content\"><div class=\"modal-header\"><button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button><h4 class=\"modal-title\">原图</h4></div><div class=\"modal-body\"><img src=\"" + item['original_pic'] + "\"></div></div></div></div>";
            $sWbItem.append($sWbItem_image_wrapper);
            $sWbItem.append(lightbox);
            
            // $sWbItem_image.find('.modal-body').on('show', function(){
              // $(this).css({width: 'auto', height: 'auto', 'max-height': '100%'});
            // });
          }

          if(item['retweeted_status'])
          {
            var rt_tweet = item['retweeted_status'];
            var rt_user = rt_tweet['user'];
            
            var rt_user_name = (typeof rt_user === "undefined" ? "" : rt_user['name']);
            var rt_user_url = (typeof rt_user === "undefined" ? "" : rt_user['profile_url']);
            //$rt_text = makeLinks($rt_tweet['text']);
            //$rt_created_by = date('Y-m-d H:i:s', strtotime($rt_tweet['created_at'])); 

            var $sWbItem_retweet = $("<div>", {"class": "wb_tweet_rt well well-lg"});
            var $sWbItem_retweet_body = $("<div>", {"class": "wb_rt_text"});
            $sWbItem_retweet_body.html(replaceURLWithHTMLLinks(rt_tweet['text']));
            $sWbItem_retweet.append($sWbItem_retweet_body);
            var $sWbItem_retweet_tags = $("<div>", {"class": "wb_rt_tags"});
            var tags_html = "<span class=\"wb_rt_user\"><a class=\"label label-primary\" target=\"_blank\" href=\"http://www.weibo.com/" + rt_user_url + "\"><span class=\"icon glyphicon glyphicon-user\"></span>"  + rt_user_name + "</a></span>";
            tags_html += "<div class=\"wb_rt_date wb_date label label-success\"><span class=\"icon glyphicon glyphicon-time\"></span>" + parseJsonDate(rt_tweet['created_at']) + "</div>";
            $sWbItem_retweet_tags.html(tags_html);
            $sWbItem_retweet.append($sWbItem_retweet_tags);          
            
            var rt_pic;
            
            if(rt_tweet['pic_urls'] && (rt_tweet['pic_urls']).length > 1) {
              $.each(rt_tweet['pic_urls'], function(i, s_url){
                 var $sWRtItem_image_wrapper= $("<div>", {"class": "wb_rt_img"});      
                $sWRtItem_image_wrapper.append("<img src='" + s_url['thumbnail_pic'] +"'>");
                $sWbItem_retweet.append($sWRtItem_image_wrapper);
              });          
            } else if( rt_pic = rt_tweet['original_pic'])
            {
              var $sWRtItem_image_wrapper= $("<div>", {"class": "wb_rt_img"});
              var $sWRtItem_image =  $("<a>", {"href": "#" + rt_tweet['idstr'] + "_rt_orig_img", "data-toggle" : "modal", "data-backdrop": true});
              $sWRtItem_image.append("<img src='" + rt_tweet['thumbnail_pic'] +"'>");
              
              $sWRtItem_image_wrapper.append($sWRtItem_image);
              var lightbox = "<div id=\"" + rt_tweet['idstr'] + "_rt_orig_img\" class=\"modal fade\"  tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"OriginalImageLabel\"  aria-hidden=\"true\"><div class=\"modal-dialog\"><div class=\"modal-content\"><div class=\"modal-header\"><button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button><h4 class=\"modal-title\">原图</h4></div><div class=\"modal-body\"><img src=\"" + rt_tweet['original_pic'] + "\"></div></div></div></div>";
              $sWbItem_retweet.append($sWRtItem_image_wrapper);
              $sWbItem_retweet.append(lightbox);
              
              // $sWRtItem_image.find('.modal-dialog').on('show', function(){
                // $(this).css({width: 'auto', height: 'auto', 'max-height': '100%'});
              // });              
              
              
            }
            $sWbItem.append($sWbItem_retweet);
          }          
          
          $(".weibo_rs").append($sWbItem);
        });
        
        //display generate button
        $("#gen_to_clipboard").removeClass("hide").click(function(e){
          e.preventDefault();
          generateHtmltoClipboard(JSON.parse(localStorage.getItem('wbarr')));
        });
        
        
      });
   
      request.fail(function(jqXHR, textStatus) { // ajax call failed
        alert( "Request failed: " + textStatus );
      });
    });
  }
})(jQuery);