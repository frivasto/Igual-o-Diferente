<script type="text/javascript">
( function($) {
$(document).ready(function() {
    $("#cmd_enviar").button().click(function(){ getHoraMinSec(); send(); });
    $("#same_different").buttonset();
    $("#same_different1").button( { text: true, icons: {primary: "ui-icon-bullet"}}).css({ height:10, width:185})
    .click(function(){
            enviarRespuesta('SAME');
            //$( "#dialog_result" ).dialog( "open" );
    });
    $('#same_different2').button( { text: true, icons: {primary: "ui-icon-bullet"}}).height(20)
    .click(function(){
            enviarRespuesta('DIFFERENT');
            //$( "#dialog_result" ).dialog( "open" );
    });

    //JQUERY CHRONY con callback  otra opcion es enviar el texto completo text: '1:20:30' MEJOR!!
    //PERMITE REAJUSTAR DATOS TAMBIÉN $('#time').chrony('set', { decrement: 2 }); re-adjust runtime options.
    $('#time').chrony({hour: 0, minute: 1, second: 3,finish: function() {
            $(this).html('Finished!');}, blink: true
    });

    $( "#dialog_result" ).dialog({autoOpen: false, show: "blind", zIndex: 9999, hide: "explode", modal: true, position: ['center',260], title: 'Respuesta', dialogClass: 'alert', resizable: false});
    $('.alert div.ui-dialog-titlebar').hide();//transparent
    $('.alert').css('background','transparent');
    ////alert('doing2');
});
} ) ( jQuery );
function getHoraMinSec(){
    var hora=$("#time #hour:first").text();
    var minuto=$("#time #minute:first").text();
    var segundo=$("#time #second:first").text();
    //alert(hora+" - "+minuto+" - "+segundo);
}
</script>

        <script src="http://localhost:6969/socket.io/socket.io.js"></script>
        <script type="text/javascript">
            /*Datos de la session*/
            var round_actual="<?php $round=$sf_user->getAttribute('round_actual'); echo $round-1; ?>"; 
            var mesa_id="<?php echo $sf_user->getAttribute('mesaid'); ?>";
            var jug_id="<?php echo $sf_user->getAttribute('jugadorid'); ?>";
            var modoJugada="<?php echo $sf_user->getAttribute('modoJugada'); ?>";
            var websocket;
            var set_videos=[];    
                        
            <?php 
            $tmp=$sf_user->getAttribute('set_intervalos_videos'); $tam=count($tmp);                               
                for($i=0;$i<$tam;$i++){                                          
            ?>
            var i=<?php echo $i; ?>;             
            var intervaloObj={};
            intervaloObj.inicio="<?php echo $tmp[$i][1]['ini']; ?>";
            intervaloObj.fin="<?php echo $tmp[$i][1]['fin']; ?>";
            intervaloObj.video_url="<?php echo $tmp[$i][1]['url']; ?>";
            intervaloObj.respuesta_real="<?php echo $tmp[$i][0]; ?>";
            set_videos[i]=intervaloObj;
            <?php } ?>
            
            var video_actual=set_videos[round_actual].video_url;
            
            /*Asigna videos por cada round_num, y lo carga*/
            function asignarVideo(round_num){                
                var video_url="";
                var ini;
                var fin;
                var respuesta_real;
                                
                var request;
                request = createXMLHttpRequest();
                request.open('GET','<?php echo url_for('Mesa/obtenerVideoRound'); ?>'+"?round_index="+round_num,true);
                request.onreadystatechange=function(){                      
                    if(request.readyState==4){
                        if(request.status==200){                             
                            respuestajson=request.responseText; 
                            if(respuestajson!=null){                                                                
                                var myObject = eval('(' + respuestajson + ')');
                                video_url=myObject.video_url;
                                ini=myObject.inicio;
                                fin=myObject.fin;
                                respuesta_real=myObject.respuesta_real;
                                ////alert(respuesta_real+" "+video_url +" + "+ini+" + "+fin);
                                cueVideo(video_url);
                                play(0);
                            }
                        }
                    }
                };
                request.send(null);                                
            }            
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
            /*Guarda la etiqueta, en el tiempo que fue ingresada*/
            function consulta_guardarEtiqueta(etiqueta_texto){                
                var request;
                request = createXMLHttpRequest();
                //obtener etiqueta, tiempo
                var hora='00';
                var minuto='01';
                var segundo='50';
                request.open('GET','<?php echo url_for('Mesa/insertarEtiqueta'); ?>'+"?etiqueta_texto="+etiqueta_texto+"&hora="+hora+"&minuto="+minuto+"&segundo="+segundo+"&mesa_id="+mesa_id+"&jug_id="+jug_id+"&round_num="+round_actual,true);
                request.onreadystatechange=function(){                      
                    if(request.readyState==4){
                        if(request.status==200){                             
                            respuestajson=request.responseText;
                            if(respuestajson!=null && respuestajson!='' && respuestajson!='0'){
                                //alert("se guardo!!");
                            }
                        }
                    }
                };
                request.send(null);
            }
            /*Guarda la respuesta, sea Same o Different, en el tiempo que fue ingresada*/
            function guardarRespuesta(respuesta){                
                var request;
                request = createXMLHttpRequest();
                //obtener respuesta, tiempo
                var hora='00';
                var minuto='01';
                var segundo='50';
                request.open('GET','<?php echo url_for('Mesa/insertarDecision'); ?>'+"?respuesta="+respuesta+"&hora="+hora+"&minuto="+minuto+"&segundo="+segundo+"&mesa_id="+mesa_id+"&jug_id="+jug_id+"&round_num="+round_actual,true);
                request.onreadystatechange=function(){                      
                    if(request.readyState==4){
                        if(request.status==200){                             
                            respuestajson=request.responseText;
                            if(respuestajson!=null && respuestajson!='' && respuestajson!='0'){
                                //alert("se guardo!!");
                            }
                        }
                    }
                };
                request.send(null);
            }
            /*Actualiza el puntaje de la base, de la mesa jugador*/
            function actualizarPuntaje(puntos){                
                var request;
                request = createXMLHttpRequest();               
                request.open('GET','<?php echo url_for('Mesa/actualizarPuntaje'); ?>'+"?puntos="+puntos+"&mesa_id="+mesa_id+"&jug_id="+jug_id,true);
                request.onreadystatechange=function(){                      
                    if(request.readyState==4){
                        if(request.status==200){                             
                            respuestajson=request.responseText;
                            if(respuestajson!=null && respuestajson!='' && respuestajson!='0'){
                                //mostrar en pantalla correcto, incorrecto n seconds
                                if(puntos==100)alert("Correcto, Puntaje: "+puntos); 
                                else alert("Incorrecto, Puntaje: "+puntos);
                                //y al cerrar eso, asignar nuevo video
                            }
                        }
                    }
                };
                request.send(null);
            }
            /*Inicializa websocket si es modo Parejas*/
            function init(){                
                //alert(modoJugada);        
                if(modoJugada=="PAREJAS"){
                    websocket = io.connect("http://localhost:6969");
                    //Websockets: onconnect
                    websocket.on('connect', function () {
                        //enviar mensaje de tipo identificacion  con mesai8d y jugid
                        enviar_mensaje_interno("identificacion",jug_id);
                    });
                    
                    //Websockets: onmessage
                    websocket.on("sendEvent", function(data){
                        var obj=JSON.parse(data);
                        var keys=Object.keys(obj.objeto);
                        var key=keys[0];
                        var value=obj.objeto[key];                            
                        if(obj.tipo=="conexion"){
                            if(value=="INCOMPLETO"){                                
                                //alert("Estamos buscándole un compañero de juego");
                            }else{                                
                                //alert("Prueba juego habilitado");                            
                            }
                        }else if(obj.tipo=="sincronizacion-completa"){                            
                            setTimeout(function(){ 
                                $("#content").unmask();
                                play(0);
                                mute(false);
                                logPartner("Received: COORDENADA ES: "+value);
                            },0.007);
                        }else if(obj.tipo=="same-different"){                         
                            //guardar puntaje, acumularlo  
                            //alert("aquii same-different");
                            actualizarPuntaje(value);                            
                        }else{
                            logPartner("Received: "+value);
                        }                           
                    }); 
                    
                    //Websockets: ondisconnect
                    websocket.on('disconnect', function () {
                        //alert("finnn, se cerró servidor");
                    });             
                }
            }
            /*Envía mensajes al servidor de sockets*/
            function enviar_mensaje_interno(tipo,texto){
                websocket.emit("mensaje", '{"tipo":"'+tipo+'","objeto":{"'+mesa_id+'": "'+texto+'"}}'); //si tipo es interno no lo muestre
            }      
            /*Envía objeto al servidor de sockets*/
            function enviar_objeto(tipo,atributo1,atributo2){
                websocket.emit("mensaje", '{"tipo":"'+tipo+'","objeto":{"'+mesa_id+'": {"atributo1":"'+atributo1+'","atributo2":"'+atributo2+'"}}}');
            }
            /*Envía etiqueta a servidor de socket, y guarda la etiqueta en la base*/
            function send(){                
                var message = document.getElementById("txt_mensaje");
                var msg = message.value;
                if(!msg){ //alert("Message can not be empty");
                 return; }
             
                websocket.emit("mensaje", '{"tipo":"mensajes","objeto":{"'+mesa_id+'": "'+msg+'"}}');                
                log('Sent: -mesa: '+mesa_id+" - "+msg);//consulta_guardarEtiqueta(msg); 
                message.value = '';
                message.focus();       
            }
            /*Imprime mensajes en el área Tú */
            function log(msg){ 
                document.getElementById("log").innerHTML+="<br>"+msg; 
            }
            /*Imprime mensajes en el área Partner */
            function logPartner(msg){ 
                document.getElementById("logpartner").innerHTML+="<br>"+msg; 
            }
            /*Envía, con evento keypress, la etiqueta a servidor de socket, y guarda la etiqueta en la base*/
            function enviar_texto(e){ 
                if (e.keyCode == 13) send();
            }   
            
            /*Uso de Youtube Api*/
            var params = { allowScriptAccess: "always" };
            var atts = { id: "myytplayer" };
            swfobject.embedSWF("http://www.youtube.com/apiplayer?enablejsapi=1&version=3",
            "ytapiplayer", "400", "233", "8", null, null, params, atts);
            //400 273     360  220
            var ytplayer;
            function onYouTubePlayerReady(playerId) {
                ytplayer = document.getElementById("myytplayer");				
                ytplayer.addEventListener("onStateChange", "onytplayerStateChange");		
                //ytplayer.cueVideoById("DZfCPEn6L6g");	
                cueVideo(video_actual);
                play(0);
            }            
            function cueVideo(video_url) {		
                if (ytplayer) {
                    ytplayer.cueVideoById(video_url);	
                }
            }           
            function play(inicio_min) {		
                if (ytplayer) {
                    ytplayer.seekTo(inicio_min,true);
                    ytplayer.playVideo();
                }
            }            
            function mute(activado) {		
                if (ytplayer) {
                    if(activado)ytplayer.mute();
                    else ytplayer.unMute();
                }
            }            
            var estacargado=0; var resp; var loaded;
            function onytplayerStateChange(newState) {  
                resp = document.getElementById("resp");
                resp.innerHTML="total: "+ytplayer.getVideoBytesTotal()+" - cargando: "+(ytplayer.getVideoBytesLoaded())+" - estado: "+newState;
		//if(newState==0) ytplayer.playVideo(); //ninguno de estos estados			
                //Possible values are unstarted (-1), ended (0), playing (1), paused (2), buffering (3), video cued (5).
		/*if(ytplayer.getVideoBytesLoaded()>=ytplayer.getVideoBytesTotal() && ytplayer.getVideoBytesTotal()!=0){                    
                    enviar_objeto("sincronizacion-videos",jug_id,"COMPLETO");
                    alerta("se cargo todo el video, lo envío a server sockets");
                }else{
                    //ytplayer.pauseVideo();	//pausado no hace buffer ràpido		                    
                    //mostrarLoading();                    
                    mute(true);
                }*/
                if(newState==1) {
                    alerta("se cargo todo el video, lo envío a server sockets");
                    ytplayer.pauseVideo(); 
                    estacargado=1;
                    enviar_objeto("sincronizacion-videos",jug_id,"COMPLETO");
                    loaded = document.getElementById("loaded");
                    loaded.innerHTML+="Estado Video:: "+newState;                    
                } else{
                    //ytplayer.playVideo();
                }               
            } 
            
            /*Muestra una máscara loading*/
            function mostrarLoading(){
                ( function($) {
                $(document).ready(function(){  
                    $("#content").mask("Esperando se sincronicen los videos...");                    
                });
                } ) ( jQuery );
            } 
            /*Envía respuesta a servidor de socket, y guarda la etiqueta en la base*/
            function enviarRespuesta(respuesta){                
                //ENVIAR POR AJAX REQUERIMIENTO PARA GUARDAR LA RESPUESTA
                guardarRespuesta(respuesta);
                //ENVIAR A SOCKET_SERVER PARA ACTUALIZA LA RESPUESTA
                enviar_objeto('same-different',jug_id,''+respuesta); 
            }

            window.onload = init;
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
                                <div id="log"></div>
			</div>
		</div>
		<div id="content1">
			<div id="same_different">
				<input type="radio" name="same_different" id="same_different1" checked value="1" onclick=""><label for="same_different1" > Igual </label>
				<input type="radio" name="same_different" id="same_different2" value="2" onclick=""><label for="same_different2" > Diferente </label>                               
			</div>
                        <div id="video_principal">
                            <!--<iframe width="360" height="200" src="http://www.youtube.com/embed/a_YR4dKArgo?rel=0&controls=0&border=0&egm=0&showinfo=0&showsearch=0&wmode=transparent" frameborder="0" allowfullscreen></iframe>-->
                            <div id="content">
                                <div id="ytapiplayer">
                                    You need Flash player 8+ and JavaScript enabled to view this video.
                                </div>
                            </div>
                        </div>
			<p style="font-size:10px; font-weight:bold" >
                            <input type="text" id="txt_mensaje" onkeypress="enviar_texto(event);"/>                          
                            <input type="button" value="Enviar" id="cmd_enviar" />
                        </p>
		</div>
		<div id="content_side2">
			<h3>Tu compa&ntilde;ero</h3>
			<div id="chat_partner">                            
                            <div id="logpartner"></div>
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

        <p style="color:black;">Hola usuario: <strong><?php echo $sf_user->getAttribute('userid') ?></strong>.</p>            
        <p style="color:black;" id="resp"></p>
        <p style="color:black;" id="loaded"></p>
        <a href="<?php echo url_for('Mesa/new'); ?>">Volver a Jugar </a><!--Va en interfaz de ranking-->    