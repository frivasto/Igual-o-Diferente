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
                                var myObject = eval('(' + respuestajson + ')');
                                var keys=Object.keys(myObject.objeto);
                                mesa_id=keys[0]; //actualizar mesa_id de este usuario para enviarlo
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
                                    //consulta(status_socket); //ajax edita el resultado  y lo envía server sockets        
                                    connected=true;
                                };                
                                socket.onmessage = function(msg){             
                                    var myObject = eval('(' + msg.data + ')');
                                    //Verificar si mensaje es de tipo conexion o mensaje //Dendiendo del tipo de mensaje enviado 
                                    /*
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
                                    } */       
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
                    consulta_guardarEtiqueta(msg);      
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
    </head>
    <!-- -->
    <body onload="init(); "> 
        <p>
            Hola usuario: <strong><?php echo $sf_user->getAttribute('userid') ?></strong>.
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
        <a href="<?php echo url_for('Mesa/new'); ?>">Volver a Jugar </a>
    </body>
</html>