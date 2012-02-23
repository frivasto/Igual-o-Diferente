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

                //Buscar una mesa incompleta
                $q = Doctrine_Query::create()
                ->select('m.id')
                ->from('Mesa m')
                ->where('m.estado=0');
                 $mesas_inc = $q->fetchArray();

                  if(!empty($mesas_inc)){ // Si hay mesas incompletas registrarme aqui
                    $id_mesa=$mesas_inc[0]['id'];
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

                            $jug1=Jugador::getJugadorById($id_jugador);
                            $jug1->setEstado(0); // no disponible
                            $jug1->save();

                       }else{ // Se quedo la mesa conmigo y estado incompleto
                            $mesa->setJugador1Id($id_jugador);
                            //$mesa->setJugador2Id($jugadores[0]->getId()); // El q me escogieron
                            $mesa->setEstado(0); //Incompleta
                            $mesa->save();
                            $id_mesa=$mesa->getId();

                            $jug=Jugador::getJugadorById($id_jugador);
                            $jug->setEstado(0); // no disponible
                            $jug->save();

                      }
                  }


                //$id_mesa=0
                //Devolver JSON con estos datos
                $response['tipo']="identificacion";
                $response['objeto']=array();
                $response['objeto'][$id_mesa]=$id_jugador;                
                
                //poner en session la mesaid
                $mesaid = $this->getUser()->getAttribute('mesaid',""); 
                $mesaid =$id_mesa;
                $this->getUser()->setAttribute('mesaid',$id_mesa);
                //$response['mesaid'] = $id_mesa;                 
                //$response['userid'] = $id_jugador; 
            }
        }                
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