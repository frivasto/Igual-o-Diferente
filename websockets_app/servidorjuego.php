#!/php -q
<?php  /*  >php -q server.php  */
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

//MESAS::::
$mesas=array();
$master  = WebSocket("localhost",12345);
$sockets = array($master);
$users   = array();
$debug   = false;
$initFrame=null; $masks=null;

while(true){
  $changed = $sockets;
  socket_select($changed,$write=NULL,$except=NULL,NULL);
  foreach($changed as $socket){
    if($socket==$master){
      $client=socket_accept($master);
      if($client<0){ console("socket_accept() failed"); continue; }
      else{ connect($client); }
    }
    else{
      $bytes = @socket_recv($socket,$buffer,2048,0);
      if($bytes==0){ disconnect($socket); }
      else{
        $user = getuserbysocket($socket);
        if(!$user->handshake){ dohandshake($user,$buffer); /*echo $user->id;*/}
        else{ process2($user,$buffer); }
      }
    }
  }
}

function process($user,$msg){
  //$action = unwrap($msg);
  echo "Usuario ID: ".$user->id;
  $action = decode2($msg);
  say("< ".$action);
  switch($action){
    case "hello" : send($user->socket,"hello human");                       break;
    case "hi"    : send($user->socket,"zup human");                         break;
    case "name"  : send($user->socket,"my name is Multivac, silly I know"); break;
    case "age"   : send($user->socket,"I am older than time itself");       break;
    case "date"  : send($user->socket,"today is ".date("Y.m.d"));           break;
    case "time"  : send($user->socket,"server time is ".date("H:i:s"));     break;
    case "thanks": send($user->socket,"you're welcome");                    break;
    case "bye"   : send($user->socket,"bye");                               break;
    default      : send($user->socket,$action." not understood");           break;
  }
}

function process2($user,$msg){	
	echo "Usuario ID (Tu): ".$user->id;
	$action = decode2($msg);
	say("< ".$action);
	global $mesas; //$mesas=$GLOBALS['mesas'];
	print_r($mesas);
	//json decode ese mensaje
	$arregloMensaje=json_decode($action, TRUE);
	//-- ver tipo::: Si es de identificación-- Todo esto Actualizar el hash mesa que esta global decode
	if($arregloMensaje["tipo"] == "identificacion"){
		$keys=array_keys($arregloMensaje["objeto"]);
		$mesaid=$keys[0]; //key es mesaid :: value es userid
		echo "\nRespuesta1: ".array_key_exists($mesaid, $mesas);
		echo "\nRespuesta2: ".array_key_exists($mesaid."", $mesas);
		//Si está mesa agregada
		if (array_key_exists($mesaid, $mesas)) {
			echo "\nExiste esta mesa_id";
			//editarla y poner usuario alli para completar
			//EDITAR EL ID DEL USER			
			$user->id = $arregloMensaje["objeto"][$mesaid];				
			$mesas[$mesaid][]=$user;
			//Mandar en formato json MSG:: COMPLETO de tipo <conexion> a los 2 usuarios	AMBOS USUARIOS poner en sus sockets	
			send($mesas[$mesaid][0]->socket,'{"tipo":"conexion","objeto":{"confirmacion": "COMPLETO"}}'); //este es el 2do player confirma completo a ambos
			send($mesas[$mesaid][1]->socket,'{"tipo":"conexion","objeto":{"confirmacion": "COMPLETO"}}'); 
		}else{
			echo "\nAún no existe esta mesa_id";
			//agregar mesa y al value que es el usuario 
			$mesas[$mesaid]=array();	
			$user->id = $arregloMensaje["objeto"][$mesaid];				
			$mesas[$mesaid][]=$user;			
			//MSG: incompleto de tipo <conexion> a este 1er usuario agregado
			send($mesas[$mesaid][0]->socket,'{"tipo":"conexion","objeto":{"confirmacion": "INCOMPLETO"}}');
			print_r($mesas);
		}	  				
	}else{  
		//Si es de mensajes-- Determinar a quien PARTNER (usuario diferente de este user, su partner, es decir misma mesa) //[Falta validar enviarle si no es bot]		
		$keys=array_keys($arregloMensaje["objeto"]);
		$mesaid=$keys[0];
		if($mesas[$mesaid][0]->id!=$user->id)
			$socket_enviar=$mesas[$mesaid][0]->socket;
		else
			$socket_enviar=$mesas[$mesaid][1]->socket; //el del otro					
			send($socket_enviar,$action); //enviar mensaje ya codificado json - en este caso es el mismo mensaje que da la vuelta por acá	'{"tipo":"mensajes","objeto":{"'+mesa_id+'": "'+msg+'"}}'	  	  
	}//fin mensajes    
}

/**
 * Encode a text for sending to clients via ws://
 * @param $text
 */
function encode2($text)
{
	// 0x1 text frame (FIN + opcode)
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);

	if($length <= 125)
		$header = pack('CC', $b1, $length);
	elseif($length > 125 && $length < 65536)
		$header = pack('CCS', $b1, 126, $length);
	elseif($length >= 65536)
		$header = pack('CCN', $b1, 127, $length);
	return $header.$text;
}
	
function decode2($payload) {
	$length = ord($payload[1]) & 127;

	if($length == 126) {
		$masks = substr($payload, 4, 4);
		$data = substr($payload, 8);
	}
	elseif($length == 127) {
		$masks = substr($payload, 10, 4);
		$data = substr($payload, 14);
	}
	else {
		$masks = substr($payload, 2, 4);
		$data = substr($payload, 6);
	}
	
	$text = '';
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}
	
//Enviar client::: $user->socket	
function send($client,$msg){ 
  say("** ".$msg);
  $send_msg = encode2 ($msg);
  socket_write ($client, $send_msg, strlen ($send_msg));  
} 

function WebSocket($address,$port){
  $master=socket_create(AF_INET, SOCK_STREAM, SOL_TCP)     or die("socket_create() failed");
  socket_set_option($master, SOL_SOCKET, SO_REUSEADDR, 1)  or die("socket_option() failed");
  socket_bind($master, $address, $port)                    or die("socket_bind() failed");
  socket_listen($master,20)                                or die("socket_listen() failed");
  echo "Server Started : ".date('Y-m-d H:i:s')."\n";
  echo "Master socket  : ".$master."\n";
  echo "Listening on   : ".$address." port ".$port."\n\n";
  return $master;
}

function connect($socket){
  global $sockets,$users;
  $user = new User();
  $user->id = uniqid();
  $user->socket = $socket;
  array_push($users,$user);
  array_push($sockets,$socket);
  console($socket." CONNECTED!");
  echo "\n**socket conectado ".$socket;
}

function disconnect($socket){
  global $sockets,$users;
  $found=null;
  $n=count($users);
  for($i=0;$i<$n;$i++){
    if($users[$i]->socket==$socket){ $found=$i; break; }
  }
  echo "\n**Usuario salió ".$users[$found]->id;
  if(!is_null($found)){ array_splice($users,$found,1); }
  $index = array_search($socket,$sockets);
  socket_close($socket);
  console($socket." DISCONNECTED!");
  echo "\n**socket desconectado ".$socket;
  if($index>=0){ array_splice($sockets,$index,1); }
}

function dohandshake($user, $buffer) {
	$key = null;
	console("\nRequesting handshake...");
	console($buffer);
	console("Handshaking...");
	// Extract header variables
	console("Getting headers...");
	if(preg_match("/GET (.*) HTTP/", $buffer, $match))
	$root = $match[1];
	if(preg_match("/Host: (.*)\r\n/", $buffer, $match))
	$host = $match[1];
	if(preg_match("/Origin: (.*)\r\n/", $buffer, $match))
	$origin = $match[1];
	if(preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $buffer, $match))
	$key = $match[1];

	$key .= "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
	$key = sha1($key,true);
	//$key = pack('H*', $key);
	$key = base64_encode($key);

	$upgrade =
			"HTTP/1.1 101 Switching Protocols\r\n" .
			"Upgrade: websocket\r\n" .
			"Connection: Upgrade\r\n" .
			"Sec-WebSocket-Accept: $key\r\n\r\n";
	socket_write($user->socket, $upgrade);
	$user->handshake = true;
	console($upgrade);
	console("Done handshaking...");
	return true;
}

function getheaders($header) {
$retVal = array();
$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
foreach ($fields as $field) {
    if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
        $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
        if (isset($retVal[$match[1]])) {
            $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
        } else {
            $retVal[$match[1]] = trim($match[2]);
        }
    }
}

if (preg_match("/GET (.*) HTTP/", $header, $match)) {
    $retVal['GET'] = $match[1];
}
return $retVal;
}

function getuserbysocket($socket){
  global $users;
  $found=null;
  foreach($users as $user){
    if($user->socket==$socket){ $found=$user; break; }
  }
  return $found;
}

function  say($msg=""){ echo "DIJO: ".$msg."\n"; }
function  unwrap($msg=""){ return substr($msg,1,strlen($msg)-2); }
function console($msg=""){ global $debug; if($debug){ echo $msg."\n"; } }

class User{
  var $id;
  var $socket;
  var $handshake;
}
?>