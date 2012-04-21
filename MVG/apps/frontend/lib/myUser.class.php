<?php

class myUser extends sfBasicSecurityUser
{
    public function getMesaId()
  {
    $mesa_id = $this->getAttribute('mesaid',"");
    if (!empty($mesa_id))
    {
      return $mesa_id;
    }
    else
    {
      return 0;
    }
  }
  
}
