<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // The request is using only the POST method
    header('Location: index.html');
}

//generar archivo xml
$userBdMysql = 'root';
$passBdMysql = '123456';
$mysqlHost = '127.0.0.1';
$mysqlDB = 'avianca';

$userBdPgsql = 'postgres';
$passBdPgsql = '123456';
$pgSqlHost = '127.0.0.1';
$pgSqlDB = 'vuelos_lan';

$_SESSION['mensajes'] = null;
$mensajesSession = [];
$origenVuelo = '%'.$_POST['origen'].'%';
$destinoVuelo = '%'.$_POST['destino'].'%';
$fechaPartida = $_POST['fecha_partida'];
$fechaRetorno = $_POST['fecha_retorno'];
$tipo = $_POST['tipo'];

$bdAvianca = new mysqli($mysqlHost, $userBdMysql, $passBdMysql, $mysqlDB);
// Check connection
if ($bdAvianca->connect_error) {
    // die("Error al conectar con MySQL-> ".mysql_error());
    $bdAvianca = null;
    array_push($mensajesSession, 'Base de datos de avianca fuera de servicio.');
}
$bdLan = pg_connect("host=".$pgSqlHost." dbname=".$pgSqlDB." user=".$userBdPgsql." password=".$passBdPgsql);
if ($bdLan == FALSE) {
    // or die('No se ha podido conectar: ' . pg_last_error())
    $bdLan = null;
    array_push($mensajesSession, 'Base de datos de Lan fuera de servicio.');
}

$xml = new DomDocument('1.0', 'UTF-8');
$xml->preserveWhiteSpace = false;
$xml->formatOutput = true;
$vuelos = $xml->createElement('vuelos');
$vuelos = $xml->appendChild($vuelos);

if($bdAvianca != null){
    $querySQL = "SELECT aeropuerto, origen AS lugar_origen, destino AS lugar_destino, `fechaIda` AS fecha, CONCAT(`horaVuelo`, ' HRS') AS hora, precio FROM vuelo WHERE origen LIKE ? AND destino LIKE ?";
    if(!is_null($fechaPartida) && $fechaPartida != "" && !is_null($fechaRetorno) && $fechaRetorno != ""){
        $querySQL = $querySQL." AND (fechaIda=? OR fechaIda=?) ";
    }
    if(!is_null($fechaPartida) && $fechaPartida != "" && (is_null($fechaRetorno) || $fechaRetorno == "")){
        $querySQL = $querySQL." AND fechaIda=?";
    }
    if((is_null($fechaPartida) || $fechaPartida == "") && !is_null($fechaRetorno) && $fechaRetorno != ""){
        $querySQL = $querySQL." AND fechaIda=?";
    }
    $queryAvianca = $bdAvianca->prepare($querySQL);
    if(!is_null($fechaPartida) && $fechaPartida != "" && !is_null($fechaRetorno) && $fechaRetorno != ""){
        $queryAvianca->bind_param('ssss', $origenVuelo, $destinoVuelo, $fechaPartida, $fechaRetorno);
    }
    if(!is_null($fechaPartida) && $fechaPartida != "" && (is_null($fechaRetorno) || $fechaRetorno == "")){
        $queryAvianca->bind_param('sss', $origenVuelo, $destinoVuelo, $fechaPartida);
    }
    if((is_null($fechaPartida) || $fechaPartida == "") && !is_null($fechaRetorno) && $fechaRetorno != ""){
        $queryAvianca->bind_param('sss', $origenVuelo, $destinoVuelo, $fechaRetorno);
    }
    if((is_null($fechaPartida) || $fechaPartida == "") && (is_null($fechaRetorno) || $fechaRetorno == "")){
        $queryAvianca->bind_param('ss', $origenVuelo, $destinoVuelo);
    }
    
    $queryAvianca->execute();
    $queryAvianca->store_result();
    $queryAvianca->bind_result($aeropuerto, $lugar_origen, $lugar_destino, $fecha, $hora, $precio);
    
    
    while($queryAvianca->fetch()) {
        $vuelo = $xml->createElement('vuelo');
        $vuelo = $vuelos->appendChild($vuelo);
        $nodo_aerolinea = $xml->createElement('aerolinea', 'AVIANCA');
        $nodo_aerolinea = $vuelo->appendChild($nodo_aerolinea);
        $nodo_aeropuerto = $xml->createElement('aeropuerto', $aeropuerto);
        $nodo_aeropuerto = $vuelo->appendChild($nodo_aeropuerto);
        $nodo_lugar_origen = $xml->createElement('lugar_origen', $lugar_origen);
        $nodo_lugar_origen = $vuelo->appendChild($nodo_lugar_origen);
        $nodo_lugar_destino = $xml->createElement('lugar_destino', $lugar_destino);
        $nodo_lugar_destino = $vuelo->appendChild($nodo_lugar_destino);
        $nodo_fecha = $xml->createElement('fecha', $fecha);
        $nodo_fecha = $vuelo->appendChild($nodo_fecha);
        $nodo_hora = $xml->createElement('hora', $hora);
        $nodo_hora = $vuelo->appendChild($nodo_hora);
        $nodo_precio = $xml->createElement('precio', $precio);
        $nodo_precio = $vuelo->appendChild($nodo_precio);
    }
    
    $queryAvianca->free_result();
    $bdAvianca->close();
}

if($bdLan != null ){
    $querySQL = "SELECT airport AS aeropuerto, flight_source AS lugar_origen, flight_destination AS lugar_destino,";
    $querySQL = $querySQL." CAST(flight_date AS DATE) AS fecha, to_char(CAST(flight_date AS TIME),'HH24:MI HRS') AS hora, price AS precio FROM flights ";
    $querySQL = $querySQL." WHERE flight_source LIKE $1 AND flight_destination LIKE $2";
    if(!is_null($fechaPartida) && $fechaPartida != "" && !is_null($fechaRetorno) && $fechaRetorno != ""){
        $querySQL = $querySQL." AND (CAST(flight_date AS DATE)=$3 OR CAST(flight_date AS DATE)=$4) ";
    }
    if(!is_null($fechaPartida) && $fechaPartida != "" && (is_null($fechaRetorno) || $fechaRetorno == "")){
        $querySQL = $querySQL." AND CAST(flight_date AS DATE)=$3";
    }
    if((is_null($fechaPartida) || $fechaPartida == "") && !is_null($fechaRetorno) && $fechaRetorno != ""){
        $querySQL = $querySQL." AND CAST(flight_date AS DATE)=$3";
    }
    
    $queryLan = pg_prepare($bdLan, "my_query", $querySQL);
    $result = null;

    if(!is_null($fechaPartida) && $fechaPartida != "" && !is_null($fechaRetorno) && $fechaRetorno != ""){
        $result = pg_execute($bdLan, "my_query", array($origenVuelo, $destinoVuelo, $fechaPartida, $fechaRetorno));
    }
    if(!is_null($fechaPartida) && $fechaPartida != "" && (is_null($fechaRetorno) || $fechaRetorno == "")){
        $result = pg_execute($bdLan, "my_query", array($origenVuelo, $destinoVuelo, $fechaPartida));
    }
    if((is_null($fechaPartida) || $fechaPartida == "") && !is_null($fechaRetorno) && $fechaRetorno != ""){
        $result = pg_execute($bdLan, "my_query", array($origenVuelo, $destinoVuelo, $fechaRetorno));
    }
    if((is_null($fechaPartida) || $fechaPartida == "") && (is_null($fechaRetorno) || $fechaRetorno == "")){
        $result = pg_execute($bdLan, "my_query", array($origenVuelo, $destinoVuelo));
    }
    
    while($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
        $vuelo = $xml->createElement('vuelo');
        $vuelo = $vuelos->appendChild($vuelo);
        $nodo_aerolinea = $xml->createElement('aerolinea', 'LAN');
        $nodo_aerolinea = $vuelo->appendChild($nodo_aerolinea);
        $nodo_aeropuerto = $xml->createElement('aeropuerto', $row['aeropuerto']);
        $nodo_aeropuerto = $vuelo->appendChild($nodo_aeropuerto);
        $nodo_lugar_origen = $xml->createElement('lugar_origen', $row['lugar_origen']);
        $nodo_lugar_origen = $vuelo->appendChild($nodo_lugar_origen);
        $nodo_lugar_destino = $xml->createElement('lugar_destino', $row['lugar_destino']);
        $nodo_lugar_destino = $vuelo->appendChild($nodo_lugar_destino);
        $nodo_fecha = $xml->createElement('fecha', $row['fecha']);
        $nodo_fecha = $vuelo->appendChild($nodo_fecha);
        $nodo_hora = $xml->createElement('hora', $row['hora']);
        $nodo_hora = $vuelo->appendChild($nodo_hora);
        $nodo_precio = $xml->createElement('precio', $row['precio']);
        $nodo_precio = $vuelo->appendChild($nodo_precio);
    }
}

$xml->save('vuelos.xml');
$_SESSION['mensajes'] = $mensajesSession;

header('Location: mostrar-vuelos.php');