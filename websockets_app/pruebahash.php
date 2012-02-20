<?php

class User{
  var $id;
  var $socket;
  var $handshake;
}

	$mesas=array();
	$user1 = new User();
	$user1->id = uniqid();
	$user1->socket = "SOCKET1";	
	
	$user2 = new User();
	$user2->id = uniqid();
	$user2->socket = "SOCKET2";	
	
	$mesas["1"]=array();
	$mesas["1"][]=$user1;
	$mesas["1"][]=$user2;
	print_r($mesas);
	
function mostrar_mesaJuego(){
	$mesas=array();
	$user1 = new User();
	$user1->id = uniqid();
	$user1->socket = "SOCKET1";	
	
	$user2 = new User();
	$user2->id = uniqid();
	$user2->socket = "SOCKET2";	
	
	$mesas["1"]=array();
	$mesas["1"][]=$user1;
	$mesas["1"][]=$user2;
	print_r($mesas);
}	

function obtener_partner($mesa_id, $socketid){
	//comparación es por el objeto $user actual
	$mesas=$GLOBALS['mesas'];
	$mesaActual=$mesas[$mesa_id];
	if($mesaActual[0]->socket==$socketid){
		echo "<br />Este es el usuario actual ".$mesaActual[0]->id." - ".$mesaActual[0]->socket;
	}else{
		echo "<br />Otro es el usuario actual ".$mesaActual[1]->id." - ".$mesaActual[1]->socket;
	}
}
	
//mostrar_mesaJuego();
obtener_partner("1", "SOCKET1");
?>