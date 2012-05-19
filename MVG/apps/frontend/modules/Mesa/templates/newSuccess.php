<html>
    <head>
        <title>WebSocket</title>
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
            
            alert(set_videos[0].inicio+" - "+set_videos[1].video_url);
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
                                //alert(respuesta_real+" "+video_url +" + "+ini+" + "+fin);
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
                                alert("se guardo!!");
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
                                alert("se guardo!!");
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
                alert(modoJugada);        
                if(modoJugada=="PAREJAS"){
                    websocket = io.connect("http://localhost:6969");
                    //Websockets: onconnect
                    websocket.on('connect', function () {
                        alert("iniciooo"); //consulta(status_socket); //Lo envía server sockets 
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
                                alert("Estamos buscándole un compañero de juego");
                            }else{                                
                                alert("Prueba juego habilitado");                            
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
                            alert("aquii same-different");
                            actualizarPuntaje(value);                            
                        }else{
                            logPartner("Received: "+value);
                        }                           
                    }); 
                    
                    //Websockets: ondisconnect
                    websocket.on('disconnect', function () {
                        alert("finnn, se cerró servidor");
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
                if(!msg){ alert("Message can not be empty"); return; }
                alert('{"tipo":"mensajes","objeto":{"'+mesa_id+'": "'+msg+'"}}');
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
            "ytapiplayer", "425", "356", "8", null, null, params, atts);
            
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
            function onytplayerStateChange(newState) {  
                var resp = document.getElementById("resp");
                resp.innerHTML="total: "+ytplayer.getVideoBytesTotal()+" - cargando: "+(ytplayer.getVideoBytesLoaded());
		//if(newState==0) ytplayer.playVideo(); //ninguno de estos estados			
                //Possible values are unstarted (-1), ended (0), playing (1), paused (2), buffering (3), video cued (5).
		if(ytplayer.getVideoBytesLoaded()>=ytplayer.getVideoBytesTotal() && ytplayer.getVideoBytesTotal()!=0){
                    //ytplayer.playVideo();
                    //mute(false);
                    //actualizar_intervalo_estado(newState);                    
                    //enviar mensaje de tipo sincronizacion-videos
                    //enviar_mensaje_interno("sincronizacion-videos","COMPLETO"); 
                    enviar_objeto("sincronizacion-videos",jug_id,"COMPLETO");
                    alerta("se cargo todo el video, lo envío a server sockets");
                }else{
                    //ytplayer.pauseVideo();	//pausado no hace buffer ràpido		                    
                    mostrarLoading();                    
                    mute(true);
                }
            } 
            
            /*Muestra una máscara loading*/
            function mostrarLoading(){
                $(document).ready(function(){  
                    $("#content").mask("Esperando se sincronicen los videos...");                    
                });
            } 
            /*Envía respuesta a servidor de socket, y guarda la etiqueta en la base*/
            function enviarRespuesta(respuesta){                
                //ENVIAR POR AJAX REQUERIMIENTO PARA GUARDAR LA RESPUESTA
                guardarRespuesta(respuesta);
                //ENVIAR A SOCKET_SERVER PARA ACTUALIZA LA RESPUESTA
                enviar_objeto('same-different',jug_id,''+respuesta); 
            }
            function jqueryfuns(){
                ( function($) {
                    $(document).ready(function(){  
                            $("#cmd_send").button();                    
                    });
                } ) ( jQuery );
            }
                
        </script>
    </head>
    <body onload="jqueryfuns(); init(); /*asignarVideo(round_actual-1);*/ "> 
        <p>Hola usuario: <strong><?php echo $sf_user->getAttribute('userid') ?></strong>.</p>    
        <div id="content">
            <div id="ytapiplayer">
                You need Flash player 8+ and JavaScript enabled to view this video.
            </div>
        </div>
        <p id="resp"></p>
        <a href="javascript:void(0);" onclick="play(0);">Same-Different</a>
        <input type="button" onclick="enviarRespuesta('SAME');" value="SAME" />
        <input type="button" onclick="enviarRespuesta('DIFFERENT');" value="DIFFERENT" />
        <h3>WebSocket v2.00</h3>
        <div id="prueba"></div>
        <div id="log"></div>
        <div id="logpartner"></div>
        <label>Message: </label><input type="text" id="txt_mensaje" onkeypress="enviar_texto(event);"/>
        <p id="timer"></p>
        <input type="button" id="cmd_send" onclick="send();" value="Enviar mensaje" />
        <a href="<?php echo url_for('Mesa/new'); ?>">Volver a Jugar </a>
    </body>
</html>