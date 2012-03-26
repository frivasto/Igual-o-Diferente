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

    public function executeComet(sfWebRequest $request) {
        $filename = dirname(__FILE__) . '/data.txt';
        $tmp = $request->getParameter('msg');
        $msg = isset($tmp) ? $tmp : '';
        if ($msg != '') {
            file_put_contents($filename, $msg);
            die();
        }
        // infinite loop until the data file is not modified
        $tmp = $request->getParameter('timestamp');
        $lastmodif = isset($tmp) ? $tmp : 0;
        $currentmodif = filemtime($filename);

        while ($currentmodif <= $lastmodif) { // check if the data file has been modified
            usleep(10000); // sleep 10ms to unload the CPU
            clearstatcache();
            $currentmodif = filemtime($filename);
        }
        $response = array();
        $response['msg'] = file_get_contents($filename);
        $response['timestamp'] = $currentmodif;
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
        return $this->renderText(json_encode($response));

        flush();
        //return sfView::NONE;
    }

    public function executeIndex(sfWebRequest $request) {
        $this->mesas = Doctrine_Core::getTable('Mesa')
                ->createQuery('a')
                ->execute();
    }

    //Observacion ESTADO: MESA 0:Incompleta 1:Completa JUGADOR 0:Disponible 1:No disponible Ocupado RESPUESTAREAL EN RELACIONMESAVIDEO 1:Same 0:Different
    public function executeEmparejar(sfWebRequest $request) {
        $tmp = $request->getParameter('estado');
        $estado = isset($tmp) ? $tmp : '';
        $response = array();
        if ($estado != '') {
            if ($estado == 1) { //estado ok de los sockets 1 OPEN                
                $user_actual = $this->getUser()->getAttribute('userid');  //sacar de session el user_id único de facebook            
                $jugador_actual = Jugador::getJugadorByUserId($user_actual);

                //SI JUGADOR NUNCA REGISTRADO, AGREGARLO, SINO TOMAR SU ID
                $id_jugador = 0;
                if (!empty($jugador_actual)) {
                    $id_jugador = $jugador_actual->getId();
                } else {
                    //Sino insertar una mesa nueva y poner alli a este usuario, tomar mesa_id
                    $jugador_actual = new Jugador();
                    $jugador_actual->setUserId($user_actual);
                    $jugador_actual->save();
                    $id_jugador = $jugador_actual->getId();
                }

                //Buscar una mesa incompleta CON UN SOLO JUGADOR                
                $mesa_inc = Mesa::getMesaIncompleta();
                
                //--------------------COLECCIONES DE VIDEOS INTERVALOS----------                 
                $intervalos_videos = Intervalo::getFragmentoVideosOrdenadosXTags(); //5 videos de los ordenados por el número de tags :: Prioridad al de menor tags                
                $tam_intervalos_videos = count($intervalos_videos);
                
                //--------------------------------------------------------------
                if (!empty($mesa_inc)) { // Si hay MESA INCOMPLETA
                    $id_mesa = $mesa_inc->getId();
                                        
                    $mesa_inc->setEstado(1);//COMPLETARLA
                    $mesa_inc->save();
                    
                    $jug_partner_id = $mesa_inc->getJugador1Id(); //OBTNER EL JUG QUE ESTA ALLI
                    //ACTUALIZAR AL JUGADOR2 QUE ES EL ACTUAL Y COMPLETAR LA MESA
                    $q = Doctrine_Query::create()
                            ->update('Mesa m')
                            ->set('jugador2_id', '?', $id_jugador)
                            ->set('estado', '?', 1) //Estado 1 :: mesa completa
                            ->where('m.id = ?', $id_mesa);
                    $rows = $q->execute();

                    //Y ACTUALIZAR EL ESTDO DEL JUG ACTUAL
                    $jug_actual = Jugador::getJugadorById($id_jugador);
                    $jug_actual->setEstado(1); // Estado: ocupado 1 no disponible
                    $jug_actual->save();
                    
                    //obtener RELACIONMESAVIDEO DEL PARTNER Y MESAID
                    $relacion_mesa_vid = RelacionMesaVideo::getRelacionMesaVideo($id_mesa, $jug_partner_id);
                    
                    //ACTUALIZAR A JUGADOR PARTNER ocupado
                    $jug_partner = Jugador::getJugadorById($jug_partner_id);
                    $jug_partner->setEstado(1); // Estado: ocupado 1 no disponible
                    $jug_partner->save();
                    
                    $respuesta_real = $relacion_mesa_vid->getRespuestaReal();
                    $intervalo_id = $relacion_mesa_vid->getIntervaloId();

                    //si same o different
                    //SAME NOO tiniyint
                    if ($respuesta_real == 1) {
                        $intervalo_id_jug = $intervalo_id;
                        $respuesta_real_jug=1;
                    } else { //DIFFERENT                        
                        $respuesta_real_jug=0;
                        $id_array = mt_rand(0, $tam_intervalos_videos - 1);
                        $intervalo_id_jug = $intervalos_videos[$id_array]['id'];
                        //Corregir aleatorio puede dar la misma  intervalo_id
                    }

                    //CREARLE relacionmesavideo A JUG ACTUAL, setearle respuesta real del anterior
                    $relacion_mesa_vid = new RelacionMesaVideo();
                    $relacion_mesa_vid->setRespuestaReal($respuesta_real_jug);
                    $relacion_mesa_vid->setIntervaloId($intervalo_id_jug);
                    $relacion_mesa_vid->setMesaId($id_mesa);
                    $relacion_mesa_vid->setJugadorId($id_jugador);
                    $relacion_mesa_vid->save();
                } else { // Crearme una mesa y buscarme quien sera mi competidor---Crear Mesa para ambos                    
                    $jugadores = Jugador::getJugadoresDisponibles($id_jugador);  //BUSCAR JUGADORES DISPONIBLES sin incluir ACTUAL
                    $mesa = new Mesa(); //NUEVA MESA

                    if (!empty($jugadores)) { // Existe con quien jugar, setearmelo de una
                        $mesa->setJugador1Id($id_jugador); //SET JUG ACTUAL
                        $mesa->setJugador2Id($jugadores[0]['id']); // El q me escogieron
                        $mesa->setEstado(1); //COMPLETA
                        $mesa->save();
                        $id_mesa = $mesa->getId();

                        $jug_actual = Jugador::getJugadorById($id_jugador);
                        $jug_actual->setEstado(1); // SETEAR JUG ACTUAL no disponible
                        $jug_actual->save();

                        $jug_partner = Jugador::getJugadorById($jugadores[0]['id']);
                        $jug_partner->setEstado(1); // SETEAR JUG PARTNER no disponible
                        $jug_partner->save();

                        $id_array = mt_rand(0, $tam_intervalos_videos - 1);
                        $intervalo_id = $intervalos_videos[$id_array]['id'];

                        //aleatorio same or different
                        $respuesta = mt_rand(0, 1);
                        if ($respuesta == 1) {
                            //$respuesta_real_jug = "SAME"; //SAME
                            $respuesta_real_jug =1;
                            $intervalo_id_partner = $intervalo_id;
                        } else {
                            //$respuesta_real_partner = "DIFFERENT"; //DIFFERENT
                            $respuesta_real_partner =0;
                            $id_array2 = mt_rand(0, $tam_intervalos_videos - 1);
                            while ($id_array2 != $id_array)
                                $id_array2 = mt_rand(0, $tam_intervalos_videos - 1);
                            $intervalo_id_partner = $intervalos_videos[$id_array2]['id'];
                        }

                        //crear relacionmesavideo de este JUG1
                        $relacion_mesa_vid = new RelacionMesaVideo();
                        $relacion_mesa_vid->setRespuestaReal($respuesta_real_jug);
                        $relacion_mesa_vid->setIntervaloId($intervalo_id);
                        $relacion_mesa_vid->setMesaId($id_mesa);
                        $relacion_mesa_vid->setJugadorId($id_jugador); //a este le seteo ACTUAL
                        $relacion_mesa_vid->save();

                        //crear relacionmesavideo de este JUG2
                        $relacion_mesa_vid_partner = new RelacionMesaVideo();
                        $relacion_mesa_vid_partner->setRespuestaReal($respuesta_real_partner);
                        $relacion_mesa_vid_partner->setIntervaloId($intervalo_id_partner);
                        $relacion_mesa_vid_partner->setMesaId($id_mesa);
                        $relacion_mesa_vid_partner->setJugadorId($jugadores[0]['id']); //a este PARTNER
                        $relacion_mesa_vid_partner->save();
                    } else { // Se quedo la mesa conmigo y estado incompleto                           
                        $mesa->setJugador1Id($id_jugador);
                        $mesa->setEstado(0); //Incompleta
                        $mesa->save();
                        $id_mesa = $mesa->getId();

                        //SÓLO PONERLO NO DISPONIBLE RELACIONMESAVIDEO SE LE ASIGNARÁ EN LOS OTROS CASOS (debe esperar)
                        $jug_actual = Jugador::getJugadorById($id_jugador);
                        $jug_actual->setEstado(1); // no disponible
                        $jug_actual->save();
                        
                        //OJOOOO ponerle uhna RELACIONMESAVIDEO porque en la siguiente le van a preguntar por su relacionmesa
                        $id_array = mt_rand(0, $tam_intervalos_videos - 1);
                        //ECHO "intervalovideo: ".count($intervalos_videos)." 1 video: ".$intervalos_videos[$id_array]. "id array: ".$id_array." empty:? ".$intervalos_videos[$id_array]['id']; die();
                        $intervalo_id = $intervalos_videos[$id_array]['id'];// OJO CON LOS FETCARRAY!!!!!!!!!!! VER SI FETCHALLOBJECT!!!//->getId(); //se cae  asdi no $intervalos_videos[$id_array]['id']

                        //aleatorio same or different
                        $respuesta = mt_rand(0, 1);
                        if ($respuesta == 1) {
                            //$respuesta_real = "SAME"; //SAME                            
                            $respuesta_real_jug=1;
                        } else {
                            //$respuesta_real = "DIFFERENT"; //DIFFERENT                            
                            $respuesta_real_jug=0;
                        }

                        //crear relacionmesavideo de este JUG1
                        $relacion_mesa_vid = new RelacionMesaVideo();
                        $relacion_mesa_vid->setRespuestaReal($respuesta_real_jug);
                        $relacion_mesa_vid->setIntervaloId($intervalo_id);
                        $relacion_mesa_vid->setMesaId($id_mesa);
                        $relacion_mesa_vid->setJugadorId($id_jugador); //a este le seteo ACTUAL
                        $relacion_mesa_vid->save();
                    }
                }
                //Devolver JSON con estos datos
                $response['tipo'] = "identificacion";
                $response['objeto'] = array();
                $response['objeto'][$id_mesa] = $id_jugador;

                //poner en session la mesaid
                $mesaid = $this->getUser()->getAttribute('mesaid', "");
                $mesaid = $id_mesa;
                $this->getUser()->setAttribute('mesaid', $id_mesa);
            }
        }
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
        return $this->renderText(json_encode($response));
    }

    //INCOMPLETO
    public function executeInsertarEtiqueta(sfWebRequest $request) {
        $tmp = $request->getParameter('etiqueta_texto');
        $etiqueta_texto = isset($tmp) ? $tmp : '';
        $tmp = $request->getParameter('tiempo_envio');
        $tiempo_envio = isset($tmp) ? $tmp : '';

        $response = array();
        if ($etiqueta_texto != '' && $tiempo_envio != '') {
            //calificar, insertarla con el tiempo
            $etiqueta = new InstanciaEtiqueta();
            $etiqueta->setRelacionmesavideoId(0); //*IMPORTANTE* ojo HAY QUE PASAR RELACIONMESAVIDEO
            $etiqueta->setTexto($etiqueta_texto);
            $etiqueta->setTiempo($tiempo_envio); //convert time
            $etiqueta->save();
            //$id_etiqueta = $etiqueta->getId();
        }
        //Devolver JSON con estos datos
        $response['puntaje'] = "identificacion"; //RETORNAR MENSAJES DE TIPO ? Y PUNTAJE
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
        return $this->renderText(json_encode($response));
    }

    public function executeNew(sfWebRequest $request) {
        $this->form = new MesaForm();
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