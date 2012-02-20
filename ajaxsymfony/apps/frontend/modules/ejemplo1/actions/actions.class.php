<?php

/**
 * ejemplo1 actions.
 *
 * @package    ajaxsymfony
 * @subpackage ejemplo1
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ejemplo1Actions extends sfActions
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
  
  public function executeAjax(sfWebRequest $request) {            
        $tmp = $request->getParameter('msg');
        $msg = isset($tmp) ? $tmp : '';
        $response = array();
        if ($msg != '') { 
            if ($msg ==5){
                $response['userid'] = "yes677181"; 
            }
        }                
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
        return $this->renderText(json_encode($response));                
    }
}
