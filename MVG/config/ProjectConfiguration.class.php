<?php

require_once 'C:\symfony-1.4.16\lib\autoload\sfCoreAutoload.class.php';
//require_once 'C:\symfony\symfony-1.4.16\lib\autoload\sfCoreAutoload.class.php';

sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins('sfDoctrinePlugin');
  }
}