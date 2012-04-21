<?php

/**
 * login actions.
 *
 * @package    MusicVideoGame
 * @subpackage login
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class loginActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  /*public function executeIndex(sfWebRequest $request)
  {
    //$this->forward('default', 'module');
  }*/
  public function executeIndex(sfWebRequest $request)
  {
      $this->facebook = new Facebook(array(
        'appId'  => '424542557563209',
        'secret' => 'e54e04639c02cc3def2ca95ef191acba',
      ));

     $this->user = $this->facebook->getUser();
      if ($this->user) {
          try {
            // Proceed knowing you have a logged in user who's authenticated.
            $user_profile = $this->facebook->api('/me');
            $this->getUser()->setAttribute('userid',$this->user);
            $mesaid = $this->getUser()->getAttribute('mesaid',"");
            $this->getUser()->setAttribute('mesaid','0');
            $this->redirect('Mesa/index');
         } catch (FacebookApiException $e) {
            error_log($e);
            $user = null;
          }
        }

    /*$this->forward404Unless($request->isMethod('post'));
    $usuario_nombre=$request->getParameter('user');
    $usuario_password=$request->getParameter('password');
    if($usuario_nombre!="" && $usuario_password!=""){
        //poner en session        
        $user = $this->getUser()->getAttribute('userid',"");    
        $user=$usuario_nombre.$usuario_password;
        $this->getUser()->setAttribute('userid',$user);
        
        $mesaid = $this->getUser()->getAttribute('mesaid',"");                    
        $this->getUser()->setAttribute('mesaid','0');
        $this->redirect('Mesa/index'); 
    }
    else{
        $this->redirect('login/index'); 
    }  */
  }
}
