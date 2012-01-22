<?php

/**
 * BaseJugador
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $token
 * @property string $user_id
 * @property string $nombre
 * @property boolean $eliminado
 * @property Doctrine_Collection $JugadorMesa2
 * @property Doctrine_Collection $JugadorMesaVideo
 * 
 * @method integer             getId()               Returns the current record's "id" value
 * @method string              getToken()            Returns the current record's "token" value
 * @method string              getUserId()           Returns the current record's "user_id" value
 * @method string              getNombre()           Returns the current record's "nombre" value
 * @method boolean             getEliminado()        Returns the current record's "eliminado" value
 * @method Doctrine_Collection getJugadorMesa2()     Returns the current record's "JugadorMesa2" collection
 * @method Doctrine_Collection getJugadorMesaVideo() Returns the current record's "JugadorMesaVideo" collection
 * @method Jugador             setId()               Sets the current record's "id" value
 * @method Jugador             setToken()            Sets the current record's "token" value
 * @method Jugador             setUserId()           Sets the current record's "user_id" value
 * @method Jugador             setNombre()           Sets the current record's "nombre" value
 * @method Jugador             setEliminado()        Sets the current record's "eliminado" value
 * @method Jugador             setJugadorMesa2()     Sets the current record's "JugadorMesa2" collection
 * @method Jugador             setJugadorMesaVideo() Sets the current record's "JugadorMesaVideo" collection
 * 
 * @package    MusicVideoGame
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseJugador extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('jugador');
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
             'unique' => true,
             'length' => 255,
             ));
        $this->hasColumn('user_id', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'unique' => true,
             'length' => 255,
             ));
        $this->hasColumn('nombre', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('eliminado', 'boolean', null, array(
             'type' => 'boolean',
             'default' => 0,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Mesa as JugadorMesa2', array(
             'local' => 'id',
             'foreign' => 'jugador2_id'));

        $this->hasMany('RelacionMesaVideo as JugadorMesaVideo', array(
             'local' => 'id',
             'foreign' => 'jugador_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}