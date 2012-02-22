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
	
	$mesas["23"]=array();
	$mesas["23"][]=$user1;
	$mesas["23"][]=$user2;
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
	
function ponerUnElemento(){
	$mesas=$GLOBALS['mesas'];
	$user2 = new User();
	$user2->id = uniqid();
	$user2->socket = "SOCKET3";	
	
	$mesas["44"]=array();
	$mesas["44"][]=$user2;
}

function ponerUnElementoDeclareGlobal(){
	global $mesas;
	$user2 = new User();
	$user2->id = uniqid();
	$user2->socket = "SOCKET3";	
	
	$mesas["44"]=array();
	$mesas["44"][]=$user2;
}

//mostrar_mesaJuego();
obtener_partner("1", "SOCKET1");
echo "<br/> elements";
foreach($mesas as $key=>$value){
	echo "<br />".$key." - ".$value;
	if($key=="23")
		echo "<br />SI existe";
	if($key=="2")
		echo "<br />SI existe";	
	else
		echo "<br />NO existe";	
}

if (array_key_exists('23', $mesas)) {
    echo "The '23' element is in the array";
}

ECHO "USUARIOS: <BR /> ".$mesas['23'][0]->id;
ECHO "<BR /> ".$mesas['23'][1]->id;

$mesitas=array();
$mesitas['abc1']="hola1";
$mesitas['abc2']="hola2";
$mesitas['abc3']="hola3";
$mesitas['abc4']="hola4";

//otra form de recorrer arrays
echo "<br />**resultados";
while ( list( $key, $value ) = each( $mesitas ) )
 echo "<br />*resp: $key - $value";
 
ponerUnElemento();
ponerUnElementoDeclareGlobal();
echo "<br />con uno más... Editar arreglo globaaaaaaaal en la función anterior.............";
print_r($mesas);
?>