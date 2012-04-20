<?php

/**
 * BaseRelacionMesaVideo
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $token
 * @property integer $mesa_id
 * @property integer $jugador_id
 * @property integer $num_round
 * @property integer $intervalo_id
 * @property integer $video_intervalo_estado
 * @property boolean $respuesta_real
 * @property boolean $eliminado
 * @property Mesa $Mesa
 * @property Jugador $Jugador
 * @property Intervalo $Intervalo
 * @property Doctrine_Collection $InstanciaEtiquetaRelacionMesaVideo
 * 
 * @method integer             getId()                                 Returns the current record's "id" value
 * @method string              getToken()                              Returns the current record's "token" value
 * @method integer             getMesaId()                             Returns the current record's "mesa_id" value
 * @method integer             getJugadorId()                          Returns the current record's "jugador_id" value
 * @method integer             getNumRound()                           Returns the current record's "num_round" value
 * @method integer             getIntervaloId()                        Returns the current record's "intervalo_id" value
 * @method integer             getVideoIntervaloEstado()               Returns the current record's "video_intervalo_estado" value
 * @method boolean             getRespuestaReal()                      Returns the current record's "respuesta_real" value
 * @method boolean             getEliminado()                          Returns the current record's "eliminado" value
 * @method Mesa                getMesa()                               Returns the current record's "Mesa" value
 * @method Jugador             getJugador()                            Returns the current record's "Jugador" value
 * @method Intervalo           getIntervalo()                          Returns the current record's "Intervalo" value
 * @method Doctrine_Collection getInstanciaEtiquetaRelacionMesaVideo() Returns the current record's "InstanciaEtiquetaRelacionMesaVideo" collection
 * @method RelacionMesaVideo   setId()                                 Sets the current record's "id" value
 * @method RelacionMesaVideo   setToken()                              Sets the current record's "token" value
 * @method RelacionMesaVideo   setMesaId()                             Sets the current record's "mesa_id" value
 * @method RelacionMesaVideo   setJugadorId()                          Sets the current record's "jugador_id" value
 * @method RelacionMesaVideo   setNumRound()                           Sets the current record's "num_round" value
 * @method RelacionMesaVideo   setIntervaloId()                        Sets the current record's "intervalo_id" value
 * @method RelacionMesaVideo   setVideoIntervaloEstado()               Sets the current record's "video_intervalo_estado" value
 * @method RelacionMesaVideo   setRespuestaReal()                      Sets the current record's "respuesta_real" value
 * @method RelacionMesaVideo   setEliminado()                          Sets the current record's "eliminado" value
 * @method RelacionMesaVideo   setMesa()                               Sets the current record's "Mesa" value
 * @method RelacionMesaVideo   setJugador()                            Sets the current record's "Jugador" value
 * @method RelacionMesaVideo   setIntervalo()                          Sets the current record's "Intervalo" value
 * @method RelacionMesaVideo   setInstanciaEtiquetaRelacionMesaVideo() Sets the current record's "InstanciaEtiquetaRelacionMesaVideo" collection
 * 
 * @package    MusicVideoGame
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseRelacionMesaVideo extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('relacion_mesa_video');
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
        $this->hasColumn('num_round', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('intervalo_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('video_intervalo_estado', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             ));
        $this->hasColumn('respuesta_real', 'boolean', null, array(
             'type' => 'boolean',
             'default' => 0,
             ));
        $this->hasColumn('eliminado', 'boolean', null, array(
             'type' => 'boolean',
             'default' => 0,
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

        $this->hasOne('Intervalo', array(
             'local' => 'intervalo_id',
             'foreign' => 'id'));

        $this->hasMany('InstanciaEtiqueta as InstanciaEtiquetaRelacionMesaVideo', array(
             'local' => 'id',
             'foreign' => 'relacionmesavideo_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}