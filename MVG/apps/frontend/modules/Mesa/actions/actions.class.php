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
                
             
                //buscar una mesa con usuario disponible de la bas de datos
                $q = Doctrine_Query::create()
                ->select('m.id')
                ->from('Mesa m')
                ->where('m.jugador1_id is not null AND m.jugador2_id is null');

                $mesas = $q->fetchArray();
                $id_mesa=0;
                
                if(!empty($mesas)){
                    //Si hay obtner la mesa y poner alli actaulizar usuario de la base tomar mesa_id
                    $id_mesa=$mesas[0]['id']; 
                    //update
                    $q = Doctrine_Query::create()
                    ->update('Mesa m')
                    ->set('jugador2_id', '?', $id_jugador)
                    ->where('m.id = ?', $id_mesa);
                    
                    $rows = $q->execute();                                                            
                }else{
                    //Sino insertar una mesa nueva y poner alli a este usuario, tomar mesa_id
                    $mesa = new Mesa();
                    $mesa->setJugador1Id($id_jugador);
                    $mesa->save();
                    $id_mesa=$mesa->getId(); 
                }                                               
                //Devolver JSON con estos datos
                $response['tipo']="identificacion";
                $response['objeto']=array();
                $response['objeto'][$id_mesa]=$id_jugador;                
                
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