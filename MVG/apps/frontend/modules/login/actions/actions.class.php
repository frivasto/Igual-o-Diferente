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
  public function executeIndex(sfWebRequest $request)
  {
    //$this->forward('default', 'module');
  }
  public function executeLogin(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    $usuario_nombre=$request->getParameter('user');
    $usuario_password=$request->getParameter('password');
    if($usuario_nombre!="" && $usuario_password!=""){
        //poner en session        
        $user = $this->getUser()->getAttribute('userid',"");    
        $user=$usuario_nombre.$usuario_password;
        $this->getUser()->setAttribute('userid',$user);
        $this->redirect('Mesa/index'); 
    }
    else{
        $this->redirect('login/index'); 
    }   
  }
}
