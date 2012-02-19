#!/php -q
<?php  /*  >php -q server.php  */
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

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
        if(!$user->handshake){ dohandshake($user,$buffer); }
        else{ process($user,$buffer); }
      }
    }
  }
}

//---------------------------------------------------------------
function process($user,$msg){
  //$action = unwrap($msg);
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
//OJO 
/*
WebSocket text packet
x84 x0c hello, world
WebSocket binary packet
x85 x06 hello!

http://caucho.com/resin-4.0/examples/websocket-java/
http://opensource.apple.com/source/WebCore/WebCore-955.66/websockets/WebSocketHandshake.cpp
http://metaoverflow.com/stackoverflowmeta/7908306/implementing-handshake-for-hybi-17
	
EXACTAMENTE EL MISMO PROBLEMA:::
http://www.x2x1.com/show/7945040.aspx

server received encoded data and not plaintext: why
http://stackoverflow.com/questions/7908306/implementing-handshake-for-hybi-17
http://stackoverflow.com/questions/7912772/decoding-network-chars-html5-websocket

How can I decode that unreadable string in a human readable one

guiarse de este documento, dfalta poner header tailer de la cadena, porque hay que implementar TCP, OJO CON EL frame
http://tools.ietf.org/html/draft-ietf-hybi-thewebsocketprotocol-17	


SOLUCIÓN AL INICIOOOO 
---------------------->
http://stackoverflow.com/questions/7945040/html5-websocket-with-hybi-17
http://stackmobile.quickmediasolutions.com/stackoverflow.com/questions/view/?id=7945040
OTROS DE SOLUCIÓN
http://stackoverflow.com/questions/7040078/how-to-deconstruct-data-frames-in-websockets-hybi-08/7045885#7045885
http://stackoverflow.com/questions/7945040/html5-websocket-with-hybi-17
http://pastebin.com/Wd2gfwa2

A text packet is the byte 0x00 followed by UTF-8 encoded data followed by a 0xff byte.
http://www.caucho.com/resin-4.0/examples/websocket-php/index.xtp
WebSocket text packet
x00 utf-8-data xff
Example: hello text packet
\x00hello, world\xff


@staticmethod
    def encode_hixie(buf):
        return s2b("\x00" + b2s(b64encode(buf)) + "\xff"), 1, 1

    @staticmethod
    def decode_hixie(buf):
        end = buf.find(s2b('\xff'))
        return {'payload': b64decode(buf[1:end]),
                'hlen': 1,
                'length': end - 1,
                'left': len(buf) - (end + 1)}
https://github.com/kanaka/websockify/blob/master/websocket.py#L233
http://git.warmcat.com/cgi-bin/cgit/libwebsockets/tree/lib/handshake.c?id=e25221763351b401b7c476c234250263fa2f8e25

POR SI ACASO::: 
https://github.com/lemmingzshadow/php-websocket/tree/master/client
https://github.com/lemmingzshadow/php-websocket/tree/master/server/lib/WebSocket
http://www.slideshare.net/spoutserver/realtime-communication-techniques-with-php
http://magazine.joomla.org/issues/Issue-Jan-2012/item/652-Integrating-WebSocket-to-Joomla
DRAFTS: http://es.scribd.com/doc/60898569/WebSockets-The-Real-Time-Web-Delivered
http://tools.ietf.org/html/rfc6455
FRAME: http://updates.html5rocks.com/2011/08/What-s-different-in-the-new-WebSocket-protocol

TERORÍA PARA CREAR SU PROPIO WEB SERVER SOCKETS http://altdevblogaday.com/2012/01/23/writing-your-own-websocket-server/

ADD WEBSCOKET STREAM FRAMING

SEND
BYTE BYTE BYTE
x00 S.ENCODE(utf-8-data) xff
http://books.google.com.ec/books?id=7hfSXSyBIHYC&pg=PA140&lpg=PA140&dq=EXAMPLES+WITH+WEBSOCKET+PROTOCOL&source=bl&ots=iRqVGM6bha&sig=czBGkunsOemTSowpMMbIJOIuGCU&hl=es&sa=X&ei=bjQvT6-pL8WqgwfVqJTtDw&ved=0CGQQ6AEwBzgo#v=onepage&q=EXAMPLES%20WITH%20WEBSOCKET%20PROTOCOL&f=false

PROBAR ESTOS EJEMPLOS:
SIMPLE http://localhost:7070/websocket_test/WebSocketTest.html
http://localhost:7070/updatedsockets/phpwebsocket/client.html OK CHAT, DE SERVER
http://localhost:7070/otroservidorsockets/client/client.php  CHAT

BUSCAR GOOGLE
Received unexpected continuation frame.
writing websocket servers RFC
WEBSOCKETS LATEST HANDSHAKE
RFC 6455 support

Revisar diapositiva::
Harnessing the Power of HTML5 Web Sockets to Create Scalable Real-time Applications Presentation DIAPOSITIVA IMPORTANTE DETALLE DEL PROTOCOLO


IMPLEMENTACIÓN CON EL PROTOCOLO
https://github.com/vti/protocol-websocket


OTROS
http://antwerkz.com/glassfish-web-sockets-sample/
http://matware.com.ar/joomla/integrando-websocket-a-joomla.html

ENCODING EXPLANATION
http://lists.whatwg.org/htdig.cgi/whatwg-whatwg.org/2010-March/025376.html
The WebSocket specification specifies that frames must be encoded in UTF-8, but the implementation in Twisted doesn't make any check. Your application code must ensure that it only sends frames correctly encoded in UTF-8, and possibly be able to handle frames sent by the client which are not correctly encoded.
http://twistedmatrix.com/trac/export/29073/branches/websocket-4173-2/doc/web/howto/websocket.xhtml
http://blogs.claritycon.com/blog/2012/01/18/websockets-with-rfc-6455/
http://mojolicio.us/perldoc/Mojo/Transaction/WebSocket

FORMAT HIBY 17 MESSAGE
http://www.it2y.info/php/how-to-format-a-websocket-hybi-17-message.html
http://forum.codecall.net/c-programming/39933-websocket-server.html


OLD
http://blakeblackshear.wordpress.com/2011/08/25/implementing-a-websocket-handshake-in-c/
PROTOCOL http://www.openhardwaresummit.org/wp-content/uploads/2011/09/OHSWebSockets.pdf
http://showmetheco.de/articles/2011/2/diving-into-html5-with-websockets-and-perl.html

TERORIA
http://daniel.haxx.se/blog/2010/08/06/websockets-right-now/
http://fatalweb.com/questions/1492/websocket-handshake-not-working
http://stackoverflow.com/questions/8901791/web-socket-server-v13-rfc-6455-client-does-not-receive-messages
http://git.warmcat.com/cgi-bin/cgit/libwebsockets/plain/lib/handshake.c?id=1efb63c2bef1d1e33ed5c5935c3cd297bbabc51e
http://stackoverflow.com/questions/9024372/how-to-configure-websockets-rfc-6455-in-netty-3-3
http://en.wikipedia.org/wiki/WebSocket#WebSocket_protocol_handshake

interesante
http://markmaunder.com/2009/10/25/web-sockets-protocol/
http://cometdaily.com/2010/03/02/is-websocket-chat-simple/

JAVA
https://github.com/adamac/Java-WebSocket-client/blob/master/src/com/sixfire/websocket/WebSocket.java
*/

function decode($buffer){
	$len = $masks = $data = $decoded = null;

	global $initFrame, $masks;
	
	/*$len = ord ($buffer[1]) & 127;
		
	if ($len === 126) {
	  $masks = substr ($buffer, 4, 4);
	  $data = substr ($buffer, 8);
	  //setear initframe
	  $initFrame = substr ($buffer, 0, 4);
	}
	else if ($len === 127) {
	  $masks = substr ($buffer, 10, 4);
	  $data = substr ($buffer, 14);
	  $initFrame = substr ($buffer, 0, 10);
	}
	else {
	  $masks = substr ($buffer, 2, 4);
	  $data = substr ($buffer, 6);
	  $initFrame = substr ($buffer, 0, 2);
	}
	
	for ($index = 0; $index < strlen ($data); $index++) {
	  $decoded .= $data[$index] ^ $masks[$index % 4];
	}
	*/	
	
	$payloadLength = '';
		$mask = '';
		$unmaskedPayload = '';
		$decoded = array();

		// estimate frame type:
		$firstByteBinary = sprintf('%08b', ord($buffer[0]));		
		$secondByteBinary = sprintf('%08b', ord($buffer[1]));
		$opcode = bindec(substr($firstByteBinary, 4, 4));
		$isMasked = ($secondByteBinary[0] == '1') ? true : false;
		$payloadLength = ord($buffer[1]) & 127;

		// close connection if unmasked frame is received:
		if($isMasked === false)
		{
			$this->close(1002);
		}

		switch($opcode)
		{
			// text frame:
			case 1:
				$decoded['type'] = 'text';				
			break;

			// connection close frame:
			case 8:
				$decoded['type'] = 'close';
			break;

			// ping frame:
			case 9:
				$decoded['type'] = 'ping';				
			break;

			// pong frame:
			case 10:
				$decoded['type'] = 'pong';
			break;

			default:
				// Close connection on unknown opcode:
				$this->close(1003);
			break;
		}

		if($payloadLength === 126)
		{
		   $mask = substr($buffer, 4, 4);
		   $payloadOffset = 8;
		   $dataLength = bindec(sprintf('%08b', ord($buffer[2])) . sprintf('%08b', ord($buffer[3]))) + $payloadOffset;
		}
		elseif($payloadLength === 127)
		{
			$mask = substr($buffer, 10, 4);
			$payloadOffset = 14;
			$tmp = '';
			for($i = 0; $i < 8; $i++)
			{
				$tmp .= sprintf('%08b', ord($buffer[$i+2]));
			}
			$dataLength = bindec($tmp) + $payloadOffset;
			unset($tmp);
		}
		else
		{
			$mask = substr($buffer, 2, 4);	
			$payloadOffset = 6;
			$dataLength = $payloadLength + $payloadOffset;
		}

		if($isMasked === true)
		{
			for($i = $payloadOffset; $i < $dataLength; $i++)
			{
				$j = $i - $payloadOffset;
				$unmaskedPayload .= $buffer[$i] ^ $mask[$j % 4];
			}
			$decoded['payload'] = $unmaskedPayload;
		}
		else
		{
			$payloadOffset = $payloadOffset - 4;
			$decoded['payload'] = substr($buffer, $payloadOffset);
		}
		
		//cadena
		//$decoded = implode('', $decoded);	
		//textpalabra
		//$decoded=trim($decoded,"text");
		//process, send, encode
		
	return $decoded['payload'];
}

		
function encode ($send_msg, $type = 'text', $masked = true) {
		global $initFrame, $masks;
		$index = $encoded = null;
	
	/*------------------------------------------------------------------*/
		$initFrame = array();
		$frame = '';
		$payloadLength = strlen($send_msg);

		switch($type)
		{		
			case 'text':
				// first byte indicates FIN, Text-Frame (10000001):
				$frameHead[0] = 129;				
			break;			

			case 'close':
				// first byte indicates FIN, Close Frame(10001000):
				$frameHead[0] = 136;
			break;

			case 'ping':
				// first byte indicates FIN, Ping frame (10001001):
				$frameHead[0] = 137;
			break;

			case 'pong':
				// first byte indicates FIN, Pong frame (10001010):
				$frameHead[0] = 138;
			break;
		}

		// set mask and payload length (using 1, 3 or 9 bytes) 
		if($payloadLength > 65535)
		{
			$payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
			$initFrame[1] = ($masked === true) ? 255 : 127;
			for($i = 0; $i < 8; $i++)
			{
				$initFrame[$i+2] = bindec($payloadLengthBin[$i]);
			}
			// most significant bit MUST be 0 (close connection if frame too big)
			if($initFrame[2] > 127)
			{
				$this->close(1004);
				return false;
			}
		}
		elseif($payloadLength > 125)
		{
			$payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
			$initFrame[1] = ($masked === true) ? 254 : 126;
			$initFrame[2] = bindec($payloadLengthBin[0]);
			$initFrame[3] = bindec($payloadLengthBin[1]);
		}
		else
		{
			$initFrame[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
		}
		// convert frame-head to string:
		foreach(array_keys($initFrame) as $i)
		{
			$initFrame[$i] = chr($initFrame[$i]);
		}
		if($masked === true)
		{
			// generate a random mask:
			$mask = array();
			for($i = 0; $i < 4; $i++)
			{
				$mask[$i] = chr(rand(0, 255));
			}

			$initFrame = array_merge($initFrame, $mask);			
		}	
	$encoded = implode('', $initFrame);	
	/*------------------------------------------------------------------*/
	
		// append payload to frame:
		for ($index = 0; $index < strlen ($send_msg); $index++) {
			$encoded .= $send_msg[$index] ^ $masks[$index % 4];
		}
		
		//ERROR no this masks
		$encoded = $initFrame . $masks . $encoded;
		return $encoded;
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
	
function send($client,$msg){ 
  say("** ".$msg);
  //$msg = wrap($msg);
  //socket_write($client,$msg,strlen($msg));
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
}

function disconnect($socket){
  global $sockets,$users;
  $found=null;
  $n=count($users);
  for($i=0;$i<$n;$i++){
    if($users[$i]->socket==$socket){ $found=$i; break; }
  }
  if(!is_null($found)){ array_splice($users,$found,1); }
  $index = array_search($socket,$sockets);
  socket_close($socket);
  console($socket." DISCONNECTED!");
  if($index>=0){ array_splice($sockets,$index,1); }
}

// function dohandshake($user,$buffer){
  // console("\nRequesting handshake...");
  // console($buffer);
  // /*        
    // GET {resource} HTTP/1.1
    // Upgrade: WebSocket
    // Connection: Upgrade
    // Host: {host}
    // Origin: {origin}
    // \r\n
  // */
  // list($resource,$host,$origin) = getheaders($buffer);
  // //$resource = "/phpwebsocketchat/server.php";
  // //$host     = "localhost:12345";
  // //$origin   = "http://localhost";
  // console("Handshaking...");
  // /*
  // HTTP/1.1 101 Switching Protocols
        // Upgrade: websocket
        // Connection: Upgrade
        // Sec-WebSocket-Accept: s3pPLMBiTxaQ9kYGzzhZRbK+xOo=
  // */
  // $upgrade  = "HTTP/1.1 101 WebSocket Protocol Handshake\r\n" .              
              // "Connection: Upgrade\r\n" .
			  // "Upgrade: websocket\r\n" .
			  // //"Sec-WebSocket-Accept: s3pPLMBiTxaQ9kYGzzhZRbK+xOo=\r\n".
              // "Origin: " . $origin . "\r\n" .
              // "Sec-WebSocket-Location: ws://" . $host . $resource . "\r\n" .
              // "\r\n";
  // socket_write($user->socket,$upgrade.chr(0),strlen($upgrade.chr(0)));
  // $user->handshake=true;
  // console($upgrade);
  // console("Done handshaking...");
  // return true;
// }

/*
What steps will reproduce the problem?
1. Complete handshake
2. Receive frame from client (masked)
3. Send text frame to client (unmasked)

http://stackoverflow.com/questions/7238106/error-not-a-valid-socket-resource-implementing-the-phpwebsocket-library
*/
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
//.-----------------------------------------------
//preg_match("#Sec-WebSocket-Key: (.*?)\r\n#", $buffer, $match) && $key = $match[1];

$key .= "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
$key = sha1($key,true);
//$key = pack('H*', $key);
$key = base64_encode($key);

$upgrade =
        "HTTP/1.1 101 Switching Protocols\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "Sec-WebSocket-Accept: $key\r\n\r\n";

//socket_write($user->socket, $upgrade . chr(0), strlen($upgrade . chr(0)));
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

// function getheaders($req){
  // $req  = substr($req,4); /* RegEx kill babies */
  // $res  = substr($req,0,strpos($req," HTTP"));
  // $req  = substr($req,strpos($req,"Host:")+6);
  // $host = substr($req,0,strpos($req,"\r\n"));
  // $req  = substr($req,strpos($req,"Origin:")+8);
  // $ori  = substr($req,0,strpos($req,"\r\n"));
  // return array($res,$host,$ori);
// }

function getuserbysocket($socket){
  global $users;
  $found=null;
  foreach($users as $user){
    if($user->socket==$socket){ $found=$user; break; }
  }
  return $found;
}

function  say($msg=""){ echo "DIJO: ".$msg."\n"; } //OJO
//function    wrap($msg=""){ return chr(0).$msg.chr(255); }
function  unwrap($msg=""){ return substr($msg,1,strlen($msg)-2); }
function console($msg=""){ global $debug; if($debug){ echo $msg."\n"; } }

class User{
  var $id;
  var $socket;
  var $handshake;
}

?>