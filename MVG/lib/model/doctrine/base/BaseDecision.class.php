<?php

/**
 * BaseDecision
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $token
 * @property integer $relacionmesavideo_id
 * @property time $tiempo
 * @property integer $respuesta
 * @property boolean $eliminado
 * @property RelacionMesaVideo $RelacionMesaVideo
 * 
 * @method integer           getId()                   Returns the current record's "id" value
 * @method string            getToken()                Returns the current record's "token" value
 * @method integer           getRelacionmesavideoId()  Returns the current record's "relacionmesavideo_id" value
 * @method time              getTiempo()               Returns the current record's "tiempo" value
 * @method integer           getRespuesta()            Returns the current record's "respuesta" value
 * @method boolean           getEliminado()            Returns the current record's "eliminado" value
 * @method RelacionMesaVideo getRelacionMesaVideo()    Returns the current record's "RelacionMesaVideo" value
 * @method Decision          setId()                   Sets the current record's "id" value
 * @method Decision          setToken()                Sets the current record's "token" value
 * @method Decision          setRelacionmesavideoId()  Sets the current record's "relacionmesavideo_id" value
 * @method Decision          setTiempo()               Sets the current record's "tiempo" value
 * @method Decision          setRespuesta()            Sets the current record's "respuesta" value
 * @method Decision          setEliminado()            Sets the current record's "eliminado" value
 * @method Decision          setRelacionMesaVideo()    Sets the current record's "RelacionMesaVideo" value
 * 
 * @package    MusicVideoGame
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseDecision extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('decision');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'autoincrement' => true,
             'primary' => true,
             'unique' => true,
             'length' => 4,
             ));
        $this->hasColumn('token', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'unique' => false,
             'length' => 255,
             ));
        $this->hasColumn('relacionmesavideo_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('tiempo', 'time', null, array(
             'type' => 'time',
             ));
        $this->hasColumn('respuesta', 'integer', 1, array(
             'type' => 'integer',
             'default' => -1,
             'length' => 1,
             ));
        $this->hasColumn('eliminado', 'boolean', null, array(
             'type' => 'boolean',
             'default' => 0,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('RelacionMesaVideo', array(
             'local' => 'relacionmesavideo_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}