<script type="text/javascript">          
    function mostrarLoading(){
        ( function($) {
        $(document).ready(function(){  
            $("#content").mask("Estamos busc&aacute;ndole un compa&ntilde;ero de juego...");
            //$("#content").unmask();
        });
        } ) ( jQuery );
    }                   
</script>
<div id="content">
    <p>
        Bienvenido <strong><?php echo $sf_user->getAttribute('userid') ?></strong>, Las reglas son: Hay que taggear el video.
    </p>
    <p><a onclick="mostrarLoading();" href="<?php echo url_for('Mesa/emparejar'); ?>">Iniciar</a></p>
</div>
