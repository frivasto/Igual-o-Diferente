<?php
	$json_recibido='{"tipo":"identificacion","objeto":{"usuario_id": "abc24", "mesa_id": 1}}';
	echo "<br />Arreglo 1:: ";
	print_r(json_decode($json_recibido, TRUE)); //Cuando es TRUE, los objects retornados se convertirán en arrays asociativos.
	
	$json_recibido='{"tipo":"mensajes","objeto":{"mensaje": "holaaa", "mesa_id": 1}}';
	echo "<br />Arreglo 2:: ";
	$arreglo2=json_decode($json_recibido, TRUE);
	print_r($arreglo2); //Cuando es TRUE, los objects retornados se convertirán en arrays asociativos.
	echo "<br />Objeto:: ";
	print_r($arreglo2["objeto"]);
	echo "<br />Objeto mesa_id:: ";
	print_r($arreglo2["objeto"]["mesa_id"]);
	
	$json_recibido='{"tipo":"identificacion","objeto":{"mesa1": "holaaaa"}}';
	echo "<br />Arreglo 3:: ";
	$arreglo2=json_decode($json_recibido, TRUE);
	print_r($arreglo2); //Cuando es TRUE, los objects retornados se convertirán en arrays asociativos.
	echo "<br />Objeto mesa_id MENSAJE:: "; //CLAVE : VALOR
	print_r($arreglo2["objeto"]["mesa1"]);
		
	//{"tipo":"identificacion","objeto":{"mesa1": "usuario123"}}
	//{"tipo":"mensajes","objeto":{"mesa1": "holaaaa"}}
	print_r(array_keys($arreglo2["objeto"]));
	
	$keys=array_keys($arreglo2["objeto"]);
	$mesaid=$keys[0];
	echo "<br />*key primero es:: ".$mesaid;
	echo "<br />*value de es primero es:: ".$arreglo2["objeto"][$mesaid];

	
	//----------------------------- json encode
	$response=array(); $idmesa='23'; $iduser='assie3934';
	$response['tipo']="identificacion";
    $response['objeto']=array();
    $response['objeto'][$idmesa]=$iduser; 
	echo json_encode($response);
?>


