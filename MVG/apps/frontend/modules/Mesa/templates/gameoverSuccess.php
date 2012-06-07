<script type="text/javascript">
    $(document).ready(function() {  
        $("#jugar_again").button();
        $("#volver_a_jugar").button();
	
        //$("#videos_radios").buttonset();
        //$("#videos_radios1").button( { text: true, icons: {primary: "ui-icon-bullet"}}).click(function(){});	
        $( "#tabs" ).tabs();
        /*Evento de Tab2: Puntajes*/
        $("a[href=#tabs-2]").click(function()
        {            
            mostrarPuntaje();
        });
    });
</script>
<script type="text/javascript">
    var mesa_id="<?php echo $sf_user->getAttribute('mesaid'); ?>";
    var jug_id="<?php echo $sf_user->getAttribute('jugadorid'); ?>";  
    var puntaje_extra_obtenido=<?php echo $sf_user->getAttribute('puntaje_extra',0); ?>;
    //alert(puntaje_extra_obtenido);
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
    /*Muestra resultados de cada round*/
    function mostrarResultados(){                
        var request;
        request = createXMLHttpRequest();                        
        request.open('GET','<?php echo url_for('Mesa/consultarResultados'); ?>'+"?mesa_id="+mesa_id+"&jug_id="+jug_id,true);
        request.onreadystatechange=function(){                      
            if(request.readyState==4){
                if(request.status==200){                             
                    respuestajson=request.responseText;
                    if(respuestajson!=null){
                        var arr_buttons_data=JSON.parse(respuestajson);    
                        var resultados_buttons=$("#resultados_buttons");
                        //$("#videos_radios").buttonset(); 
                        var checked="";                        
                        for(i=0;i<arr_buttons_data.length;i++){   
                            if(i==0) checked="checked";
                            else checked="";
                            
                            $(function() {                                
                                var id_button=arr_buttons_data[i].id;
                                var elem=$("<input type='radio' name='videos_radios' id='videos_radios"+id_button+"' "+checked+" value='"+id_button+"' /><label for='videos_radios"+id_button+"' style='width: 99.5% !important; text-align: left;'> "+id_button+". "+arr_buttons_data[i].texto+"</label>");
                                elem.button( { text: true, icons: {primary: "ui-icon-bullet"}}).click(function(){ mostrarDetalles(id_button); });                                    
                                //resultados_buttons.buttonset('destroy');
                                resultados_buttons.append(elem);
                                //resultados_buttons.buttonset('');
                            });
                        } 
                        $("#resultados_buttons").buttonset('destroy').buttonset(); 
                        //$("#resultados_buttons").buttonset();                        
                        //$("#resultados_buttons :radio").buttonset(); 
                        //$("#videos_radios").buttonset("refresh");
                    }
                }
            }
        };
        request.send(null);
    }
    function mostrarDetalles(roundnum){
        //mostrar detalles video y etiquetas de ambos jugs de este round y mesa como Tu Y Tu compañero                
        var request;
        request = createXMLHttpRequest();                        
        request.open('GET','<?php echo url_for('Mesa/mostrarDetallesResultados'); ?>'+"?mesa_id="+mesa_id+"&jug_id="+jug_id+"&round_consultado="+roundnum,true);
        request.onreadystatechange=function(){                      
            if(request.readyState==4){
                if(request.status==200){                             
                    respuestajson=request.responseText;
                    if(respuestajson!=null){
                        var dataObj=JSON.parse(respuestajson);   
                        //var nombre1=dataObj.nombre1;
                        //var nombre2=dataObj.nombre2;
                        var url_video1=dataObj.url_video1;
                        var ini1=dataObj.ini1;
                        var fin1=dataObj.fin1;
                        var url_video2=dataObj.url_video2;
                        var ini2=dataObj.ini2;
                        var fin2=dataObj.fin2;
                        
                        //arreglos de etiquetas
                        var etiquetas1=dataObj.etiquetas1;
                        var etiquetas2=dataObj.etiquetas2;
                                                
                        $("#video_1 iframe").attr("src","http://www.youtube.com/embed/"+url_video1+"?rel=0&controls=0&border=0&egm=0&showinfo=0&showsearch=0");
                        $("#video_2 iframe").attr("src","http://www.youtube.com/embed/"+url_video2+"?rel=0&controls=0&border=0&egm=0&showinfo=0&showsearch=0");
                                    
                        $("#video_1 .video_details").html("<p>&lt;&lt;No hay etiquetas&gt;&gt;</p>");                       
                        for(i=0;i<etiquetas1.length;i++){
                            $("#video_1 .video_details").html("<p>"+etiquetas1[i]+"</p>");
                        }
                        
                        $("#video_2 .video_details").html("<p>&lt;&lt;No hay etiquetas&gt;&gt;</p>");                        
                        for(i=0;i<etiquetas2.length;i++){
                            $("#video_2 .video_details").html("<p>"+etiquetas2[i]+"</p>");
                        }
                        
                    }
                }
            }
        };
        request.send(null);
    }
    function mostrarPuntaje(){        
        var request;
        request = createXMLHttpRequest();                        
        request.open('GET','<?php echo url_for('Mesa/mostrarPuntajes'); ?>'+"?mesa_id="+mesa_id+"&jug_id="+jug_id+"&puntaje_extra="+puntaje_extra_obtenido,true);
        request.onreadystatechange=function(){                      
            if(request.readyState==4){
                if(request.status==200){                             
                    respuestajson=request.responseText;
                    if(respuestajson!=null){
                        var arr_puntajes=JSON.parse(respuestajson); 
                        var puntaje_total=0, puntaje_mesa=0, puntaje_extra=0, puntaje_mejor=0; 
                        if(arr_puntajes.puntaje_total!=null) puntaje_total=arr_puntajes.puntaje_total;                        
                        if(arr_puntajes.puntaje_mesa!=null) puntaje_mesa=arr_puntajes.puntaje_mesa;
                        if(arr_puntajes.puntaje_extra!=null) puntaje_extra=arr_puntajes.puntaje_extra;
                        if(arr_puntajes.puntaje_mejor!=null) puntaje_mejor=arr_puntajes.puntaje_mejor;
                        //alert(" - "+puntaje_total+" - "+puntaje_mesa+" - "+puntaje_extra+" - "+puntaje_mejor);
                        
                        $("#puntos_mesa_neto").html(puntaje_mesa+"");
                        $("#puntos_mesa").html((puntaje_mesa-puntaje_extra)+"");
                        $("#puntos_extra").html(puntaje_extra+"");
                        $("#puntos_total").html(puntaje_total+"");
                        $("#puntos_mejor").html(puntaje_mejor+"");
                    }
                }
            }
        };
        request.send(null);
    }
    //resultados rounds juego    
    window.onload = mostrarResultados;
</script>

<div id="wrapper">
    <div id="header">
        <h1>CazaVideos</h1>
        <p>Res&uacute;men del Juego</p>
    </div>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Res&uacute;men del Juego</a></li>
            <li><a href="#tabs-2">Puntaje</a></li>		
        </ul>
        <!--Tab Game Recap-->
        <div id="tabs-1" style="background: #D2BCD8;">	
            <div id="content1">                
                <h3>Resultados:</h3>			
                <span id="rounds">Rounds:</span>
                <div id="resultados_buttons">
                    <!--<input type="radio" name="videos_radios" id="videos_radios1" checked value="1" onclick=""><label for="videos_radios1" style="width: 99.5% !important; text-align: left;"> 1. Correcto </label> 
                    <input type="radio" name="videos_radios" id="videos_radios2" value="2" onclick=""><label for="videos_radios2" style="width: 99.5% !important; text-align: left;"> 2. Incorrecto </label>-->   
                </div>                
                <button id="jugar_again" onclick="">Volver a Jugar</button>
            </div>	
            <div id="content2">
                <h3>Detalle de los resultados:</h3>
                <div id="videos"><!--display:none ajax videos visto el checked display: block when checked-->
                    <div id="video_1" class="video_with_details" >
                        <h3>T&uacute;</h3>
                        <iframe width="200" height="131" src="http://www.youtube.com/embed/a_YR4dKArgo?rel=0&controls=0&border=0&egm=0&showinfo=0&showsearch=0" frameborder="0" allowfullscreen></iframe>
                        <div class="video_details">
                            <p>Tag 1 escrito</p>
                            <p>Tag 2 escrito</p>
                        </div>
                    </div>
                    <div id="video_2" class="video_with_details" >	
                        <h3>Tu compa&ntilde;ero</h3>
                        <iframe width="200" height="131" src="http://www.youtube.com/embed/_RRyniZG0Jo?rel=0&controls=0&border=0&egm=0&showinfo=0&showsearch=0" frameborder="0" allowfullscreen></iframe>
                        <div class="video_details">
                            <p>Tag 1 escrito</p>
                            <p>Tag 2 escrito</p>
                        </div>
                    </div>									
                </div>
            </div>	
            <div id="clear-fix" style="clear:both; width:100%"></div>
        </div>
        <!--Tab Scores-->
        <div id="tabs-2">
            <div id="tab2_contenido1">
                <div id="resultados_juego">
                    <div class="info_general"><p class="title">El puntaje de este juego:</p><div><p id="puntos_mesa_neto" class="info_resaltada estilo_numerico">80</p></div></div>
                    <div class="info_general"><p class="title">Tu CazaNivel es:</p><p class="info_nivel estilo_numerico"><span>Nivel 1</span>Novato</p></div>	
                    <div id="clear-fix" style="clear:both; width:100%"></div>
                </div>							
                <div id="tab2_menu">
                    <button id="volver_a_jugar" onclick="">Volver a Jugar</button>
                </div>
            </div>	
            <div id="tab2_contenido2">				
                <div class="score_info "><p>Tu puntaje:</p><p class="estilo_numerico" id="puntos_mesa">80</p></div>
                <div class="other_info "><p>Tu puntos extra:</p><p class="estilo_numerico" id="puntos_extra">80</p></div>
                <div class="score_info "><p>Tu total CazaPuntaje:</p><p class="estilo_numerico" id="puntos_total">80</p></div>
                <div class="other_info"><p>Puntaje del mejor jugador:</p><p class="estilo_numerico" id="puntos_mejor">80</p></div>
                <div id="clear-fix" style="clear:both; width:100%"></div>
            </div>	
            <div id="clear-fix" style="clear:both; width:100%"></div>
        </div>
    </div>

</div>