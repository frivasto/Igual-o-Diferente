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

        // return a json array
        //$this->getResponse()->setContentType('application/json');
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

    public function executeEmparejar(sfWebRequest $request) {            
        $tmp = $request->getParameter('estado');
        $estado = isset($tmp) ? $tmp : '';
        $response = array();
        if ($estado != '') { 
            if ($estado ==1){ //estado ok de los sockets 1 OPEN
                //sacar de session el user_id
                $user_actual = $this->getUser()->getAttribute('userid');
                
                //buscar usuario de este userid de facebook                
                $q = Doctrine_Query::create()
                ->select('j.id')
                ->from('Jugador j')
                ->where('j.user_id = ?',$user_actual);

                $jugador = $q->fetchArray();
                $id_jugador=0;
                if(!empty($jugador)){
                    $id_jugador=$jugador[0]['id']; 
                }else{
                    //Sino insertar una mesa nueva y poner alli a este usuario, tomar mesa_id
                    $jugador = new Jugador();
                    $jugador->user_id=$user_actual;
                    $jugador->save();
                    $id_jugador=$jugador->getId(); 
                }


                // NUEVO jugadores disponibles de acuerdo al estado
                $q = Doctrine_Query::create()
                ->select('j.id')
                ->from('Jugador j')
                ->where('j.estado=1 and j.id != ?',$id_jugador);

                $jugadores = $q->fetchArray();

                //Buscar una mesa incompleta OJO
                $q = Doctrine_Query::create()
                ->select('m.id,m.jugador1_id')
                ->from('Mesa m')
                ->where('m.estado=0');
                 $mesas_inc = $q->fetchArray();

                //--------------------COLECCIONES DE VIDEOS INTERVALOS----------
                 //5 videos de los ordenados por el número de tags :: Prioridad al de menor tags
                 $q = Doctrine_Query::create()
                ->select('i.id')
                ->from('Intervalo i')             
                ->orderBy('i.total_tags')         
                ->limit(5);
                 $intervalos_videos = $q->fetchArray();                 
                 //-------------------------------------------------------------
                 $tam_intervalos_videos=count($intervalos_videos);
                 
                  if(!empty($mesas_inc)){ // Si hay mesas incompletas registrarme aqui
                    $id_mesa=$mesas_inc[0]['id'];
                    $jug_partner=$mesas_inc[0]['jugador1_id']; //OJO
                    //update
                    $q = Doctrine_Query::create()
                    ->update('Mesa m')
                    ->set('jugador2_id', '?', $id_jugador)
                    ->set('estado', '?', 1) //Estado 1 :: mesa completa
                    ->where('m.id = ?', $id_mesa);
                    $rows = $q->execute();

                    $jug=Jugador::getJugadorById($id_jugador);
                    $jug->setEstado(0); // no disponible
                    $jug->save();
                    
                    //-----------------------------------ASIGNAR VIDEO ---------
                    //relacion mesa video del otro JUG
                    $q = Doctrine_Query::create()
                    ->select('r.id, r.respuesta_real, r.intervalo_id')
                    ->from('RelacionMesaVideo r')
                    ->where('r.mesa_id = ?',$id_mesa)
                    ->andWhere('r.jugador_id = ?',$jug_partner);  //OJO  

                    $relacion_mesa_vid = $q->fetchArray(); //die();
                    $id_relacion_mesa=$relacion_mesa_vid[0]['id'];
                    $respuesta_real=$relacion_mesa_vid[0]['respuesta_real'];
                    $intervalo_id=$relacion_mesa_vid[0]['intervalo_id'];
                    
                    //si same o different
                    if($respuesta_real=="SAME"){
                        $intervalo_id2=$intervalo_id;
                    }else{ //DIFFERENT
                        //aleatorio
                        $id_array=mt_rand(0,$tam_intervalos_videos-1);
                        $intervalo_id2=$intervalos_videos[$id_array]['id'];                        
                    }
                        
                    //crear relacionmesavideo de este nuevo, setearle respuesta real del anterior
                    $relacion_mesa_vid2 = new RelacionMesaVideo();
                    $relacion_mesa_vid2->respuesta_real=$respuesta_real;
                    $relacion_mesa_vid2->intervalo_id=$intervalo_id2;
                    $relacion_mesa_vid2->mesa_id=$id_mesa;
                    $relacion_mesa_vid2->jugador_id=$id_jugador;
                    $relacion_mesa_vid2->save();                    
                    //----------------------------------------------------------

                  }else{ // Crearme una mesa y buscarme quien sera mi competidor---Crear Mesa para ambos
                    $mesa = new Mesa();
                      if(!empty($jugadores)){ // Existe con quien jugar, setearmelo de una
                            $mesa->setJugador1Id($id_jugador);
                            $mesa->setJugador2Id($jugadores[0]->getId()); // El q me escogieron
                            $mesa->setEstado(1);
                            $mesa->save();
                            $id_mesa=$mesa->getId();

                            $jug=Jugador::getJugadorById($id_jugador);
                            $jug->setEstado(0); // no disponible
                            $jug->save();

                            //OJO
                            $jug1=Jugador::getJugadorById($jugadores[0]->getId());
                            $jug1->setEstado(0); // no disponible
                            $jug1->save();
                            
                            //-----------------------------------ASIGNAR VIDEO -
                            $id_array=mt_rand(0,$tam_intervalos_videos-1);
                            $intervalo_id=$intervalos_videos[$id_array]['id'];
                            
                            //aleatorio same or different
                            $respuesta=mt_rand(0,1);
                            if($respuesta==1){                                
                                $respuesta_real="SAME"; //SAME
                                $intervalo_id2=$intervalo_id;
                            }else{
                                $respuesta_real="DIFFERENT"; //DIFFERENT
                                $id_array2=mt_rand(0,$tam_intervalos_videos-1);
                                while($id_array2!=$id_array)
                                    $id_array2=mt_rand(0,$tam_intervalos_videos-1);
                                $intervalo_id2=$intervalos_videos[$id_array2]['id'];
                            }
                            
                            //crear relacionmesavideo de este JUG1
                            $relacion_mesa_vid = new RelacionMesaVideo();
                            $relacion_mesa_vid->respuesta_real=$respuesta_real;
                            $relacion_mesa_vid->intervalo_id=$intervalo_id;
                            $relacion_mesa_vid->mesa_id=$id_mesa;
                            $relacion_mesa_vid->jugador_id=$id_jugador;
                            $relacion_mesa_vid->save();
                            
                            //crear relacionmesavideo de este JUG2
                            $relacion_mesa_vid2 = new RelacionMesaVideo();
                            $relacion_mesa_vid2->respuesta_real=$respuesta_real;
                            $relacion_mesa_vid2->intervalo_id=$intervalo_id2;
                            $relacion_mesa_vid2->mesa_id=$id_mesa;
                            $relacion_mesa_vid2->jugador_id=$jugadores[0]->getId();
                            $relacion_mesa_vid2->save();
                            //--------------------------------------------------

                       }else{ // Se quedo la mesa conmigo y estado incompleto
                            $mesa->setJugador1Id($id_jugador);
                            //$mesa->setJugador2Id($jugadores[0]->getId()); // El q me escogieron
                            $mesa->setEstado(0); //Incompleta
                            $mesa->save();
                            $id_mesa=$mesa->getId();

                            $jug=Jugador::getJugadorById($id_jugador);
                            $jug->setEstado(0); // no disponible
                            $jug->save();
                            
                            //-----------------------------------ASIGNAR VIDEO -
                            //este es relacion_mesa 1ER JUG INCOMPLETO----------
                            //aleatorio same or different
                            $respuesta=mt_rand(0,1);
                            if($respuesta==1){                                
                                $respuesta_real="SAME"; //SAME                               
                            }else{
                                $respuesta_real="DIFFERENT"; //DIFFERENT                                
                            }
                            //aleatorio video
                            $id_array=mt_rand(0,$tam_intervalos_videos-1);
                            $intervalo_id=$intervalos_videos[$id_array]['id'];
                            //crear relacionmesavideo de este JUG
                            $relacion_mesa_vid = new RelacionMesaVideo();
                            $relacion_mesa_vid->respuesta_real=$respuesta_real;
                            $relacion_mesa_vid->intervalo_id=$intervalo_id;
                            $relacion_mesa_vid->mesa_id=$id_mesa;
                            $relacion_mesa_vid->jugador_id=$id_jugador;
                            $relacion_mesa_vid->save();
                            //--------------------------------------------------
                      }
                  }
                  
                //Devolver JSON con estos datos
                $response['tipo']="identificacion";
                $response['objeto']=array();
                $response['objeto'][$id_mesa]=$id_jugador;                
                
                //poner en session la mesaid
                $mesaid = $this->getUser()->getAttribute('mesaid',""); 
                $mesaid =$id_mesa;
                $this->getUser()->setAttribute('mesaid',$id_mesa);
            }
        }                
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
        return $this->renderText(json_encode($response));                
    }
    
    public function executeInsertarEtiqueta(sfWebRequest $request) {  
        $tmp = $request->getParameter('etiqueta_texto');
        $etiqueta_texto = isset($tmp) ? $tmp : '';
        $tmp = $request->getParameter('tiempo_envio');
        $tiempo_envio = isset($tmp) ? $tmp : '';
        
        $response = array();
        if ($etiqueta_texto != '' && $tiempo_envio!='') {
            //calificar, insertarla con el tiempo
            $etiqueta = new InstanciaEtiqueta();
            $etiqueta->relacionmesavideo_id=0;
            $etiqueta->texto=$etiqueta_texto;
            $etiqueta->tiempo=$tiempo_envio; //convert time
            $etiqueta->save();           
            $id_etiqueta=$etiqueta->getId();
        }
        //Devolver JSON con estos datos
        $response['puntaje']="identificacion";        
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