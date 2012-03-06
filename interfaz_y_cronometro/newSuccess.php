<html>
<head>
<title>WebSocket</title>
<style>
 html,body{font:normal 0.9em arial,helvetica;}
 #log {width:220px; height:200px; border:1px solid #7F9DB9; overflow:auto; float:left; margin-right: 10px;}
 #logpartner {width:220px; height:200px; border:1px solid #7F9DB9; overflow:auto;}
 #msg {width:330px;}
</style>
<script type="text/javscript" src="/js/jquery.min.js"></script>
<script type="text/javscript" src="/js/jquery.chrony.min.js"></script>
<script type="text/javascript">
	$('#time').chrony({ hour: 1, minute: 2, second: 3 });
</script>
</head>
<body>
<div id="ytapiplayer">
    You need Flash player 8+ and JavaScript enabled to view this video.
  </div>
  <script type="text/javascript">
    var params = { allowScriptAccess: "always" };
    var atts = { id: "myytplayer" };
    swfobject.embedSWF("http://www.youtube.com/apiplayer?enablejsapi=1",
                       "ytapiplayer", "425", "356", "8", null, null, params, atts);
  function onYouTubePlayerReady(playerId) {
      ytplayer = document.getElementById("myytplayer");
      ytplayer.loadVideoById("iQqK2onobes");
      ytplayer.seekTo(150,true);
      ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
      ytplayer.playVideo();     
    }
    function play() {
      ytplayer = document.getElementById("myytplayer");
      if (ytplayer) {
          ytplayer.loadVideoById("29SuuEKztPc");
            ytplayer.playVideo();
          }
    }
    function onytplayerStateChange(newState) {
      // alert("Player's new state: " + newState);
    }
  </script>
<a href="javascript:void(0);" onclick="play();">Next</a>
 <h3>WebSocket v2.00</h3>
 <div id="prueba"></div>
 <div id="log"></div>
 <div id="logpartner"></div>
 <input id="msg" type="textbox" onkeypress="onkey(event)"/> 
 <div id="time"></div>
 <button onclick="send()">Send</button>
 <button onclick="quit()">Quit</button>
 <div>Commands: hello, hi, name, age, date, time, thanks, bye</div>
</body>
</html>