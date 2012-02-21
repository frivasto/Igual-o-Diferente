<html>
<head>
<title>WebSocket</title>
<style>
 html,body{font:normal 0.9em arial,helvetica;}
 #log {width:220px; height:200px; border:1px solid #7F9DB9; overflow:auto; float:left; margin-right: 10px;}
 #logpartner {width:220px; height:200px; border:1px solid #7F9DB9; overflow:auto;}
 #msg {width:330px;}
</style>
<script>
/*Ajax*/
            function createXMLHttpRequest() {
              var request = false;
              /* Does this browser support the XMLHttpRequest object? */
              if (window.XMLHttpRequest) {
                if (typeof XMLHttpRequest != 'undefined')
                  /* Try to create a new XMLHttpRequest object */
                  try {
                    request = new XMLHttpRequest( );
                  } catch (e) {
                    request = false;
                  }
              /* Does this browser support ActiveX objects? */
              } else if (window.ActiveXObject) {
                /* Try to create a new ActiveX XMLHTTP object */
                try {
                  request = new ActiveXObject('Msxml2.XMLHTTP');
                } catch(e) {
                  try {
                    request = new ActiveXObject('Microsoft.XMLHTTP');
                  } catch (e) {
                    request = false;
                  }
                }
              }
              return request;
            }
            var respuestajson=null;
            function consulta(status_socket){
                var request;
                request = createXMLHttpRequest( );
                request.open('GET','<?php echo url_for('Mesa/emparejar'); ?>'+"?estado="+status_socket,true);
                request.onreadystatechange=function(){                      
                    if(request.readyState==4){
                        if(request.status==200){                             
                            respuestajson=manejador(request);  
                            if(respuestajson!=null){
                                //enviar al servidor de sockets
                                sendMensajes(respuestajson);
                                var pruebadiv=document.getElementById("prueba");
                                pruebadiv.innerHTML="RESPUESTA: "+respuestajson;
                            }
                        }
                    }
                };
                request.send(null);
            }
            function manejador(xhr)
            {	
		var resp2=xhr.responseText;
                return resp2;                
            }
var socket;
function init(){
  var host = "ws://127.0.0.1:12345"; //ws://localhost:12345/websocket/server.php
  try{
    socket = new WebSocket(host);
    log('WebSocket - status '+socket.readyState);
    socket.onopen = function(msg){ 
        log("Welcome - status "+this.readyState);
        var status_socket=this.readyState;
        consulta(status_socket); //ajax edita el resultado  y lo envía server sockets        
    };
    socket.onmessage = function(msg){ 
        //json
        var myObject = eval('(' + msg.data + ')');
        //Verificar si mensaje es de tipo conexion o mensaje //Dendiendo del tipo de mensaje enviado 
        if(myObject.tipo=="conexion"){
            //de tipo CONEXION
            if(myObject.objeto.confirmacion=="INCOMPLETO"){
                //Si es incompleto mostrar en pantalla juego deshabilitado no cronom text inahbilit y mensaje esperando
                alert("Estamos buscándole un compañero de juego");
            }else{
                //Si es completo ya habilita juego que estuvo dehabilitado e iniciar cronometro
                alert("Prueba juego habilitado");
            }                                   
        }else{
            //de tipo MENSAJES            
            logPartner("Received: "+myObject.objeto.mensaje);
        }        
    };
    socket.onclose   = function(msg){ 
        log("Disconnected - status "+this.readyState); 
        //además bloquear la pantalla de juego o enviar a una página de error personalizada
    };
  }
  catch(ex){ log("error: "+ex); }
  $("msg").focus();
}
//Envío de mensajes internos al servidor
function sendMensajes(msg){
    try{ socket.send(msg); } catch(ex){ }
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
function logPartner(msg){ $("logpartner").innerHTML+="<br>"+msg; }
function onkey(event){ if(event.keyCode==13){ send(); } }
</script>
</head>
<body onload="init()">
<p>
    Hola usuario: <strong><?php echo $sf_user->getAttribute('userid')?></strong>.
</p>    
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
 <button onclick="send()">Send</button>
 <button onclick="quit()">Quit</button>
 <div>Commands: hello, hi, name, age, date, time, thanks, bye</div>
</body>
</html>