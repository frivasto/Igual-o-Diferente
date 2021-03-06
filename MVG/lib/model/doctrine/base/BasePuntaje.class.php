<?php

/**
 * BasePuntaje
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $token
 * @property integer $mesa_id
 * @property integer $jugador_id
 * @property integer $puntaje
 * @property Mesa $Mesa
 * @property Jugador $Jugador
 * 
 * @method integer getId()         Returns the current record's "id" value
 * @method string  getToken()      Returns the current record's "token" value
 * @method integer getMesaId()     Returns the current record's "mesa_id" value
 * @method integer getJugadorId()  Returns the current record's "jugador_id" value
 * @method integer getPuntaje()    Returns the current record's "puntaje" value
 * @method Mesa    getMesa()       Returns the current record's "Mesa" value
 * @method Jugador getJugador()    Returns the current record's "Jugador" value
 * @method Puntaje setId()         Sets the current record's "id" value
 * @method Puntaje setToken()      Sets the current record's "token" value
 * @method Puntaje setMesaId()     Sets the current record's "mesa_id" value
 * @method Puntaje setJugadorId()  Sets the current record's "jugador_id" value
 * @method Puntaje setPuntaje()    Sets the current record's "puntaje" value
 * @method Puntaje setMesa()       Sets the current record's "Mesa" value
 * @method Puntaje setJugador()    Sets the current record's "Jugador" value
 * 
 * @package    MusicVideoGame
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePuntaje extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('puntaje');
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
        $this->hasColumn('mesa_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('jugador_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('puntaje', 'integer', 4, array(
             'type' => 'integer',
             'default' => 0,
             'length' => 4,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Mesa', array(
             'local' => 'mesa_id',
             'foreign' => 'id'));

        $this->hasOne('Jugador', array(
             'local' => 'jugador_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}