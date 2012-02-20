<html>
    <head><title>Ajax</title>
        <script type="text/javascript">
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
            function consulta(){
                var request;
                request = createXMLHttpRequest( );
                request.open('GET','<?php echo url_for('ejemplo1/ajax'); ?>'+"?msg="+5,true);
                request.onreadystatechange=function(){                      
                    if(request.readyState==4){
                        if(request.status==200){                             
                            manejador(request);                            
                        }
                    }
                };
                request.send(null);
            }
            function manejador(xhr)
            {	
		var resp2=xhr.responseText;
                var div=document.getElementById("respajax");
                div.innerHTML=resp2;
            }
        </script>
    </head>
    <body>
        <input type="button" value="clickea" onclick="consulta();" />
        <div id="respajax"></div>
    </body>
</html>    