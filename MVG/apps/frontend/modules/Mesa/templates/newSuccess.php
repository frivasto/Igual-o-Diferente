<script type="text/javascript">
( function($) {
$(document).ready(function() {
    $("#cmd_enviar").button().click(function(){ send(); });
    
    $("input","#same_different").button();
    $("#same_different").buttonset();
    $("#same_different1").button( { text: true, icons: {primary: "ui-icon-bullet"}}).css({ height:10, width:185})
    .click(function(){
            enviarRespuesta('SAME');
    });
    $('#same_different2').button( { text: true, icons: {primary: "ui-icon-bullet"}}).height(20)
    .click(function(){
            enviarRespuesta('DIFFERENT');
    });    
    $( "#dialog_result" ).dialog({autoOpen: false, show: "blind", zIndex: 9999, hide: "explode", modal: true, position: ['center',260], title: 'Respuesta', dialogClass: 'alert', resizable: false});
    $('.alert div.ui-dialog-titlebar').hide();//transparent
    $('.alert').css('background','transparent');    
});
} ) ( jQuery );

//JQUERY CHRONY con callback  otra opcion es enviar el texto completo text: '1:20:30' MEJOR!!
//PERMITE REAJUSTAR DATOS TAMBIÉN $('#time').chrony('set', { decrement: 2 }); re-adjust runtime options.
function empezarTimerGlobal(){
( function($) {
    $('#timeglobal').chrony({hour: 0, minute: 4, second: 0,finish: function() {
        $(this).html('Finished!');
        }, blink: true
    });
} ) ( jQuery );
}    

function empezarTimerLocal(){
( function($) {
    //$(window).unbind('.chrony');
    $("#time").remove();
    $("#timer_content").append("<div id='time' class='content_text_min' ></div>");
    //$('#time').unbind('.chrony'); //.chrony('destroy')
    $('#time').chrony({hour: 0, minute: 0, second: 35,finish: function() {
        //aqui va evento same different automatico envíe """ si el usuario no ha contestado
        verificarEnvioRespuesta();
        /*$(this).html('Finished!');*/
        }, blink: true
    });
} ) ( jQuery );
}

</script>

        <script src="http://localhost:6969/socket.io/socket.io.js"></script>
        <script type="text/javascript">
            /*Datos de la session*/
            var round_actual=<?php $round=$sf_user->getAttribute('round_actual'); echo $round-1; ?>; 
            var mesa_id="<?php echo $sf_user->getAttribute('mesaid'); ?>";
            var jug_id="<?php echo $sf_user->getAttribute('jugadorid'); ?>";
            var modoJugada="<?php echo $sf_user->getAttribute('modoJugada'); ?>";
            var websocket;
            var set_videos=[]; 
            var envioDecision=0;
            var vecesjugadas=0;
            var puntos_extra=0;
            
            <?php 
            $tmp=$sf_user->getAttribute('set_intervalos_videos'); $tam=count($tmp);                               
                for($i=0;$i<$tam;$i++){                                          
            ?>
            var tam_arr_php=<?php echo $tam; ?>; 
            //alert("en php: "+tam_arr_php);
            
            var i=<?php echo $i; ?>;             
            var intervaloObj={};
            intervaloObj.inicio="<?php echo $tmp[$i][1]['ini']; ?>";
            intervaloObj.fin="<?php echo $tmp[$i][1]['fin']; ?>";
            intervaloObj.video_url="<?php echo $tmp[$i][1]['url']; ?>";
            intervaloObj.respuesta_real="<?php echo $tmp[$i][0]; ?>";
            set_videos[i]=intervaloObj;
            <?php } ?>
            
           // alert("en js: "+set_videos.length);
            var video_actual=set_videos[round_actual].video_url;
            //cueVideo(video_url); play(0);
            
            /*Sino ha contestado este jugador, entonces enviar NO_CONTESTO*/
            function verificarEnvioRespuesta(){                
                if(envioDecision==0) enviarRespuesta('NO_CONTESTO');
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
                var hora=$("#time #hour:first").text();
                var minuto=$("#time #minute:first").text();
                var segundo=$("#time #second:first").text();
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
                var hora=$("#time #hour:first").text();
                var minuto=$("#time #minute:first").text();
                var segundo=$("#time #second:first").text();
                
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
            /*PASO DE NIVELES ROUNDS*/
            /*Actualiza el puntaje de la base, de la mesa jugador*/
            function actualizarPuntaje(puntos,resultado_jug_tu,resultado_jug_partner){                
                var request;
                request = createXMLHttpRequest();               
                request.open('GET','<?php echo url_for('Mesa/actualizarPuntaje'); ?>'+"?puntos="+puntos+"&mesa_id="+mesa_id+"&jug_id="+jug_id,true);
                request.onreadystatechange=function(){                      
                    if(request.readyState==4){
                        if(request.status==200){                             
                            respuestajson=request.responseText;
                            if(respuestajson!=null && respuestajson!='' && respuestajson!='0'){
                                //****************** reusltados y PASAR A SIGUIENTE ROUND ***************************                              
                                //EDITAR RESULTADO_INDIVIDUAL 
                                //JUG1    
                                //alert(puntos+" - "+resultado_jug_tu+" - "+resultado_jug_partner);
                                if(resultado_jug_tu=="ACIERTO") $("#respuesta_jug").attr({ src: "/images/check.png", alt: "Resultado Jug1" });
                                else $("#respuesta_jug").attr({ src: "/images/cross.png", alt: "Resultado Jug1" });
                                //JUG2
                                if(resultado_jug_partner=="ACIERTO") $("#respuesta_jug_partner").attr({ src: "/images/check.png", alt: "Resultado Jug2" });
                                else $("#respuesta_jug_partner").attr({ src: "/images/cross.png", alt: "Resultado Jug2" });
                                
                                //EDITAR PUNTAJE GRUPAL O RESULTADO_DECISIONES_COLABORATIVAS [mostrar en pantalla correcto, incorrecto por n seconds]
                                if(puntos==100+"") $("#resultado_decision").html("Correcto2");                                                                                                    
                                else $("#resultado_decision").html("Incorrecto2");
                                
                                $("#puntaje_grupal").html(puntos);
                                
                                //MOSTRAR RESULTADO 5000ms
                                $( "#dialog_result" ).dialog( "open" );
                                setTimeout(function(){$( "#dialog_result" ).dialog("close")},5000);
                                                                
                                //incrementar round
                                round_actual++;    
                                if(round_actual<set_videos.length){
                                    //y al cerrar eso, asignar nuevo video
                                    video_actual=set_videos[round_actual].video_url;
                                    cueVideo(video_actual);
                                    

                                    //Limpiar
                                    envioDecision=0;
                                    estacargado=0; //video 

                                    //Limpiar LOG y LOGPARTNER
                                    document.getElementById("log").innerHTML="";
                                    document.getElementById("logpartner").innerHTML="";
                                    
                                    //reiniciar timer local <-- eso ya lo incluye la sync video
                                    empezarTimerLocal(); //aqui no va sino después de sincronizado, por ahora qui probar
                                    
                                }else{
                                    //Ir a Game Over url
                                    window.location.href = "<?php echo url_for('Mesa/gameOver') ?>";
                                }
                                    
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
                        //alert("obj:"+obj);
                        var value=obj.objeto[key];                            
                        if(obj.tipo=="conexion"){
                            if(value=="INCOMPLETO"){                                
                                //alert("Estamos buscándole un compañero de juego");
                            }else{                                
                                //alert("Prueba juego habilitado");                            
                            }
                        }else if(obj.tipo=="sincronizacion-completa"){                            
                            setTimeout(function(){ 
                            //    $("#content").unmask();
                                play(0);
                                mute(false);
                              //  alert(value);
                                //logPartner("Received: SINCRONIZADOS: "+value);
                                //iniciar AQUI timer de ese round sincronizado
                            },0.007);
                        }else if(obj.tipo=="same-different"){                                                             
                            var keys=Object.keys(value);                            
                            var puntaje_grupal=value[keys[0]];
                            var resultado_jug_tu=value[keys[1]];
                            var resultado_jug_partner=value[keys[2]]; 
                            
                            //CORRECTO, acumular vecesjugadas consecutivas
                            if(resultado_jug_tu==resultado_jug_partner) vecesjugadas++;
                            else vecesjugadas=0;
                            
                            //3 vecesjugadas consecutivas incrementa 10 puntos
                            if(vecesjugadas!=0 && vecesjugadas%3==0){
                               puntos_extra=10;
                            }
                            
                            puntaje_grupal+=puntos_extra;
                            
                            //guardar puntaje, acumularlo
                            actualizarPuntaje(puntaje_grupal,resultado_jug_tu,resultado_jug_partner);                            
                        }else{
                            logPartner(""+value);
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
                 return; 
                }             
                websocket.emit("mensaje", '{"tipo":"mensajes","objeto":{"'+mesa_id+'": "'+msg+'"}}');                
                log(""+msg);
                consulta_guardarEtiqueta(msg);
                
                message.value = '';
                message.focus();                 
            }
            /*Imprime mensajes en el área Tú */
            function log(msg){                 
                var texto=document.getElementById("log").innerHTML;
                document.getElementById("log").innerHTML=msg+"<br>"+texto; 
            }
            /*Imprime mensajes en el área Partner */
            function logPartner(msg){                 
                var texto=document.getElementById("logpartner").innerHTML;
                document.getElementById("logpartner").innerHTML=msg+"<br>"+texto; 
            }
            /*Envía, con evento keypress, la etiqueta a servidor de socket, y guarda la etiqueta en la base*/
            function enviar_texto(e){ 
                if (e.keyCode == 13) send();
            }   
            
            /*Uso de Youtube Api*/
            var params = { allowScriptAccess: "always", wmode:"transparent" };
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
                //play(0);
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
            var estacargado=0; var resp; var loaded; var cont=0;
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
                //alert("estado: "+newState+" tipo: "+(typeof newState));
                //if(newState!=1)
                if(newState!=-1 && estacargado==0) {
                    //alert("se cargo todo el video, lo envío a server sockets");
                    ytplayer.pauseVideo(); 
                    estacargado=1;
                    enviar_objeto("sincronizacion-videos",jug_id,"COMPLETO");
                    loaded = document.getElementById("loaded");
                    cont++;
                    loaded.innerHTML="Estado Video:: "+newState+" se cargo todo el video, lo envío a server sockets "+cont;                     
                    //empezar reloj local una vez sinronizado no aqui en sync complet                   
                } else{
                    //ytplayer.playVideo();
                    //alerta("no playing");
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
                                
                var rsp_real=set_videos[round_actual].respuesta_real;
                if(rsp_real==1) rsp_real="SAME";
                else rsp_real="DIFFERENT";
                
                //resultado: ACIERTO si coinciden
                if(respuesta==rsp_real+"") respuesta='ACIERTO';
                //else respuesta='NO_ACIERTO';
                
                //ENVIAR A SOCKET_SERVER PARA ACTUALIZA LA RESPUESTA
                enviar_objeto('same-different',jug_id,''+respuesta); 
                envioDecision=1;
            }

            function inicializar(){
                init();
                empezarTimerGlobal();
                empezarTimerLocal(); //aqui no va sino después de sincronizado, por ahora qui probar
            }
            window.onload = inicializar;
        </script>

<div id="wrapper">
	<div id="header">
		<h1>CazaVideos</h1>
		<p>Divi&eacute;rtete</p>
		<div id="timer_content">
                    <h3>Tiempo:</h3>
                    <div id="timeglobal" class="content_text" ></div>
                    <div id="clear-fix" style="clear:both; width:100%"></div>
                    <!--Delete and Create again and add plugin-->
                    <div id="time" class="content_text_min" ></div>                    
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
				<input type="radio" name="same_different" id="same_different1" checked value="1" /><label for="same_different1" > Igual </label>
				<input type="radio" name="same_different" id="same_different2" value="2" /><label for="same_different2" > Diferente </label>                               
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
		<div style="float:left;" ><p style="font-size:10px; font-weight:bold;">Tu</p><img id="respuesta_jug" src="/images/icon_checked.png" width="16px" /></div>
		<div style="float:left; padding:0px 25px;"><h3 style="text-align:center; font-size:20px; color:white;" id="resultado_decision" >Incorrecto</h3><p id="puntaje_grupal" ></p></div>
		<div style="float:left;"><p style="font-size:10px; font-weight:bold;">Tu compa&ntilde;ero</p><img id="respuesta_jug_partner" src="/images/icon_checked.png" width="16px" /></div>
	</div>
</div>
<!--Fin de Interfaz-->
        <p style="color:black;">Hola usuario: <strong><?php echo $sf_user->getAttribute('userid') ?></strong>.</p>            
        <p style="color:black;" id="resp"></p>
        <p style="color:black;" id="loaded"></p>
        <a href="<?php echo url_for('Mesa/new'); ?>">Volver a Jugar </a><!--Va en interfaz de ranking-->    