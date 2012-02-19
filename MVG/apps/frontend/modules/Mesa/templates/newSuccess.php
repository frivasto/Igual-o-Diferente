<html>
<head>
<title>WebSocket</title>
<style>
 html,body{font:normal 0.9em arial,helvetica;}
 #log {width:440px; height:200px; border:1px solid #7F9DB9; overflow:auto;}
 #msg {width:330px;}
</style>
<script>
var socket;
function init(){
  var host = "ws://127.0.0.1:12345"; //ws://localhost:12345/websocket/server.php
  try{
    socket = new WebSocket(host);
    log('WebSocket - status '+socket.readyState);
    socket.onopen    = function(msg){ log("Welcome - status "+this.readyState); };
    socket.onmessage = function(msg){ log("Received: "+msg.data); };
    socket.onclose   = function(msg){ log("Disconnected - status "+this.readyState); };
  }
  catch(ex){ log("error: "+ex); }
  $("msg").focus();
}

function send(){
  var txt,msg;
  txt = $("msg");
  msg = txt.value;
  if(!msg){ alert("Message can not be empty"); return; }
  txt.value="";
  txt.focus();
  try{ socket.send(msg); log('Sent: '+msg); } catch(ex){ log(ex); }
}
function quit(){
  log("Goodbye!");
  socket.close();
  socket=null;
}

// Utilities
function $(id){ return document.getElementById(id); }
function log(msg){ $("log").innerHTML+="<br>"+msg; }
function onkey(event){ if(event.keyCode==13){ send(); } }
</script>

</head>
<body onload="init()">
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
 <div id="log"></div>
 <input id="msg" type="textbox" onkeypress="onkey(event)"/>
 <button onclick="send()">Send</button>
 <button onclick="quit()">Quit</button>
 <div>Commands: hello, hi, name, age, date, time, thanks, bye</div>
</body>
</html>