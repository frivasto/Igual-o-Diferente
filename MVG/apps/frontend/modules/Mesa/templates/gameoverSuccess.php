<script type="text/javascript">
    $(document).ready(function() {  
        $("#jugar_again").button();
        $("#volver_a_jugar").button();
	
        $("#videos_radios").buttonset();
        $("#videos_radios1").button( { text: true, icons: {primary: "ui-icon-bullet"}}).css({ height:10, width:185})
        .click(function(){});
        $('#videos_radios2').button( { text: true, icons: {primary: "ui-icon-bullet"}}).height(20)
        .click(function(){});
        $('#videos_radios3').button( { text: true, icons: {primary: "ui-icon-bullet"}}).height(20)
        .click(function(){});
        $('#videos_radios4').button( { text: true, icons: {primary: "ui-icon-bullet"}}).height(20)
        .click(function(){});		
        //$( "#menu_opciones" ).buttonset();	
        $( "#tabs" ).tabs();
    });
</script>
<script type="text/javascript">
    var mesa_id="<?php echo $sf_user->getAttribute('mesaid'); ?>";
    var jug_id="<?php echo $sf_user->getAttribute('jugadorid'); ?>";
    //alert(mesa_id+" - "+jug_id);
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
    function mostrarRespuestas(etiqueta_texto){                
        var request;
        request = createXMLHttpRequest();                        
        request.open('GET','<?php echo url_for('Mesa/consultarRespuestas'); ?>'+"?mesa_id="+mesa_id+"&jug_id="+jug_id,true);
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
    //resultados rounds juego    
    //window.onload = mostrarRespuestas;
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
        <div id="tabs-1">	
            <div id="content1">
                <div id="menu_opciones">				
                        <!--<input type="radio" id="menu_opciones1" name="menu_opciones" /><label for="menu_opciones1">Puntajes</label>
                        <input type="radio" id="menu_opciones2" name="menu_opciones" checked="checked" /><label for="menu_opciones2">Res&uacute;men</label>-->				
                </div>
                <h3>Resultados:</h3>			
                <span id="rounds">Rounds:</span>						
                <input type="radio" name="videos_radios" id="videos_radios1" checked value="1" onclick=""><label for="videos_radios1" style="width: 99.5% !important; text-align: left;"> 1. Correcto </label> 
                <input type="radio" name="videos_radios" id="videos_radios2" value="2" onclick=""><label for="videos_radios2" style="width: 99.5% !important; text-align: left;"> 2. Incorrecto </label> 
                <input type="radio" name="videos_radios" id="videos_radios3" value="3" onclick=""><label for="videos_radios3" style="width: 99.5% !important; text-align: left;"> 3. Incorrecto </label>
                <input type="radio" name="videos_radios" id="videos_radios4" value="4" onclick=""><label for="videos_radios4" style="width: 99.5% !important; text-align: left;"> 4. Incorrecto </label>
                <button id="jugar_again" onclick="">Volver a Jugar</button>
            </div>	
            <div id="content2">
                <h3>Detalle de los resultados:</h3>
                <div id="videos"><!--display:none ajax videos visto el checked display: block when checked-->
                    <div id="video_1" class="video_with_details" >
                        <h3 style="margin: 0px; padding: 5px 5px 2px 0px;">T&uacute;</h3>
                        <iframe width="200" height="131" src="http://www.youtube.com/embed/a_YR4dKArgo?rel=0&controls=0&border=0&egm=0&showinfo=0&showsearch=0" frameborder="0" allowfullscreen></iframe>
                        <div id="video_details">
                            <p>Tag 1 escrito</p>
                            <p>Tag 2 escrito</p>
                            <p>Tag 3 escrito</p>
                            <p>Tag 4 escrito</p>
                            <p>Tag 5 escrito</p>
                            <p>Tag 6 escrito</p>
                            <p>Tag 7 escrito</p>
                        </div>
                    </div>
                    <div id="video_2" class="video_with_details" >	
                        <h3 style="margin: 0px; padding: 5px 5px 2px 0px;">Tu compa&ntilde;ero</h3>
                        <iframe width="200" height="131" src="http://www.youtube.com/embed/_RRyniZG0Jo?rel=0&controls=0&border=0&egm=0&showinfo=0&showsearch=0" frameborder="0" allowfullscreen></iframe>
                        <div id="video_details">
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
                    <div class="info_general"><p class="title">El puntaje de este juego:</p><div><p class="info_resaltada estilo_numerico">80</p></div></div>
                    <div class="info_general"><p class="title">Tu CazaNivel es:</p><p class="info_nivel estilo_numerico"><span>Nivel 1</span>Novato</p></div>	
                    <div id="clear-fix" style="clear:both; width:100%"></div>
                </div>							
                <div id="tab2_menu">
                    <button id="volver_a_jugar" onclick="">Volver a Jugar</button>
                </div>
            </div>	
            <div id="tab2_contenido2">				
                <div class="score_info "><p>Tu puntaje:</p><p class="estilo_numerico">80</p></div>
                <div class="other_info "><p>Tu puntos extra:</p><p class="estilo_numerico">80</p></div>
                <div class="score_info "><p>Tu total CazaPuntaje:</p><p class="estilo_numerico">80</p></div>
                <div class="other_info"><p>Puntaje del mejor jugador:</p><p class="estilo_numerico">80</p></div>
                <div id="clear-fix" style="clear:both; width:100%"></div>
            </div>	
            <div id="clear-fix" style="clear:both; width:100%"></div>
        </div>
    </div>

</div>