var server = require("socket.io").listen(6969);
//var usuarios_conectados = []; 
//var mesas = []; //contiene todas las mesas - partidas de jugadores
//var estadovideos = []; //contiene el estado de los videos de la partida de los jugadores

//Array is not meant to be used for key/value pairs.
var usuarios_conectados = {};
var mesas = {};  				// mesaid:[jugid1,jugid2]
var estadovideos = {};			// mesaid:{jugid:{'texto':''},jugid:{'texto':''}}
var decisionesmesa = {};		// mesaid:{jugid:{'respuesta':''},jugid:{'respuesta':''}}
//var decisionesmesa =[]; //contiene las repuestas que envìan los jugadores de su partida

function User(socket) {
  this.socket = socket;
  this.jug_id = 0;
  this.mesa_id = "";
}
User.prototype = {
  imprimirUser: function () {
    return "Hello world, my jugid is " + this.jug_id + " - socket_id "+this.socket.id;
  },
  getJugId: function () {
    return this.jug_id;
  },
  setJugId: function (jug_id) {
    this.jug_id=jug_id;
  },
  getSocket: function () {
    return this.socket;
  },
  setSocket: function (socket) {
    this.socket=socket;
  },
  getMesaId: function () {
    return this.mesa_id;
  },
  setMesaId: function (mesa_id) {
    this.mesa_id=mesa_id;
  }
};

//CONNECT
server.sockets.on("connection", function(client)
{
	console.log('Got connect User:!'+client.id);
	usuarios_conectados[""+client.id]=new User(client); //add userobject
	console.log(usuarios_conectados);
	//usuarios_conectados.push(client);
	
	//MESSAGE
    client.on("mensaje", function(data)
    {
		var obj=JSON.parse(data);
		//obj=JSON.parse(obj);
		var keys=Object.keys(obj.objeto);
        var key=keys[0];
        var value=obj.objeto[key];
		switch (obj.tipo)
		{
			case "identificacion"://en caso de sincronización emitir a ambos, completo e incompleto el estado de sus relacionesmesavideo
				var estado_mesa="";
				var user_actual;
				var jugid=value;
								
				console.log('Ingresó Jugador: '+jugid);
				
				//************ USUARIOS_CONECTADOS [jugid de user] Actualizar 
				user_actual=usuarios_conectados[""+client.id];
				user_actual.setJugId(jugid);				
				
				//******** MESAS [mesaid - jugid]
				if(mesas[""+key]==null) {
					mesas[""+key]=new Array();
				}	
				
				//MESA: poner en mesa el arreglo de usuarios 
				mesas[""+key].push(user_actual); 
				user_actual.setMesaId(""+key);				
				
				//************ ESTADOVIDEOS [mesaid - jugid -estado]
				if(estadovideos[""+key]==null) {				
					estadovideos[""+key]={};
				}
							
				//estadovideo_ SINCRONIZACIONES esta_sincronizado round
				if(estadovideos[""+key]["esta_sincronizado"]==null) {				
					estadovideos[""+key]["esta_sincronizado"]="";
				}			
				
				//actualiza jugid obj 			
				estadovideos[""+key][""+jugid] = {};
				estadovideos[""+key][""+jugid]["texto"] = "";
				
				//************ DECISIONESMESA [mesaid - jugid -respuesta]				
				if(decisionesmesa[""+key]==null) {				
					decisionesmesa[""+key]={};
				}
							
				//actualiza jugid obj 			
				decisionesmesa[""+key][""+jugid] = {};
				decisionesmesa[""+key][""+jugid]["respuesta"] = "";
				
				//verificar estado de la mesa	
				if(mesas[""+key][0]!=null && mesas[""+key][1]!=null) estado_mesa="COMPLETO";
				else estado_mesa="INCOMPLETO";	
				
				console.log('Mesa '+key+' contenido es: '+mesas[""+key]);
				if(estado_mesa=="COMPLETO"){
					destinatario1=mesas[""+key][0].socket; 
					destinatario2=mesas[""+key][1].socket;
										
					destinatario1.emit("sendEvent", '{"tipo":"conexion","objeto":{"'+key+'": "'+estado_mesa+'"}}');
					destinatario2.emit("sendEvent", '{"tipo":"conexion","objeto":{"'+key+'": "'+estado_mesa+'"}}');
				}else{
					client.emit("sendEvent", '{"tipo":"conexion","objeto":{"'+key+'": "'+estado_mesa+'"}}');
				}
				//server.sockets.emit("sendEvent", '{"tipo":"conexion","objeto":{"'+key+'": "'+estado_mesa+'"}}');	
			break;
			case "same-different":				
				var jugid=value.atributo1;
				var respuesta=value.atributo2;
				var puntaje=0;
				if(mesas[""+key]!=null){
					if(mesas[""+key][0]!=null && mesas[""+key][1]!=null){	
						
						if(decisionesmesa[""+key]!=null){
							console.log("Decisionmesa no null "+decisionesmesa[""+key]);
							//editar la decision del jug de la mesa
							if(decisionesmesa[""+key][""+jugid]!=null) {
								console.log("Decisionmesa JUG decision no null "+decisionesmesa[""+key][""+jugid]);
								decisionesmesa[""+key][""+jugid]["respuesta"] = respuesta;
								console.log("Su respuesta se cambio a "+decisionesmesa[""+key][""+jugid]["respuesta"]);															
							}
							//verificar los estados de videos de esta mesa
							var keys=Object.keys(decisionesmesa[""+key]); //keys de este obj
							if(keys!=null){	
								console.log("Los 2 estan registrados "+decisionesmesa[""+key]);
								var key_jug1=keys[0];
								var key_jug2=keys[1];
								if(key_jug1!=null && key_jug2!=null){
									console.log("Ambos idjugs "+decisionesmesa[""+key]);
									var decision1=decisionesmesa[""+key][key_jug1]["respuesta"];
									var decision2=decisionesmesa[""+key][key_jug2]["respuesta"];
									
									/*************************** Posibles Destinatarios ***************************/
									destinatario1=mesas[""+key][0].socket; 
									destinatario2=mesas[""+key][1].socket;
									
									// ya contestaron ambos
									if(decision1!="" && decision2!=""){	
									
										/*Envío de mensaje a ambos*/
										console.log("Ambos tienen respuestas: "+decision1+" - "+decision2);
										
										// si ambos iguales y esa respuesta es ACERTO jug1 y ACERTO jug2
										if(decision1==decision2 && decision1=="ACIERTO") puntaje=100;
										else puntaje=0;										
										
										/*
										destinatario1=mesas[""+key][0].socket; 
										destinatario2=mesas[""+key][1].socket;
										*/
										
										//destinatario1.emit("sendEvent", '{"tipo":"same-different","objeto":{"'+key+'": "'+puntaje+'"}}');
										//destinatario2.emit("sendEvent", '{"tipo":"same-different","objeto":{"'+key+'": "'+puntaje+'"}}');
										
										if(destinatario1.id==client.id){
											//su respuesta es la que está como decision1, la otra es de su partner
											destinatario1.emit("sendEvent", '{"tipo":"same-different","objeto":{"'+key+'": {"puntaje":"'+puntaje+'","jugtu":"'+decision1+'","jugpartner":"'+decision2+'"}}}');
											destinatario2.emit("sendEvent", '{"tipo":"same-different","objeto":{"'+key+'": {"puntaje":"'+puntaje+'","jugtu":"'+decision2+'","jugpartner":"'+decision1+'"}}}');
										}else{
											//su respuesta es la que está como decision2, la otra es de su partner
											destinatario1.emit("sendEvent", '{"tipo":"same-different","objeto":{"'+key+'": {"puntaje":"'+puntaje+'","jugtu":"'+decision2+'","jugpartner":"'+decision1+'"}}}');
											destinatario2.emit("sendEvent", '{"tipo":"same-different","objeto":{"'+key+'": {"puntaje":"'+puntaje+'","jugtu":"'+decision1+'","jugpartner":"'+decision2+'"}}}');
										}
										
										console.log("ya coontestaron, puntaje es: "+puntaje);
										
										//limpiar respuestas de ambos
										decisionesmesa[""+key][key_jug1]["respuesta"]="";
										decisionesmesa[""+key][key_jug2]["respuesta"]="";
										
									}else{
										if(decision1=="ACIERTO" || decision2=="ACIERTO"){									
											/*Envío de mensaje a Compañero de Jug actual*/
											if(destinatario1.id==client.id){
												//mensaje a su partner									
												destinatario2.emit("sendEvent", '{"tipo":"same-different-incompleto","objeto":{"'+key+'": "CONTESTO"}}');
											}else{
												//mensaje a su partner
												destinatario1.emit("sendEvent", '{"tipo":"same-different-incompleto","objeto":{"'+key+'": "CONTESTO"}}');
											}
										}	
									}
								}
							}
						}
					}	
					console.log("SAME-DIFFERNT:.: "+key +" - "+jugid+" - "+respuesta);
				}
			break;
			case "sincronizacion-videos":		//en caso de sincronización emitir a ambos, completo e incompleto el estado de sus relacionesmesavideo
				//server.sockets.emit("sendEvent", '{"tipo":"coordenadas","objeto":{"'+key+'": {"posx":"'+value.posx+'","posy":"'+value.posy+'"}}}');	
				var jugid=value.atributo1;
				var estado_video=value.atributo2;
				if(mesas[""+key]!=null){
					if(mesas[""+key][0]!=null && mesas[""+key][1]!=null){	
						if(estadovideos[""+key]!=null){
							//editar el estado
							if(estadovideos[""+key][""+jugid]!=null) estadovideos[""+key][""+jugid]["texto"] = estado_video;
							
							//verificar los estados de videos de esta mesa
							var keys=Object.keys(estadovideos[""+key]); //keys de este obj
							if(keys!=null){
								var key_jug1=keys[0];
								var key_jug2=keys[1];
								if(key_jug1!=null && key_jug2!=null){
									var estado_video1=estadovideos[""+key][key_jug1]["texto"];
									var estado_video2=estadovideos[""+key][key_jug2]["texto"];																	
									
									if(estado_video1=="COMPLETO" && estado_video2=="COMPLETO" && estadovideos[""+key]["esta_sincronizado"]!="1"){
										// si ambos estados están iguales																				
										destinatario1=mesas[""+key][0].socket; 
										destinatario2=mesas[""+key][1].socket;
										
										destinatario1.emit("sendEvent", '{"tipo":"sincronizacion-completa","objeto":{"'+key+'": "'+estado_video+'"}}');
										destinatario2.emit("sendEvent", '{"tipo":"sincronizacion-completa","objeto":{"'+key+'": "'+estado_video+'"}}');														
										
										console.log("LISTO! SINCRONIZADOS: sincronizacion-videos "+estado_video);
										
										//limpiar estados de ambos
										//estadovideos[""+key][key_jug1]["texto"]="";
										//estadovideos[""+key][key_jug2]["texto"]="";
										estadovideos[""+key]["esta_sincronizado"]="1";
									}
								}
							}
						}
					}	
					console.log("************ ingresoo a sincronizacion-videos "+key +" - "+jugid+" - "+estado_video);
				}				
			break;
			case "reiniciar_estado_videos":
				var jugid=value.atributo1;
				var mensaje=value.atributo2;
				if(mesas[""+key]!=null){
					if(mesas[""+key][0]!=null && mesas[""+key][1]!=null){	
						if(estadovideos[""+key]!=null){
							//reiniciar, editar -> esta_sincronizado?
							if(estadovideos[""+key]["esta_sincronizado"]!=null) estadovideos[""+key]["esta_sincronizado"]=mensaje;
							//editar el estado
							if(estadovideos[""+key][""+jugid]!=null) estadovideos[""+key][""+jugid]["texto"] = "";							
						}
					}	
					console.log("************ ingresoo a reiniciar_estado_videos "+key +" - "+jugid+" - "+mensaje);
				}
			break;
			case "mensajes":	//en caso mensajes, no mandar a client this, sino a su partner no más
				var socket_destinatario;
				console.log('Mensaje enviado'); 
				if(mesas[""+key]!=null){
					if(mesas[""+key][0]!=null && mesas[""+key][1]!=null){
						if(mesas[""+key][0].socket.id!=client.id)
							socket_destinatario=mesas[""+key][0].socket; //este es el partner
						else
							socket_destinatario=mesas[""+key][1].socket; //este es el partner
						//enviar mensaje al partner no a él mismo	
						socket_destinatario.emit("sendEvent", '{"tipo":"mensajes","objeto":{"'+key+'": "'+value+'"}}');	
					}											
				}				
			break;
			default:
				server.sockets.emit("sendEvent", '{"tipo":"mensajes","objeto":{"'+key+'": "'+obj.tipo+":"+value+'"}}'); 
		}
		console.log('Mesas contenido es: '+mesas);
        //server.sockets.emit("sendEvent", data); //todos los sockets conectados a este server
		//client.emit('sendEvent', { mensaje: data });
		//client.emit('sendEvent',data); //emitir a este cliente
    });

	//DISCONNECT
	client.on('disconnect', function() {
		console.log('Got disconnect!'+client.id);
		//var i = usuarios_conectados.indexOf(client);
		//delete usuarios_conectados[i];
		
		//eliminar al usuario de usuarios-conectados
		var user_actual=usuarios_conectados[""+client.id];
		var key_mesa=""+user_actual.getMesaId();
		delete usuarios_conectados[""+client.id];
		console.log("Se elimina este jugador");
		
		//eliminar sus sets a partir de su mesaid, y aunque tenga compañero de juego
		if(key_mesa!=null && key_mesa!=""){
			delete mesas[""+key_mesa];
			delete estadovideos[""+key_mesa];
			delete decisionesmesa[""+key_mesa];
			console.log("Y se acaba de eliminar sus sets");
		}
		
		console.log("CONECTADOS: "+usuarios_conectados);
	});

});