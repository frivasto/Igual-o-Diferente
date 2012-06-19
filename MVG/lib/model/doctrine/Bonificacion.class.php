<?php

/**
 * Bonificacion
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    MusicVideoGame
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Bonificacion extends BaseBonificacion
{
    /*Puntaje obtenido en una mesa específica del Jugador*/
    public static function getBonificacionXMesaJugId($id_mesa, $id_jug){        
        $bonificacion=Doctrine_Core::getTable('Bonificacion')
                    ->createQuery('b')                                      
                    ->where('b.mesa_id = ?',$id_mesa)
                    ->andWhere('b.jugador_id = ?',$id_jug)
                    ->fetchOne();
        return $bonificacion;        
    }
}