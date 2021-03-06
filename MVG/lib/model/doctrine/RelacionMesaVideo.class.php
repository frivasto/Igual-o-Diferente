<?php

/**
 * RelacionMesaVideo
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    MusicVideoGame
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class RelacionMesaVideo extends BaseRelacionMesaVideo
{
    public static function getRelacionMesaVideoXRound($id_mesa, $round_num){
        $relacion_mesa_vid=Doctrine_Core::getTable('RelacionMesaVideo')
                    ->createQuery('r')                                      
                    ->where('r.mesa_id = ?',$id_mesa)
                    ->andWhere('r.num_round = ?',$round_num)
                    ->fetchOne();
        return $relacion_mesa_vid;        
    }
    public static function getRelacionMesaVideo($id_mesa, $jugador_duenio, $round_num){
        $relacion_mesa_vid=Doctrine_Core::getTable('RelacionMesaVideo')
                    ->createQuery('r')                                      
                    ->where('r.mesa_id = ?',$id_mesa)
                    ->andWhere('r.jugador_id = ?',$jugador_duenio)
                    ->andWhere('r.num_round = ?',$round_num)
                    ->fetchOne();
        return $relacion_mesa_vid;        
    }
    
    public static function getRelacionMesaVideoxJug($id_mesa, $jugador_duenio){
        $relacion_mesa_vid=Doctrine_Core::getTable('RelacionMesaVideo')
                    ->createQuery('r')                                      
                    ->where('r.mesa_id = ?',$id_mesa)
                    ->andWhere('r.jugador_id = ?',$jugador_duenio)
                    ->orderBy('r.num_round')
                    ->fetchArray();
        return $relacion_mesa_vid;        
    }
    
    //SET DE RELACIONESMESAVIDEO DE LA MESA ORDENADOS POR EL NUM DE ROUND
    public static function getRelacionMesaVideoXId($id_mesa){
        $relaciones_mesa_vid=Doctrine_Core::getTable('RelacionMesaVideo')
                    ->createQuery('r')                                      
                    ->where('r.mesa_id = ?',$id_mesa) 
                    ->orderBy('r.num_round')
                    ->fetchArray();
        return $relaciones_mesa_vid;        
    }
    
    //SET VIDEOS USERS
    public static function getJugadoresSetVideos($id_mesa){
        //distinct(a.user_id)
        //DISTINCT r.jugador_id as jugador_id        
        $relaciones_mesa_vid = Doctrine_Query::create()
                     ->select('DISTINCT(r.jugador_id) as jug_id')            
                     ->from('RelacionMesaVideo r')
                     ->where('r.mesa_id = ?',$id_mesa) 
                     ->orderBy('r.num_round')
                     ->fetchArray();
        return $relaciones_mesa_vid; 
    }
    
    //GENERAR SET DE VIDEOS DE LA MESAID PARCIALMENTE, INDICANDO EL JUGADOR DUENIO CORRESPONDIENTE
    public static function generarSetVideos($mesa_id, $idjugador) {
        $array_excluidos = array();
        $array_excluidos[] = 0;
        $num_round = 0;

        //COMPLETAR LA GENERACION DE VIDEOS PARA LA PARTIDA DE ESTA MESA
        $relaciones_mesa_video = self::getRelacionMesaVideoXId($mesa_id);
        if ($relaciones_mesa_video != NULL) {
            $jug_set=0;
            
            $jugadores_set_videos=self::getJugadoresSetVideos($mesa_id);
            $tam_jugadores_set_videos=count($jugadores_set_videos);
            if($tam_jugadores_set_videos==1) $jug_set=$jugadores_set_videos[0]['jug_id'];
            //echo "Set de videos completando ".$idjugador."+ jugadores duenios ".$tam_jugadores_set_videos." set jug: ".$jugadores_set_videos[0]['jug_id']; die();
            
            //SI EL JUG QUE YA TIENE SET DE VIDEOS NO ES EL MISMO, SINO SU PARTNER
            if($jug_set!=0 && $jug_set!=$idjugador){
                //YA EXISTE FUE EL INICIAL, ::::::::::COMPLETARLO AQUI::::::::::                
                $respuesta_real = "";
                $intervalo1 = 0;

                //N ROUNDS ->N REGS
                $tam_relaciones_mesa_video = count($relaciones_mesa_video);
                
                //echo "<br />tammm 1:  ".$tam_relaciones_mesa_video; 
                //print_r($relaciones_mesa_video);
                //echo "<br />";
                
                /*Dormir n segundos hasta que complete sus videos el 2do jugador*/
                if($tam_relaciones_mesa_video<10){                   
                    //sleep(5); //10-$tam_relaciones_mesa_video
                    sleep(10-$tam_relaciones_mesa_video); 
                    $relaciones_mesa_video = self::getRelacionMesaVideoXId($mesa_id);
                    $tam_relaciones_mesa_video = count($relaciones_mesa_video);
                    
                    //echo "<br />tammm 2:  ".$tam_relaciones_mesa_video; 
                    //print_r($relaciones_mesa_video);
                }
                //die();
                                
                for ($index = 0; $index < $tam_relaciones_mesa_video; $index++) {

                    //RESPUESTA_REAL Y PRIMER VIDEO
                    $num_round = $relaciones_mesa_video[$index]['num_round'];
                    $respuesta_real = $relaciones_mesa_video[$index]['respuesta_real'];
                    $intervalo1 = $relaciones_mesa_video[$index]['intervalo_id'];
                    $array_excluidos[] = $intervalo1;

                    //BUSCAR 2DO VIDEO
                    if ($respuesta_real == 1) {
                        //SAME                            
                        $intervalo2 = $intervalo1;
                    } else {
                        //DIFFERENT
                        $categoria="";
                        $intervaloObj=Intervalo::getIntervaloXId($intervalo1);
                        $categoria=Video::getVideoxId($intervaloObj->getVideoId())->getCategoria();
                        $intervalo2 = Intervalo::obtenerIntervaloAleatorio($array_excluidos,$categoria);
                        $array_excluidos[] = $intervalo2;
                    }

                    //INSERTARLO PARA ROUND n JUG_PARTNER
                    $relacion_mesa_vid_jug_partner = new RelacionMesaVideo();
                    $relacion_mesa_vid_jug_partner->setRespuestaReal($respuesta_real);
                    $relacion_mesa_vid_jug_partner->setIntervaloId($intervalo2);
                    $relacion_mesa_vid_jug_partner->setMesaId($mesa_id);
                    $relacion_mesa_vid_jug_partner->setJugadorId($idjugador); //partner
                    $relacion_mesa_vid_jug_partner->setNumRound($num_round);
                    $relacion_mesa_vid_jug_partner->save();                        
                }
                //echo "**Set de videos completando ".$idjugador."  jug set era: ".$jug_set; die();
            }  
            //echo "Set de videos completando ".$idjugador; die();
        } else {
            //SET VIDEOS INICIAL, ::::::::::PRIMER JUG::::::::::
            for ($num_round = 0; $num_round < 10; $num_round++) {
                //echo "<br />aqui: ".$num_round;
                if (self::getRelacionMesaVideo($mesa_id, $idjugador, $num_round + 1) == NULL) {

                    //BUSCAR 1ER VIDEO 
                    $intervalo1 = Intervalo::obtenerIntervaloAleatorio($array_excluidos);
                    $array_excluidos[] = $intervalo1;

                    //RESPUESTA_REAL ALEATORIA
                    $respuesta_aleatorio = mt_rand(0, 1);

                    //INSERTARLO PARA ROUND n JUG_ACTUAL
                    $relacion_mesa_vid_jug_actual = new RelacionMesaVideo();
                    $relacion_mesa_vid_jug_actual->setRespuestaReal($respuesta_aleatorio);
                    $relacion_mesa_vid_jug_actual->setIntervaloId($intervalo1);
                    $relacion_mesa_vid_jug_actual->setMesaId($mesa_id);
                    $relacion_mesa_vid_jug_actual->setJugadorId($idjugador); //actual
                    $relacion_mesa_vid_jug_actual->setNumRound($num_round + 1);
                    $relacion_mesa_vid_jug_actual->save();
                    
                    //echo " - ".$relacion_mesa_vid_jug_actual->getId();
                }
            }
            //echo "Set de videos inicial ".$idjugador."round nums".$num_round; die();
        }
    }
    
    //CONSULTAR EL SET VIDEOS COMPLETO
    public static function SetVideostoJsonArray($mesa_id){
        //MESA
        $mesa_tmp=Mesa::getMesaxId($mesa_id);
        
        //JUGADORES
        $jugador1=$mesa_tmp->getJugador1Id();
        $jugador2=$mesa_tmp->getJugador2Id();
        
        //CONSULTAR EL SET VIDEOS COMPLETO
        $relaciones_mesa_video_jug1 = self::getRelacionMesaVideoxJug($mesa_id, $jugador1);
        $relaciones_mesa_video_jug2 = self::getRelacionMesaVideoxJug($mesa_id, $jugador2);
        
        $set_intervalos_videos=array();
        //RECORRERLO Y TOMAR SÓLO DATA IMPORTANTE PARA CLIENTE VIDEO1, VIDEO2, RESPUESTA_REAL
        $tam_relaciones_mesa_video = count($relaciones_mesa_video_jug1); //ambos tienen el mismo tamanio
        
        echo "tam 1: ".count($relaciones_mesa_video_jug1)." tam2: ".count($relaciones_mesa_video_jug2);
        for ($index_round = 0; $index_round < $tam_relaciones_mesa_video; $index_round++) {
             //RESPUESTA_REAL Y PRIMER VIDEO
             $respuesta_real = $relaciones_mesa_video_jug1[$index_round]['respuesta_real'];
             $intervalo1_id = $relaciones_mesa_video_jug1[$index_round]['intervalo_id'];
             $intervalo2_id = $relaciones_mesa_video_jug2[$index_round]['intervalo_id'];                           
             
             $intervalo1=Intervalo::getIntervaloXId($intervalo1_id);
             $intervalo2=Intervalo::getIntervaloXId($intervalo2_id);
             
             $intervaloObj1=array();
             $intervaloObj1["url"]= Video::getVideoxId($intervalo1->getVideoId())->getUrl();
             $intervaloObj1["ini"]= $intervalo1->getInicio();
             $intervaloObj1["fin"]= $intervalo1->getFin();
             
             $intervaloObj2=array();
             $intervaloObj2["url"]= Video::getVideoxId($intervalo2->getVideoId())->getUrl();
             $intervaloObj2["ini"]= $intervalo2->getInicio();
             $intervaloObj2["fin"]= $intervalo2->getFin();
             
             $intervalos_row=array($respuesta_real,$intervaloObj1,$intervaloObj2);             
             $set_intervalos_videos[]=$intervalos_row;
        }
        return $set_intervalos_videos;
    }
    
    
    
    //CONSULTAR EL SET VIDEOS DEL JUGADOR
    public static function toJsonArray($mesa_id,$jug_id){        
        $relaciones_mesa_video_jug1 = self::getRelacionMesaVideoxJug($mesa_id, $jug_id);
       
        $set_intervalos_videos=array();
        //RECORRERLO Y TOMAR SÓLO DATA IMPORTANTE PARA CLIENTE VIDEO1, RESPUESTA_REAL
        $tam_relaciones_mesa_video = count($relaciones_mesa_video_jug1); //ambos tienen el mismo tamanio
        
        for ($index_round = 0; $index_round < $tam_relaciones_mesa_video; $index_round++) {
             //RESPUESTA_REAL Y PRIMER VIDEO
             $respuesta_real = $relaciones_mesa_video_jug1[$index_round]['respuesta_real'];
             $intervalo1_id = $relaciones_mesa_video_jug1[$index_round]['intervalo_id'];
            
             $intervalo1=Intervalo::getIntervaloXId($intervalo1_id);
            
             $intervaloObj1=array();
             $intervaloObj1["url"]= Video::getVideoxId($intervalo1->getVideoId())->getUrl();
             $intervaloObj1["ini"]= Intervalo::time2seconds($intervalo1->getInicio());
             $intervaloObj1["fin"]= Intervalo::time2seconds($intervalo1->getFin());
             
             $intervalos_row=array($respuesta_real,$intervaloObj1);             
             $set_intervalos_videos[]=$intervalos_row;
        }
        return $set_intervalos_videos;
    }
}