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
        //echo json_encode($response);
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
                //buscar una mesa con usuario disponible de la bas de datos
                $q = Doctrine_Query::create()
                ->select('m.id')
                ->from('Mesa m')
                ->where('m.jugador1_id is not null AND m.jugador2_id is null');

                $mesas = $q->fetchArray();
                $id_mesa=0;
                //sacar de session el user_id
                $user_actual = $this->getUser()->getAttribute('userid');
                
                if(!empty($mesas)){
                    //Si hay obtner la mesa y poner alli actaulizar usuario de la base tomar mesa_id
                    echo $mesas[0]['id']; 
                    $id_mesa=$mesas[0]['id']; 
                    //update
                    $q = Doctrine_Query::create()
                    ->update('Mesa m')
                    ->set('jugador2_id', '?', $user_actual)
                    ->where('m.id = ?', $id_mesa);
                    
                    $rows = $q->execute();                                                            
                }else{
                    //Sino insertar una mesa nueva y poner alli a este usuario, tomar mesa_id
                    $mesa = new Mesa();
                    $mesa->setJugador1Id($user_actual);
                    $mesa->save();
                    
                    echo $mesa->getId(); 
                    $id_mesa=$mesa->getId(); 
                }                                               
                //Devolver JSON con estos datos
                $response['mesaid'] = $id_mesa;                 
                $response['userid'] = $user_actual; 
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