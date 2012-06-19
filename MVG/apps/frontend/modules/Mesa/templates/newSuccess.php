<script type="text/javascript">
    ( function($) {
        $(document).ready(function() {
            $("#cmd_enviar").button().click(function(){ send(); });    
            /*$("input","#same_different").button();
            $("#same_different").buttonset();
            $("#same_different1").button( { text: true, icons: {primary: "ui-icon-bullet"}}).click(function(){ 
                $(this).attr('disabled','disabled');  
                $(this).addClass('ui-state-disabled');
                $(this).addClass('button-state-disabled');                
                enviarRespuesta('SAME');
            });
            $('#same_different2').button( { text: true, icons: {primary: "ui-icon-bullet"}}).click(function(){ 
                $(this).attr('disabled','disabled'); 
                $(this).addClass('ui-state-disabled');
                $(this).addClass('button-state-disabled');
                enviarRespuesta('DIFFERENT');
            });*/
            function desHabilitarSameDifferent(){
                $("#same_different1").attr('disabled','disabled');
                $("#same_different1").addClass('ui-state-disabled');
                $("#same_different2").attr('disabled','disabled');
                $("#same_different2").addClass('ui-state-disabled');
            }            
            $("#same_different1").button( { text: true, icons: {primary: "ui-icon-bullet"}}).removeClass('ui-corner-all').addClass('ui-corner-left').click(function(){ 
                desHabilitarSameDifferent();
                enviarRespuesta('SAME');                                              
            });            
            $('#same_different1').hover(
                function(){ $(this).addClass('otherbuttonhover') },
                function(){ $(this).removeClass('otherbuttonhover') }
            )
            $("#same_different2").button( { text: true, icons: {primary: "ui-icon-bullet"}}).removeClass('ui-corner-all').addClass('ui-corner-right').addClass('mybuttonclass').click(function(){ 
                desHabilitarSameDifferent();
                enviarRespuesta('DIFFERENT');                                             
            });
            $('#same_different2').hover(
                function(){ $(this).addClass('mybuttonclass_hover') },
                function(){ $(this).removeClass('mybuttonclass_hover') }
            )
            $( "#dialog_result" ).dialog({autoOpen: false, show: "blind", zIndex: 9999, hide: "explode", modal: true, position: ['center',260], title: 'Respuesta', dialogClass: 'alert', resizable: false});
            $( "#dialog_mensaje" ).dialog({autoOpen: false, show: "blind", zIndex: 9999, hide: "explode", modal: true, position: ['center',260], title: 'Respuesta', dialogClass: 'alert', resizable: false});    
            $( "#dialog_bonus" ).dialog({autoOpen: false, show: "blind", zIndex: 99999, hide: "explode", modal: false, position: ['left',130], title: 'Bono', dialogClass: 'bonus_alert', resizable: true, stack: true});            
            $('.alert div.ui-dialog-titlebar').hide();//transparent
            $('.alert').css('background','transparent');  
            $('.bonus_alert').css('background','black'); 
            $("#progress_3inrow").progressbar({ value: 0 });
        });
    } ) ( jQuery );
    
    
    function habilitarSameDifferent(){
        ( function($) {
            $("#same_different1").removeAttr("disabled");
            $("#same_different1").removeClass('ui-state-disabled');
            $("#same_different2").removeAttr("disabled");
            $("#same_different2").removeClass('ui-state-disabled');
        } ) ( jQuery );
    }
    
    /*
     *JQUERY CHRONY 
     *Con callback  otra opcion es enviar el texto completo text: '1:20:30' MEJOR!!
     *PERMITE REAJUSTAR DATOS TAMBIÉN $('#time').chrony('set', { decrement: 2 }); 
     *re-adjust runtime options.
     */
    function empezarTimerGlobal(){
        ( function($) {
            $('#timeglobal').chrony({hour: 0, minute: 5, second: 0,finish: function() {
                    $(this).html('Finished!');
                }, blink: true
            });
        } ) ( jQuery );
    }    
    
    function destroyTimerLocal(){
        $('#time').chrony('destroy');
        $("#time").remove();          
        $("#video_timer").empty();        
    }
    
    function empezarTimerLocal(){
        ( function($) {   
                      
            $("#video_timer").append("<div id='time' class='content_text_min' ></div>");    
            $('#time').chrony({hour: 0, minute: 0, second: 35,finish: function() {        
                    //$(this).html('Finished! '+envioDecision);
                    verificarEnvioRespuesta();   //evento same different automatico envíe "" si el usuario no ha contestado     
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
            var estan_emparejados=false;            
            <?php 
            $tmp=$sf_user->getAttribute('set_intervalos_videos'); $tam=count($tmp);                               
                for($i=0;$i<$tam;$i++){                                          
            ?>
            var tam_arr_php=<?php echo $tam; ?>;             
            var i=<?php echo $i; ?>;             
            var intervaloObj={};
            intervaloObj.inicio="<?php echo $tmp[$i][1]['ini']; ?>";
            intervaloObj.fin="<?php echo $tmp[$i][1]['fin']; ?>";
            intervaloObj.video_url="<?php echo $tmp[$i][1]['url']; ?>";
            intervaloObj.respuesta_real="<?php echo $tmp[$i][0]; ?>";
            set_videos[i]=intervaloObj;
            <?php } ?>
                       
            var video_actual=set_videos[round_actual].video_url;  
            var minuto_actual=set_videos[round_actual].inicio;
            
            /*Sino ha contestado este jugador, entonces enviar NO_CONTESTO*/
            function verificarEnvioRespuesta(){                
                if(!envioDecision) enviarRespuesta('NO_CONTESTO');
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
            function actualizarPuntuaciones(puntos, es_acertado){                
                var request;
                request = createXMLHttpRequest();               
                request.open('GET','<?php echo url_for('Mesa/actualizarPuntuaciones'); ?>'+"?puntos="+puntos+"&mesa_id="+mesa_id+"&jug_id="+jug_id+"&es_acertado="+es_acertado,true);
                request.onreadystatechange=function(){                      
                    if(request.readyState==4){
                        if(request.status==200){                             
                            respuestajson=request.responseText;
                            if(respuestajson!=null && respuestajson!=''){
                                actualizarInfo("inrow",respuestajson);
                                var obj = JSON.parse(respuestajson);
                                var puntaje_total=obj.puntaje_total;
                                var puntaje_mesa=obj.puntaje_mesa;
                                var msg_bonos=obj.msg_bonos;
                                var inrow_count=obj.inrow_count;
                                var inrow=obj.inrow;
                                
                                //ACTUALIZAR PUNTAJE EN LA INTERFAZ
                                actualizarInfo("puntajeglobal", ""+puntaje_mesa);
                                
                                //DESTROY TIMER
                                destroyTimerLocal();
                                
                                //REINICIAR ESTADO VIDEOS
                                enviar_objeto("reiniciar_estado_videos",jug_id,"");
                                
                                //ACTUALIZAR 3INROW //actualizarInfo("progress_3inrow",inrow_count);
                                inrow_count=parseInt(inrow_count);
                                $( "#progress_3inrow" ).progressbar( "option", "value", inrow_count*10 );
                                
                                actualizarInfo("inrow", ""+inrow+" - "+inrow_count);
                                
                                //VERIFICAR ACREEDOR A BONO
                                if(msg_bonos!=""){
                                    actualizarInfo("dialog_bonus_txt",msg_bonos);
                                    $( "#dialog_bonus" ).dialog("open");                                    
                                    setTimeout(function(){$( "#dialog_bonus" ).dialog("close")},1250);
                                } 
                                
                                //incrementar round
                                round_actual++;
                                
                                if(round_actual<set_videos.length){

                                    enMascarar("wrapper","Esperando se sincronicen los videos...");
                                    
                                    video_actual=set_videos[round_actual].video_url; 
                                    minuto_actual=set_videos[round_actual].inicio;
                                    iniciarVideo(video_actual,minuto_actual);

                                    //Limpiar variable Decision
                                    envioDecision=0;                                    

                                    //Limpiar LOG y LOGPARTNER
                                    document.getElementById("log").innerHTML="";
                                    document.getElementById("logpartner").innerHTML="";                                                                                                           
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
            /*Mostrar la alerta de Resultados*/
            function mostrarResultados(resultado_jug_tu, resultado_jug_partner, puntos){
                //****************** RESULTADOS y PASAR A SIGUIENTE ROUND ***************************                                                                                               
                //JUG1
                if(resultado_jug_tu=="ACIERTO") $("#respuesta_jug").attr({ src: "/images/check.png", alt: "Resultado Jug1" });
                else $("#respuesta_jug").attr({ src: "/images/cross.png", alt: "Resultado Jug1" });
                //JUG2
                if(resultado_jug_partner=="ACIERTO") $("#respuesta_jug_partner").attr({ src: "/images/check.png", alt: "Resultado Jug2" });
                else $("#respuesta_jug_partner").attr({ src: "/images/cross.png", alt: "Resultado Jug2" });

                //EDITAR PUNTAJE GRUPAL O RESULTADO_DECISIONES_COLABORATIVAS [mostrar en pantalla correcto, incorrecto por n seconds]
                if(puntos==100+"") $("#resultado_decision").html("Correcto");                                                                                                    
                else $("#resultado_decision").html("Incorrecto");                                
                $("#puntaje_grupal").html(puntos+"");

                //MOSTRAR RESULTADO 5000ms
                $( "#dialog_result" ).dialog( "open" );
                setTimeout(function(){$( "#dialog_result" ).dialog("close")},1000);                
            }             
            
            var veces_sincronizado=0;
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
                                //iniciarVideo(video_actual,0);
                                estan_emparejados=true;
                            }
                        }else if(obj.tipo=="sincronizacion-completa"){                            
                            //setTimeout(function(){                                 
                                desEnMascarar("wrapper");
                                veces_sincronizado++;
                                play(minuto_actual);
                                mute(false);  
                                actualizarInfo("sincronizado_msg","sincronizacion-completa "+veces_sincronizado+" veces, en round: "+(round_actual+1));
                                empezarTimerLocal(); //iniciar AQUI timer de ese round sincronizado                                
                                //habilitar Botones Same Different
                                habilitarSameDifferent();
                            //},0.007);
                        }else if(obj.tipo=="same-different-incompleto"){
                            //Open Cuadro de diálogo que indica que respondió su partner en una esquina y por n seconds                            
                            $( "#dialog_mensaje" ).dialog("open");
                            setTimeout(function(){$( "#dialog_mensaje" ).dialog("close")},1000);                            
                        
                        }else if(obj.tipo=="same-different"){                                                                                    
                            var puntaje_grupal=value.puntaje;
                            var resultado_jug_tu=value.jugtu;
                            var resultado_jug_partner=value.jugpartner; 
                           
                            var es_acertado=0;
                            if(resultado_jug_tu==resultado_jug_partner && resultado_jug_tu=="ACIERTO") es_acertado=1;
                            else es_acertado=0;
                            
                            puntaje_grupal=parseInt(puntaje_grupal);
                                                        
                            //Mostrar la alerta con los Resultados del Round sin acumular
                            mostrarResultados(resultado_jug_tu, resultado_jug_partner, puntaje_grupal);
                            
                            //guardar puntaje, acumularlo y Pasar a siguiente nivel
                            actualizarPuntuaciones(puntaje_grupal, es_acertado);                             
                            
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
                if(!msg){ 
                 //alert("Message can not be empty");
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
            var ytplayer;
            function onYouTubePlayerReady(playerId) {
                ytplayer = document.getElementById("myytplayer");				
                ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
                iniciarVideo(video_actual,minuto_actual);
            }           
            
            /*Iniciar el video de cada round en mute*/
            function iniciarVideo(video,inicio_min) {		
                if (ytplayer) {
                    cueVideo(video,inicio_min,"small"); //calidad más baja
                    play(inicio_min);
                    mute(true);
                }
            }  
            
            function cueVideo(video_url,startSeconds,suggestedQuality) {		
                if (ytplayer) {
                    ytplayer.cueVideoById(video_url,startSeconds,suggestedQuality);	
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
            var cont=0;
            function onytplayerStateChange(newState) {  
                actualizarInfo("resp","total: "+ytplayer.getVideoBytesTotal()+" - cargando: "+(ytplayer.getVideoBytesLoaded())+" - estado: "+newState+" - estanemparejados: "+estan_emparejados);                					
                //Possible values are unstarted (-1), ended (0), playing (1), paused (2), buffering (3), video cued (5).		
                //if(newState!=-1 && newState!=5) {  
                //if(estan_emparejados && newState!=-1 && newState!=5) {
                if(newState!=-1 && newState!=5) {
                    //se cargo todo el video, lo envío a server sockets               
                    enviar_objeto("sincronizacion-videos",jug_id,"COMPLETO");                 
                    cont++;
                    actualizarInfo("loaded", "Estado Video:: "+newState+" se cargo todo el video, lo envío a server sockets "+cont);                                                         
                }              
            } 
            
            /*Muestra una máscara loading con el mensaje indicado y sobre el contenedor id*/            
            function enMascarar(id_contenedor,mensaje){
                ( function($) {
                $(document).ready(function(){  
                    $("#"+id_contenedor).mask(mensaje);                    
                });
                } ) ( jQuery );
            } 
            
            /*Quita la máscara loading del contenedor id*/            
            function desEnMascarar(id_contenedor){
                ( function($) {
                $(document).ready(function(){  
                    $("#"+id_contenedor).unmask();                    
                });
                } ) ( jQuery );
            } 
            
            /*Devuelve la respuesta real del video del round indicado*/
            function obtenerRespuestaReal(round){
                var rsp_real=set_videos[round].respuesta_real;
                if(rsp_real==1) rsp_real="SAME";
                else rsp_real="DIFFERENT";
                return rsp_real;
            }
            
            /*Envía respuesta a servidor de socket, y guarda la etiqueta en la base*/
            function enviarRespuesta(respuesta){                
                //ENVIAR POR AJAX REQUERIMIENTO PARA GUARDAR LA RESPUESTA
                guardarRespuesta(respuesta);
                
                var respuesta_real=obtenerRespuestaReal(round_actual);                
                if(respuesta==respuesta_real) respuesta='ACIERTO';
                //else respuesta='NO_ACIERTO';
                
                //ENVIAR A SOCKET_SERVER PARA ACTUALIZAR LA RESPUESTA
                enviar_objeto('same-different',jug_id,''+respuesta); 
                envioDecision=1;                
            }

            /*Modifica el texto de un elemento HTML*/
            function actualizarInfo(id_contenedor, info_msg){                
                //var contenedor= document.getElementById(id_contenedor);                
                //contenedor.innerHTML=info_msg;
                $("#"+id_contenedor).html(info_msg);
            }
            
            function inicializar(){
                init();
                empezarTimerGlobal();                
                enMascarar("wrapper","Esperando se sincronicen los videos...");
            }
            window.onload = inicializar;
        </script>

<p style="color:red; text-align: right;" id="inrow">3inRow: </p>        
<div id="wrapper">        
        <div id="progress_3inrow"></div>
	<div id="header">
		<h1>CazaVideos</h1>
		<p>Divi&eacute;rtete</p>
		<div id="timer_content">
                    <h3>Tiempo:</h3>
                    <div id="timeglobal" class="content_text" ></div>
                    <div id="clear-fix" style="clear:both; width:100%"></div>                                        
		</div>
		<div id="puntos_content">
			<h3>Puntos:</h3>
			<h3 class="content_text" id="puntajeglobal">0</h3>
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
				<!--<input type="radio" name="same_different" id="same_different1" checked value="1" /><label for="same_different1" > Igual </label>
				<input type="radio" name="same_different" id="same_different2" value="2" /><label for="same_different2" > Diferente </label>-->                               
                                <input type="button" id="same_different1" value="Igual"/>   
                                <input type="button" id="same_different2" value="Diferente"/>
			</div>
                        <div id="video_principal">                           
                            <div id="content">
                                <div id="ytapiplayer">
                                    You need Flash player 8+ and JavaScript enabled to view this video.
                                </div>
                                <div id="video_timer">
                                    <!--Delete and Create again and add plugin-->
                                    <div id="time" class="content_text_min" ></div>
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
		<div style="float:left; padding:0px 25px;"><h3 style="text-align:center; font-size:20px; color:white;" id="resultado_decision" >?</h3><p id="puntaje_grupal" ></p></div>
		<div style="float:left;"><p style="font-size:10px; font-weight:bold;">Tu compa&ntilde;ero</p><img id="respuesta_jug_partner" src="/images/icon_checked.png" width="16px" /></div>
	</div>
        <div id="dialog_mensaje" title="Basic dialog" style="display:none; background: #333; min-height: 0px !important; opacity:0.95; filter:alpha(opacity=95); ">	
		<div style="float:left; padding:0px 25px;"><h3 style="text-align:center; font-size:20px; color:white;" >Tu compa&ntilde;ero acaba de contestar</h3></div>		
	</div>
        <div id="dialog_bonus" title="Basic dialog" style="display:none; background: #333; min-height: 0px !important;">	
		<div style="float:left; padding:0px 25px;"><h3 style="text-align:center; font-size:20px; color:white;" id="dialog_bonus_txt" >Felicitaciones ganaste un Bono!</h3></div>		
	</div>
</div>
<!--Fin de Interfaz-->
        <p style="color:black;">Hola usuario: <strong><?php echo $sf_user->getAttribute('userid') ?></strong>.</p>            
        <p style="color:black;" id="resp"></p>
        <p style="color:black;" id="loaded"></p>
        
        <p style="color:black;" id="sincronizado_msg"></p>
        <a href="<?php echo url_for('Mesa/new'); ?>">Volver a Jugar </a><!--Va en interfaz de ranking-->    