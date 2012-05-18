<script type="text/javascript">
$(document).ready(function() {
    alert('doing');
    var a1=document.getElementById("cmd_enviar");
    var a2=$("#cmd_enviar");
    alert("a1"+a1);
    alert("a1"+a2);
    //a1.button().click(function(){ getHoraMinSec(); });
    //a2.button().click(function(){ getHoraMinSec(); });
    $("#cmd_enviar").button().click(function(){ getHoraMinSec(); });
    $("#same_different").buttonset();
    $("#same_different1").button( { text: true, icons: {primary: "ui-icon-bullet"}}).css({ height:10, width:185})
    .click(function(){
            $( "#dialog_result" ).dialog( "open" );
    });
    $('#same_different2').button( { text: true, icons: {primary: "ui-icon-bullet"}}).height(20)
    .click(function(){
            $( "#dialog_result" ).dialog( "open" );
    });

    //JQUERY CHRONY con callback  otra opcion es enviar el texto completo text: '1:20:30' MEJOR!!
    //PERMITE REAJUSTAR DATOS TAMBIÉN $('#time').chrony('set', { decrement: 2 }); re-adjust runtime options.
    $('#time').chrony({hour: 0, minute: 1, second: 3,finish: function() {
            $(this).html('Finished!');}, blink: true
    });

    $( "#dialog_result" ).dialog({autoOpen: false, show: "blind", zIndex: 9999, hide: "explode", modal: true, position: ['center',260], title: 'Respuesta', dialogClass: 'alert', resizable: false});
    $('.alert div.ui-dialog-titlebar').hide();//transparent
    $('.alert').css('background','transparent');
    alert('doing2');
});

function getHoraMinSec(){
    var hora=$("#time #hour:first").text();
    var minuto=$("#time #minute:first").text();
    var segundo=$("#time #second:first").text();
    alert(hora+" - "+minuto+" - "+segundo);
}
</script>

<!-- END PASTE-->
<!-- <style>
html,body{font:normal 0.9em arial,helvetica;}
#log {width:220px; height:200px; border:1px solid #7F9DB9; overflow:auto; float:left; margin-right: 10px;}
#logpartner {width:220px; height:200px; border:1px solid #7F9DB9; overflow:auto;}
#msg {width:330px;}
</style>-->

<script>
var round_actual=1;    
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
var mesa_id;
/*AJax para enviar a emparejar*/
function consulta(status_socket){
    var request;
    request = createXMLHttpRequest();
    request.open('GET','<?php echo url_for('Mesa/consultarIdentificacion'); ?>'+"?estado="+status_socket,true);
    request.onreadystatechange=function(){
        if(request.readyState==4){
            if(request.status==200){
                respuestajson=manejador(request);
                if(respuestajson!=null){
                    sendMensajes(respuestajson);//enviar al servidor de sockets
                    var pruebadiv=document.getElementById("prueba");
                    pruebadiv.innerHTML="RESPUESTA: "+respuestajson;
                    var myObject = eval('(' + respuestajson + ')');
                    var keys=Object.keys(myObject.objeto);
                    mesa_id=keys[0]; //actualizar mesa_id de este usuario para enviarlo
                    alert("mesaid= "+mesa_id);
                }
            }
        }
    };
    request.send(null);
}
function manejador(xhr)
{	
    return xhr.responseText;
}
/*AJAX para mandar a guardar la etiqueta y acumularla y evaluarla para el puntaje del jugador*/
/*ENVIAR  ETIQUETA TEXTO, TIEMPO */
function consulta_guardarEtiqueta(etiqueta_texto){                
    var request;
    request = createXMLHttpRequest();
    //obtener etiqueta, tiempo
    var hora='00';
    var minuto='01';
    var segundo='50';
    request.open('GET','<?php echo url_for('Mesa/insertarEtiqueta'); ?>'+"?etiqueta_texto="+etiqueta_texto+"&hora="+hora+"&minuto="+minuto+"&segundo="+segundo,true);
    request.onreadystatechange=function(){
        if(request.readyState==4){
            if(request.status==200){
                respuestajson=manejador(request);
                if(respuestajson!=null && respuestajson!='' && respuestajson!='0'){
                    alert("se guardo!!");
                }
            }
        }
    };
    request.send(null);
}
/*Actualiza estado del video_intervalo*/
function actualizar_intervalo_estado(estado){                
    var request;
    request = createXMLHttpRequest();
    request.open('GET','<?php echo url_for('Mesa/actualizarIntervaloEstado'); ?>'+"?estado="+estado+"&mesa_id"+mesa_id+"&num_round="+round_actual,true);
    request.onreadystatechange=function(){
        if(request.readyState==4){
            if(request.status==200){
                respuestajson=manejador(request);
                if(respuestajson!=null && respuestajson!='' && respuestajson!='0'){
                    var myObject = eval('(' + respuestajson + ')');
                    var respuesta=myObject.respuesta;
                    alert("estdo de intervalo, hacer algo si resp= COMPLETO" +respuesta);
                    //Si está completo se inica el round, no más que podría esperar mucho en ciertos casos
                    if(respuesta=='COMPLETO'){
                        alerta("empieza round!!!!!!");
                    }
                }
            }
        }
    };
    request.send(null);
}
/****************************** Tiempo Regresivo ****************************/            
var today2=new Date();
today2.setHours(0);
today2.setMinutes(3);
today2.setSeconds(0);            
function bajarTime()
{
    var h=today2.getHours();
    var m=today2.getMinutes();
    var s=today2.getSeconds();

    if(!(h==0 && m==0 && s==0)){
        today2.setSeconds(today2.getSeconds()-1);
        m=today2.getMinutes();
        s=today2.getSeconds();
        // add a zero in front of numbers<10
        m=checkTime(m);
        s=checkTime(s);
        document.getElementById('timer').innerHTML=h+":"+m+":"+s;
        t=setTimeout('bajarTime()',1000);
    }
}

function checkTime(i)
{
    if (i<10)i="0" + i;
    return i;
}

/****************************** SOCKETS ****************************/
var socket;
var connected=false;
var supported=true;
var host = "ws://127.0.0.1:12345"; //ws://localhost:12345/websocket/server.php	
var appType;
function init(){
    var modoJugada="<?php echo $sf_user->getAttribute('modoJugada'); ?>";
    alert(modoJugada);

    if (typeof MozWebSocket != "undefined") { // (window.MozWebSocket)
        appType = "MozillaFirefox10";
    } else if (window.WebSocket) {
        appType = "Chrome";
    } else {
        alert('<strong style="color: red;">ERROR: This browser does not support WebSockets.</strong>');
        supported=false;
    }

    if(modoJugada=="PAREJAS"){
        if(supported){ //Supported
            if (connected) { //Conected WebSocket
                alert("<span style='color: red;'>You're already connected!</span>");
            } else {
                try{
                    if(!socket){
                        if (appType == "MozillaFirefox10") {
                            socket = new MozWebSocket(host);
                        } else {
                            socket = new WebSocket(host);
                        }
                    }
                    log('WebSocket - status '+socket.readyState);
                    socket.onopen = function(msg){
                        log("Welcome - status "+this.readyState);
                        var status_socket=this.readyState;
                        consulta(status_socket); //Lo envía server sockets
                        connected=true;
                    };
                    socket.onmessage = function(msg){
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
                                //COMPLETO, VER SI SE CARGARON LOS VIDEOS DE AMBOS PARA SINCRONIZAR
                                bajarTime();
                            }
                        }else{
                            //de tipo MENSAJES
                            var keys=Object.keys(myObject.objeto);
                            var key=keys[0];
                            var value=myObject.objeto[key];
                            logPartner("Received: "+value);
                        }
                    };
                    socket.onclose   = function(msg){ log("Disconnected - status "+this.readyState); connected=false;};
                }
                catch(ex){ log("error: "+ex); }
            }
            $("msg").focus();
        }
    }
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
    try{
        var mensajejson= '{"tipo":"mensajes","objeto":{"'+mesa_id+'": "'+msg+'"}}';
        //enviarlo a aoacket server
        socket.send(mensajejson);
        //mostrarlo en la cajita de texto Tu
        log('Sent: -mesa: '+mesa_id+" - "+msg);
        //guardarlo en la base de datos llamar al action por ajax
        //consulta_guardarEtiqueta(msg);
    } catch(ex){ log(ex); }
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

function asignarVideo(round_index, jugador_index){
    //ajax obtener video
    var request;
    request = createXMLHttpRequest();
    request.open('GET','<?php echo url_for('Mesa/ObtenerVideoRound'); ?>'+"?round_index="+round_index+"&jugador_index="+jugador_index,true);
    request.onreadystatechange=function(){
        if(request.readyState==4){
            if(request.status==200){
                respuestajson=manejador(request);
                if(respuestajson!=null && respuestajson!='' && respuestajson!='0'){
                    var myObject = eval('(' + respuestajson + ')');
                    var url_video=myObject.video_url;
                    var respuesta_real=myObject.respuesta_real;
                    //calcular intervalo de ese video con fórmula yotube api
                    alert(respuestajson);
                    alert(url_video);
                    play(url_video);
                }
            }
        }
    };
    request.send(null);
}
</script>
<script type="text/javascript">
var params = { allowScriptAccess: "always" };
var atts = { id: "myytplayer" };
swfobject.embedSWF("http://www.youtube.com/apiplayer?enablejsapi=1",
"ytapiplayer", "425", "356", "8", null, null, params, atts);
function onYouTubePlayerReady(playerId) {
    ytplayer = document.getElementById("myytplayer");
    //play("I9cCPQVPv8o&ob"); //or2GH3CHXqY I9cCPQVPv8o&ob
    asignarVideo(round_actual,1);
    /*
    ytplayer.loadVideoById("iQqK2onobes");
    ytplayer.seekTo(150,true);
    ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
    ytplayer.playVideo();     */
}

function play(video_url) {
    ytplayer = document.getElementById("myytplayer");
    if (ytplayer) {
        ytplayer.loadVideoById(video_url);
        ytplayer.playVideo();
    }
}
function onytplayerStateChange(newState) {                
    if(newState==0){
        actualizar_intervalo_estado(newState);
    }
}

</script>

<div id="wrapper">
	<div id="header">
		<h1>My First Heading</h1>
		<p>Divi&eacute;rtete</p>
		<div id="timer_content">
			<h3>Tiempo:</h3>
			<div id="time" class="content_text" ></div>
		</div>
		<div id="puntos_content">
			<h3>Puntos:</h3>
			<h3 class="content_text">100</h3>
		</div>
	</div>
	<div id="body">
		<div id="content_side1">
			<h3>T&uacute;</h3>
			<div id="chat_jug">
				<textarea cols="12" readonly="readonly">Holaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa</textarea>
			</div>
		</div>
		<div id="content1">
			<div id="same_different">
				<input type="radio" name="same_different" id="same_different1" checked value="1" onclick=""><label for="same_different1" > Igual </label>
				<input type="radio" name="same_different" id="same_different2" value="2" onclick=""><label for="same_different2" > Diferente </label>
			</div>
			<div id="video_principal">
				<!--iframe zindex youtube wmode=transparent-->
				<!--UNSAFE JAVASCRIPT SOLUTION: http://apiblog.youtube.com/2011/01/introducing-javascript-player-api-for.html-->
				<!--https://developers.google.com/youtube/iframe_api_reference?hl=es-ES-->
				<iframe width="360" height="200" src="http://www.youtube.com/embed/a_YR4dKArgo?rel=0&controls=0&border=0&egm=0&showinfo=0&showsearch=0&wmode=transparent" frameborder="0" allowfullscreen></iframe>
			</div>
			<p style="font-size:10px; font-weight:bold" ><input type="text" name="txt_palabra" /><input type="button" value="Enviar" id="cmd_enviar" /></p>
		</div>
		<div id="content_side2">
			<h3>Tu compa&ntilde;ero</h3>
			<div id="chat_partner">
				<textarea cols="12" readonly="readonly">HOLA siiiiiiiiiiiiiii</textarea>
			</div>
		</div>
	</div>
	<div id="dialog_result" title="Basic dialog" style="display:none; background: #333; min-height: 0px !important; opacity:0.95; filter:alpha(opacity=95); ">
		<div style="float:left;" ><p style="font-size:10px; font-weight:bold;">Tu</p><img src="/images/icon_checked.png" width="16px" /></div>
		<div style="float:left; padding:0px 25px;"><h3 style="text-align:center; font-size:20px; color:white;">Incorrecto</h3></div>
		<div style="float:left;"><p style="font-size:10px; font-weight:bold;">Tu compa&ntilde;ero</p><img src="/images/icon_checked.png" width="16px" /></div>
	</div>
</div>
<!--
<p>
    Hola usuario: <strong><?php //echo $sf_user->getAttribute('userid') ?></strong>.
</p>    
<div id="ytapiplayer">
    You need Flash player 8+ and JavaScript enabled to view this video.
</div>

<a href="javascript:void(0);" onclick="play('I9cCPQVPv8o&ob');">Same-Different</a>
<h3>WebSocket v2.00</h3>
<div id="prueba"></div>
<div id="log"></div>
<div id="logpartner"></div>
<input id="msg" type="textbox" onkeypress="onkey(event)"/>
<p id="timer"></p>
<button onclick="send()">Send</button>
<button onclick="quit()">Quit</button>
<div>Commands: hello, hi, name, age, date, time, thanks, bye</div>
<a href="<?php // echo url_for('Mesa/new'); ?>">Volver a Jugar </a>
<script>init();</script>

-->