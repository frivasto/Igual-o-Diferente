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

    //Observacion ESTADO: MESA 0:Incompleta 1:Completa JUGADOR 0:Disponible 1:No disponible Ocupado RESPUESTAREAL EN RELACIONMESAVIDEO 1:Same 0:Different    
    public function executeEmparejar(sfWebRequest $request) {                
        $jugador_pareja_id = 0;
        $modoJugada = '';
        $user_actual = $this->getUser()->getAttribute('userid');  //sacar de session el user_id único de facebook
        $mesa_id=0;       
        $date1 = time();
        $min = 0;
        $esta_completa=false;
        
        //$jugadorObj=Jugador::getJugadorByUserId($user_actual);
        //&& $jugadorObj->getEstado()!=1        
        //MIENTRAS NO HAYA PASADO TIEMPO ESPERA Y JUG REAL NO CONSEGUIDO        
        while ($min<=0.35 && $jugador_pareja_id == 0 && !$esta_completa) {
            //OBTENER PAREJA REAL
            $mesa_y_jug=Mesa::obtenerParejaJuego($user_actual,$mesa_id);
            $jugador_pareja_id=$mesa_y_jug[0];
            $mesa_id=$mesa_y_jug[1];
            $mesa_tmp=Mesa::getMesaxId($mesa_id);
            if($mesa_tmp->getEstado()==1)
                $esta_completa=true;
            $date2 = time();
            $min = ($date2 - $date1) / 60;           
        }       
        //NUNCA CONSIGUIÓ JUG REAL        
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
            
        } else if ($jugador_pareja_id != 0){
            $modoJugada = 'PAREJAS';                        
        }else{
            $modoJugada = 'ERROR';
        }
                
        //PONER EN SESSION LA COLECCION DE VIDEOS PARA TODO EL JUEGO
        $mesa = Mesa::getMesaxId($mesa_id);
        $coleccion_id=$mesa->getColeccionId(); //COMPLETA
        if($coleccion_id==NULL){            
            $coleccion_id=Coleccion::obtenerColeccionAleatoria();
            $item_colecciones_array= ItemColeccion::getItemsColeccion($coleccion_id);
            $this->getUser()->setAttribute('colecciones_item', $item_colecciones_array);
            //setear esta coleccion generada en la mesa                        
            $mesa->setColeccionId($coleccion_id); //COMPLETA
            $mesa->save();
        }
            
        //PONER EN SESSION EL MODO DE JUGADA        
        $this->getUser()->setAttribute('modoJugada', $modoJugada);

        //PONER EN SESSION LA MESAID       
        $this->getUser()->setAttribute('mesaid', $mesa_id);        
        
        //REDIRECCIONAR A PÁGINA JUEGO
        $this->redirect('Mesa/new');
    }

    //Para ambos modos de jugada, obtener video
    public function executeObtenerVideoRound(sfWebRequest $request) {
        $tmp = $request->getParameter('round_index');
        $round_index = isset($tmp) ? $tmp : '';

        $tmp = $request->getParameter('jugador_index');
        $jugador_index = isset($tmp) ? $tmp : '';

        $url_video='';
        $respuesta_real='';        
        //SACAR LOS VIDEOS DE SESSION  ROUND Y JUG (1 o 2) (DETERMINARLO O YA SE PONGFA ANTES)     
        $item_colecciones_array = $this->getUser()->getAttribute('colecciones_item', "");
        if($round_index!=0 && $jugador_index!=0){
            $id_video=$item_colecciones_array[$round_index]['video'.$jugador_index];
            $url_video=Video::getVideoxId($id_video)->getUrl();
            $respuesta_real=$item_colecciones_array[$round_index]['respuesta_real'];
        }
        //Devolver JSON con estos datos
        $response = array();        
        $response['video_url'] = $url_video;
        $response['respuesta_real'] = $respuesta_real;        
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
        return $this->renderText(json_encode($response));
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

        $id_etiqueta = 0;
        //$response = array();
        if ($etiqueta_texto != '' && $hora != '' && $minuto != '' && $segundo != '') {
            //calificar, insertarla con el tiempo
            $etiqueta = new InstanciaEtiqueta();
            //sacar de session su relacionmesavideo_id
            $relacionmesavideo_id = $this->getUser()->getAttribute('relacionmesavideo_id', "");
            //$relacionmesavideo_id=$request->getParameter('relacionmesavideo_id');
            $etiqueta->setRelacionmesavideoId($relacionmesavideo_id); //*IMPORTANTE* ojo HAY QUE PASAR RELACIONMESAVIDEO
            $etiqueta->setTexto($etiqueta_texto);
            $etiqueta->setTiempo(date("H:i:s", strtotime(' ', mktime($hora, $minuto, $segundo, 0, 0, 0)))); //convert time
            $etiqueta->save();
            $id_etiqueta = $etiqueta->getId();
        }
        //Devolver JSON con estos datos
        //$response['puntaje'] = "identificacion"; //RETORNAR MENSAJES DE TIPO ? Y PUNTAJE
        //$this->getResponse()->setHttpHeader('Content-type', 'application/json');
        //return $this->renderText(json_encode($response));
        return $this->renderText("" + $id_etiqueta);
    }

    //actualizarIntervaloEstado
    public function executeActualizarIntervaloEstado(sfWebRequest $request) {
        $tmp = $request->getParameter('estado');
        $estado = isset($tmp) ? $tmp : '';
        $tmp = $request->getParameter('mesa_id');
        $mesa_id = isset($tmp) ? $tmp : '';
        $tmp = $request->getParameter('num_round');
        $num_round = isset($tmp) ? $tmp : '';
        $rsp = '';
        $response = array();

        $user_id_facebook = $this->getUser()->getAttribute('userid', "");
        $jugador_actual = Jugador::getJugadorByUserId($user_id_facebook);
        $id_jug = $jugador_actual->getId();

        if ($estado != '' && $mesa_id != '' && $num_round != '') {
            $mesa = Mesa::getMesaxId($mesa_id);
            $estado_mesa = $mesa->getEstado();
            if ($estado_mesa == 0) {
                //Incompleta
                $relacion_mesa_vid = RelacionMesaVideo::getRelacionMesaVideo($mesa_id, $id_jug, $num_round);
                $relacion_mesa_vid->setVideoIntervaloEstado(1); //1: loaded 0 no loaded
                $relacion_mesa_vid->save();
                $rsp = 'INCOMPLETO';
            } else if ($estado_mesa == 1) {
                //Completa
                $id_jug1 = $mesa->getJugador1Id();
                $id_jug2 = $mesa->getJugador2Id();

                //relaciones mesavideo
                $relacion_mesa_vid_jug1 = RelacionMesaVideo::getRelacionMesaVideo($mesa_id, $id_jug1, $num_round);
                $relacion_mesa_vid_jug2 = RelacionMesaVideo::getRelacionMesaVideo($mesa_id, $id_jug2, $num_round);

                //Se actualiza la relación mesa video del usuario actualiza
                if ($id_jug == $id_jug1) {
                    $relacion_mesa_vid_jug1->setVideoIntervaloEstado(1); //1: loaded 0 no loaded 
                    $relacion_mesa_vid_jug1->save();
                } else {
                    $relacion_mesa_vid_jug2->setVideoIntervaloEstado(1); //1: loaded 0 no loaded  
                    $relacion_mesa_vid_jug2->save();
                }

                //Ahora cual es el estado de los videos
                if ($relacion_mesa_vid_jug1->getNumRound() == 1 && $relacion_mesa_vid_jug2->getNumRound() == 1) {
                    $rsp = 'COMPLETO'; // en este momento es posible coincidan él último va llegar aquí a confirmar
                } else {
                    $rsp = 'INCOMPLETO';
                }
            }
        }
        //Devolver JSON con estos datos
        $response['respuesta'] = $rsp; //RETORNAR MENSAJES DE TIPO ? Y PUNTAJE
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
        return $this->renderText(json_encode($response));
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