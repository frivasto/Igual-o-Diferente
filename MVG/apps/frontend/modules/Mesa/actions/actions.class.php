<?php

/**
 * Mesa actions.
 *
 * @package    MusicVideoGame
 * @subpackage Mesa
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MesaActions extends sfActions {
    public function executeIndex(sfWebRequest $request) {
        $this->mesas = Doctrine_Core::getTable('Mesa')
                ->createQuery('a')
                ->execute();
    }

    //EMPAREJAR Y GENERAR SET DE VIDEOS DE LA MESA Observacion ESTADO: MESA 0:Incompleta 1:Completa JUGADOR 0:Disponible 1:No disponible Ocupado RESPUESTAREAL EN RELACIONMESAVIDEO 1:Same 0:Different    
    public function executeEmparejar(sfWebRequest $request) {
        
        
        
        $jugador_pareja_id = 0;
        $modoJugada = '';
        $user_actual = $this->getUser()->getAttribute('userid');  //sacar de session el user_id único de facebook
        $mesa_id=0;       
      //  $date1 = time();
        $min = 0;
        $date1 = 0;
        $date2 = 0;
        $esta_completa=false;
        $mesa_tmp=NULL;
        
        //&& $jugadorObj->getEstado()!=1        
        //MIENTRAS NO HAYA PASADO TIEMPO ESPERA Y JUG REAL NO CONSEGUIDO        
        while ($min<=10 && $jugador_pareja_id == 0 && !$esta_completa) {
            //OBTENER PAREJA REAL
            $mesa_y_jug=Mesa::obtenerParejaJuego($user_actual,$mesa_id);
            $jugador_pareja_id=$mesa_y_jug[0];
            $mesa_id=$mesa_y_jug[1];
            $mesa_tmp=Mesa::getMesaxId($mesa_id);
            if($mesa_tmp->getEstado()==1)
                $esta_completa=true;
            $date1 = $mesa_tmp->getTiempoEmparejar();
            $date2= time();
            //$min = round(abs(strtotime($date2) - strtotime($date1)) / 60,2);
            $min = ($date2 - $date1);
        }       
        
        //NUNCA CONSIGUIÓ JUG REAL
        $jugadorObj=Jugador::getJugadorByUserId($user_actual);
        $jugador_actual_id=$jugadorObj->getId();
        
        //OBTENER JUGADOR COMPANIERO DE MESA ID (x condicion no devuielve el correcto)
        //************ SELECT `jugador1_id` FROM `mesa` WHERE `jugador2_id`=2 UNION SELECT `jugador2_id` FROM `mesa` WHERE `jugador1_id`=2
        $jugador1=$mesa_tmp->getJugador1Id();
        $jugador2=$mesa_tmp->getJugador2Id();
        if($jugador1!=NULL && $jugador2!=NULL){
            if($jugador1==$jugador_actual_id){
                //REENPLAZAR JUG_PAREJA_ID
                $jugador_pareja_id=$jugador2;
            }else{
                $jugador_pareja_id=$jugador1;
            }
        }else{
            //PARA QUE LE ASIGNEN BOT
            $jugador_pareja_id=0; 
        }
        //**************************************
                
        if ($jugador_pareja_id == 0) {
            $modoJugada = 'BOT';
            //DEVOLVER EL JUG BOT
            $jugadorBOT = Jugador::getJugador('BOT-001', 'BOT');           
            if (!empty($jugadorBOT)) {
                $jugadorBOT_id = $jugadorBOT->getId();
            }
            //ponerlo en la mesa de jugador actual            
            $mesa = Mesa::getMesaxId($mesa_id);
            $mesa->setJugador2Id($jugadorBOT_id); // El q me escogieron: BOT
            $mesa->setEstado(1); //COMPLETA
            $mesa->save();
            
            //echo "mesa bot jug ".$jugador_pareja_id." de mesa ".$mesa_id. " min:".$min ."_jugador:". $user_actual. "_estado_mesa:".$mesa_tmp->getEstado() ."clases: ".get_class($min).get_class($date1).get_class($date2); die();
            echo "mesa bot jug ".$jugador_pareja_id." de mesa ".$mesa_id. " min:".$min ."_jugador:". $user_actual. "_estado_mesa:".$mesa_tmp->getEstado() ."clases: ".$date1; die();
            $jugador_pareja_id=$jugadorBOT_id; //PAREJA ES EL 
            
            //GENERAR SET DE VIDEOS DE ESTA MESA COMPLETANDO PARA ESTE JUGADOR PAREJA O JUGADOR BOT
            RelacionMesaVideo::generarSetVideos($mesa_id,$jugador_pareja_id);
            
        } else if ($jugador_pareja_id != 0){
            $modoJugada = 'PAREJAS';               
        }else{
            $modoJugada = 'ERROR';
        }
        
                        
        //OBTENER EL ARRAY DE RELACIONESMESAVIDEO EN FORMATO LISTO PARA JSON
        $set_intervalos_videos=RelacionMesaVideo::toJsonArray($mesa_id,$jugador_actual_id);
        
        //echo "jugador es: ".$jugador_actual_id." partner: ".$jugador_pareja_id;
        //print_r($set_intervalos_videos); 
        //echo $set_intervalos_videos[0][1]['url']; die();
        //$json_arr=json_encode($set_intervalos_videos);
        //echo $json_arr; die();
        
        //PONER EN SESSION LOS INTERVALOS DE VIDEOS PARA EL JUEGO
        $this->getUser()->setAttribute('set_intervalos_videos', $set_intervalos_videos);
    
        //PONER EN SESSION EL MODO DE JUGADA        
        $this->getUser()->setAttribute('modoJugada', $modoJugada);

        //PONER EN SESSION LA MESAID       
        $this->getUser()->setAttribute('mesaid', $mesa_id);   
        
        //PONER EN SESSION JUG ACTUAL ID       
        $this->getUser()->setAttribute('jugadorid', $jugador_actual_id); 
        
        //PONER EN SESSION ROUND       
        $this->getUser()->setAttribute('round_actual', 1);
        
        //REDIRECCIONAR A PÁGINA JUEGO
        $this->redirect('Mesa/new');
    }
    
    //OBTENER VIDEO DEL ROUND Para ambos modos de jugada, obtener video
    public function executeObtenerVideoRound(sfWebRequest $request) {
        $tmp = $request->getParameter('round_index');
        $round_index = isset($tmp) ? $tmp : '';

        $url_video='';
        $respuesta_real=''; 
        $inicio_video=0;
        $fin_video=0;
        
        //SACAR LOS VIDEOS DE SESSION ROUND   
        $set_intervalos_videos = $this->getUser()->getAttribute('set_intervalos_videos', "");
        if($round_index>=0){
            $respuesta_real=$set_intervalos_videos[$round_index][0];
            $url_video=$set_intervalos_videos[$round_index][1]['url'];
            $inicio_video=$set_intervalos_videos[$round_index][1]['ini'];
            $fin_video=$set_intervalos_videos[$round_index][1]['fin'];            
        }
        //Devolver JSON con estos datos
        $response = array();        
        $response['video_url'] = $url_video;
        $response['inicio'] = $inicio_video;
        $response['fin'] = $fin_video;
        $response['respuesta_real'] = $respuesta_real; 
        
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
        return $this->renderText(json_encode($response));
    }
    
    public function executeInsertarDecision(sfWebRequest $request) {
        $tmp = $request->getParameter('respuesta');
        $respuesta = isset($tmp) ? $tmp : '';

        $tmp = $request->getParameter('hora');
        $hora = isset($tmp) ? $tmp : '';

        $tmp = $request->getParameter('minuto');
        $minuto = isset($tmp) ? $tmp : '';

        $tmp = $request->getParameter('segundo');
        $segundo = isset($tmp) ? $tmp : '';
        
        $tmp = $request->getParameter('mesa_id');
        $mesa_id = isset($tmp) ? $tmp : '';
        
        $tmp = $request->getParameter('jug_id');
        $jug_id = isset($tmp) ? $tmp : '';
        
        $tmp = $request->getParameter('round_num');
        $round_num = isset($tmp) ? $tmp : '';

        $id_decision = 0;
        if ($respuesta != '' && $hora != '' && $minuto != '' && $segundo != '') {            
            $decision = new Decision();
            $relacionmesavideo= RelacionMesaVideo::getRelacionMesaVideo($mesa_id, $jug_id, $round_num+1);
            if($relacionmesavideo!=NULL){
                $relacionmesavideo_id=$relacionmesavideo->getId();            
                $decision->setRelacionmesavideoId($relacionmesavideo_id);
                $decision->setRespuesta($respuesta);
                $decision->setTiempo(date("H:i:s", strtotime(' ', mktime($hora, $minuto, $segundo, 0, 0, 0)))); //convert time
                $decision->save();
                $id_decision = $decision->getId();
            }
        }
        return $this->renderText("" + $id_decision);
    }
    
    
    public function executeActualizarPuntaje(sfWebRequest $request) {
        $tmp = $request->getParameter('mesa_id');
        $mesa_id = isset($tmp) ? $tmp : '';
        
        $tmp = $request->getParameter('jug_id');
        $jug_id = isset($tmp) ? $tmp : '';
        
        $tmp = $request->getParameter('puntos');
        $puntos = isset($tmp) ? $tmp : '';
        
        $id_puntajeObj = 0;
        $puntajeObj=Puntaje::getPuntajeXMesaJugId($mesa_id, $jug_id);
        
        if($puntajeObj==NULL){
            $puntajeObj=new Puntaje();
            $puntajeObj->setMesaId($mesa_id);
            $puntajeObj->setJugadorId($jug_id);
            $puntajeObj->save();            
        }
        
        $puntajeObj->setPuntaje($puntajeObj->getPuntaje()+$puntos);
        $puntajeObj->save(); 
        $id_puntajeObj = $puntajeObj->getId();
        
        return $this->renderText("" + $id_puntajeObj);        
    }
    
    //INCOMPLETO
    public function executeInsertarEtiqueta(sfWebRequest $request) {
        $tmp = $request->getParameter('etiqueta_texto');
        $etiqueta_texto = isset($tmp) ? $tmp : '';

        $tmp = $request->getParameter('hora');
        $hora = isset($tmp) ? $tmp : '';

        $tmp = $request->getParameter('minuto');
        $minuto = isset($tmp) ? $tmp : '';

        $tmp = $request->getParameter('segundo');
        $segundo = isset($tmp) ? $tmp : '';

        $tmp = $request->getParameter('mesa_id');
        $mesa_id = isset($tmp) ? $tmp : '';
        
        $tmp = $request->getParameter('jug_id');
        $jug_id = isset($tmp) ? $tmp : '';
        
        $tmp = $request->getParameter('round_num');
        $round_num = isset($tmp) ? $tmp : '';
        
        $id_etiqueta = 0;
        //$response = array();
        if ($etiqueta_texto != '' && $hora != '' && $minuto != '' && $segundo != '') {
            //calificar, insertarla con el tiempo
            $etiqueta = new InstanciaEtiqueta();
            $relacionmesavideo= RelacionMesaVideo::getRelacionMesaVideo($mesa_id, $jug_id, $round_num+1);
            if($relacionmesavideo!=NULL){
                $relacionmesavideo_id=$relacionmesavideo->getId();  
                $etiqueta->setRelacionmesavideoId($relacionmesavideo_id); //*IMPORTANTE* ojo HAY QUE PASAR RELACIONMESAVIDEO
                $etiqueta->setTexto($etiqueta_texto);
                $etiqueta->setTiempo(date("H:i:s", strtotime(' ', mktime($hora, $minuto, $segundo, 0, 0, 0)))); //convert time
                $etiqueta->save();
                $id_etiqueta = $etiqueta->getId();
            }
        }
        return $this->renderText("" + $id_etiqueta);
    }

    /* Link Volver a Jugar: Remueve session y va Iniciar */
    public function executeVolverAJugar(sfWebRequest $request) {
        //REMOVER DATOS DE SESSION
        $this->getUser()->getAttributeHolder()->clear();
        /*
        if(isset($this->getUser()->getAttribute('set_intervalos_videos')))
            $this->getUser()->getAttributeHolder()->remove('set_intervalos_videos');
        if(isset($this->getUser()->getAttribute('jugadorid')))
            $this->getUser()->getAttributeHolder()->remove('jugadorid');
        if(isset($this->getUser()->getAttribute('mesaid')))
            $this->getUser()->getAttributeHolder()->remove('mesaid');
        if(isset($this->getUser()->getAttribute('round_actual')))
            $this->getUser()->getAttributeHolder()->remove('round_actual');
        if(isset($this->getUser()->getAttribute('modoJugada')))
            $this->getUser()->getAttributeHolder()->remove('modoJugada');*/        
        //VOLVER A INICIAR
        $this->redirect('Mesa/index');
    }
    
    /* Link Iniciar */
    public function executeNew(sfWebRequest $request) {
        
    }

    public function executeCreate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfRequest::POST));
        $this->form = new MesaForm();
        $this->processForm($request, $this->form);
        $this->setTemplate('new');
    }

    public function executeEdit(sfWebRequest $request) {
        $this->forward404Unless($mesa = Doctrine_Core::getTable('Mesa')->find(array($request->getParameter('id'))), sprintf('Object mesa does not exist (%s).', $request->getParameter('id')));
        $this->form = new MesaForm($mesa);
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
        $this->forward404Unless($mesa = Doctrine_Core::getTable('Mesa')->find(array($request->getParameter('id'))), sprintf('Object mesa does not exist (%s).', $request->getParameter('id')));
        $this->form = new MesaForm($mesa);
        $this->processForm($request, $this->form);
        $this->setTemplate('edit');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();
        $this->forward404Unless($mesa = Doctrine_Core::getTable('Mesa')->find(array($request->getParameter('id'))), sprintf('Object mesa does not exist (%s).', $request->getParameter('id')));
        $mesa->delete();
        $this->redirect('Mesa/index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            $mesa = $form->save();
            $this->redirect('Mesa/edit?id=' . $mesa->getId());
        }
    }

}

?>