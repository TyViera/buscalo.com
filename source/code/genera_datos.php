<?php
$userBdMysql = 'root';
$passBdMysql = '123456';

$userBdPgsql = 'postgres';
$passBdPgsql = '123456';

$bdlan = new mysqli('localhost', $userBdMysql, $passBdMysql, 'lan')or die("Error al conectar con MySQL-> ".mysql_error());

$bdperuvian = new mysqli('localhost', $userBdMysql, $passBdMysql, 'peruvian_airlines')or die("Error al conectar con MySQL-> ".mysql_error());

$link = pg_connect("host=localhost dbname=avianca user=".$userBdPgsql." password=".$passBdPgsql) or die('No se ha podido conectar: ' . pg_last_error());


// Realizando una consulta SQL
$query1 = 'SELECT vuelo, rtrim(origen) origen, rtrim(dest) dest, fechita, costo, rtrim(servicio) servicio FROM itinerarios';
$result = pg_query($query1) or die('La consulta fallo: ' . pg_last_error());


//  Para MySQL BD LAN *********************************************************************************************

       $stmt = $bdlan->prepare("SELECT vuelo.idvuelo, precio, fecha, tipo_vuelo, lugar_origen, lugar_destino
			  FROM vuelo INNER JOIN detalle_vuelos on(vuelo.idvuelo=detalle_vuelos.idvuelo)");
       $stmt->execute();
       $stmt->store_result();
       $stmt->bind_result($idvuelo, $precio, $fecha, $tipo_vuelo, $lugar_origen, $lugar_destino);

        $xml = new DomDocument('1.0', 'UTF-8');

      //to have indented output, not just a line
         $xml->preserveWhiteSpace = false;
         $xml->formatOutput = true;

// ------------- Interresting part here ------------

//creating an xslt adding processing line
$xslt = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="vuelos-xslt.xsl"');

//adding it to the xml
     $xml->appendChild($xslt);

      $vuelos = $xml->createElement('vuelos');
      $vuelos = $xml->appendChild($vuelos);
// ***************************************************************************************************************


//Para MySQL BD Peruvian *******************************************************************************************
      $query = $bdperuvian->prepare("SELECT num_vuelo,precio,fecha,tipo_servicio,origen,destino FROM vuelos");
       $query->execute();
       $query->store_result();
       $query->bind_result($num_vuelo, $precio, $fecha, $tipo_servicio, $origen, $destino);

        $xml = new DomDocument('1.0', 'UTF-8');

      $vuelos = $xml->createElement('vuelos');
      $vuelos = $xml->appendChild($vuelos);
//  *****************************************************************************************************************



// *********** POSTGRES *********************************************************************************************
while($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
	 $vuelo = $xml->createElement('vuelo');
     $vuelo = $vuelos->appendChild($vuelo);
	 $nodo_aerolinea = $xml->createElement('aerolinea', 'AVIANCA');
	 $nodo_aerolinea = $vuelo->appendChild($nodo_aerolinea);
	 $nodo_idvuelo = $xml->createElement('idvuelo', $row['vuelo']);
	 $nodo_idvuelo = $vuelo->appendChild($nodo_idvuelo);
	 $nodo_precio = $xml->createElement('precio', $row['costo']);
	 $nodo_precio = $vuelo->appendChild($nodo_precio);
	 $nodo_fecha = $xml->createElement('fecha', $row['fechita']);
	 $nodo_fecha = $vuelo->appendChild($nodo_fecha);
	 $nodo_tipo_vuelo = $xml->createElement('tipo_vuelo', $row['servicio']);
	 $nodo_tipo_vuelo = $vuelo->appendChild($nodo_tipo_vuelo);
	 $nodo_lugar_origen = $xml->createElement('lugar_origen', $row['origen']);
	 $nodo_lugar_origen = $vuelo->appendChild($nodo_lugar_origen);
	 $nodo_lugar_destino = $xml->createElement('lugar_destino', $row['dest']);
	 $nodo_lugar_destino = $vuelo->appendChild($nodo_lugar_destino);
       }

 // ******************************************************************************************************************



 // ******************************************************************************************************************
      while($stmt->fetch()) {

        $vuelo = $xml->createElement('vuelo');
        $vuelo = $vuelos->appendChild($vuelo);

        $nodo_aerolinea = $xml->createElement('aerolinea', 'LAN');
        $nodo_aerolinea = $vuelo->appendChild($nodo_aerolinea);
        $nodo_idvuelo = $xml->createElement('idvuelo', $idvuelo);
        $nodo_idvuelo = $vuelo->appendChild($nodo_idvuelo);
        $nodo_precio = $xml->createElement('precio', $precio);
        $nodo_precio = $vuelo->appendChild($nodo_precio);
        $nodo_fecha = $xml->createElement('fecha', $fecha);
        $nodo_fecha = $vuelo->appendChild($nodo_fecha);
        $nodo_tipo_vuelo = $xml->createElement('tipo_vuelo', $tipo_vuelo);
        $nodo_tipo_vuelo = $vuelo->appendChild($nodo_tipo_vuelo);
        $nodo_lugar_origen = $xml->createElement('lugar_origen', $lugar_origen);
        $nodo_lugar_origen = $vuelo->appendChild($nodo_lugar_origen);
        $nodo_lugar_destino = $xml->createElement('lugar_destino', $lugar_destino);
        $nodo_lugar_destino = $vuelo->appendChild($nodo_lugar_destino);
       }

       $stmt->free_result();
       $bdlan->close();
 // ******************************************************************************************************************



// *********************** Peruvian **********************************************************************************
    while($query->fetch()) {

        $vuelo = $xml->createElement('vuelo');
        $vuelo = $vuelos->appendChild($vuelo);

        $nodo_aerolinea = $xml->createElement('aerolinea', 'PERUVIAN AIRLINES');
        $nodo_aerolinea = $vuelo->appendChild($nodo_aerolinea);
        $nodo_idvuelo = $xml->createElement('idvuelo', $num_vuelo);
        $nodo_idvuelo = $vuelo->appendChild($nodo_idvuelo);
        $nodo_precio = $xml->createElement('precio', $precio);
        $nodo_precio = $vuelo->appendChild($nodo_precio);
        $nodo_fecha = $xml->createElement('fecha', $fecha);
        $nodo_fecha = $vuelo->appendChild($nodo_fecha);
        $nodo_tipo_vuelo = $xml->createElement('tipo_vuelo', $tipo_servicio);
        $nodo_tipo_vuelo = $vuelo->appendChild($nodo_tipo_vuelo);
        $nodo_lugar_origen = $xml->createElement('lugar_origen', $origen);
        $nodo_lugar_origen = $vuelo->appendChild($nodo_lugar_origen);
        $nodo_lugar_destino = $xml->createElement('lugar_destino', $destino);
        $nodo_lugar_destino = $vuelo->appendChild($nodo_lugar_destino);
       }

       $query->free_result();
       $bdperuvian->close();
// ****************************************************************************************************


 // ******************* DBF ********StarPeru ******************************************************************
$bbdd = '../../BDDATOS/lc.dbf';
$res=dbase_open($bbdd,0);
$ultimo = dbase_numrecords($res);
$num_campos=dbase_numfields($res);

for ($contador=1; $contador < $ultimo; $contador++) {
//for ($i=0; $i < 6; $i++) {
$rec = dbase_get_record($res, $contador);
     $vuelo = $xml->createElement('vuelo');
     $vuelo = $vuelos->appendChild($vuelo);
	 $nodo_aerolinea = $xml->createElement('aerolinea', 'StarPeru');
	 $nodo_aerolinea = $vuelo->appendChild($nodo_aerolinea);
	 $nodo_idvuelo = $xml->createElement('idvuelo', $rec[0]);
	 $nodo_idvuelo = $vuelo->appendChild($nodo_idvuelo);
	 $nodo_precio = $xml->createElement('precio', $rec[4]);
	 $nodo_precio = $vuelo->appendChild($nodo_precio);
	 $nodo_fecha = $xml->createElement('fecha', $rec[3]);
	 $nodo_fecha = $vuelo->appendChild($nodo_fecha);
	 $nodo_tipo_vuelo = $xml->createElement('tipo_vuelo', $rec[5]);
	 $nodo_tipo_vuelo = $vuelo->appendChild($nodo_tipo_vuelo);
	 $nodo_lugar_origen = $xml->createElement('lugar_origen', $rec[1]);
	 $nodo_lugar_origen = $vuelo->appendChild($nodo_lugar_origen);
	 $nodo_lugar_destino = $xml->createElement('lugar_destino', $rec[2]);
	 $nodo_lugar_destino = $vuelo->appendChild($nodo_lugar_destino);
//}
}

// ******************************************************************************************************************

   $xml->formatOutput = true;

      //$el_xml = $xml->saveXML();
      $xml->save('vuelos.xml');

    /*
    $xml = simplexml_load_file('vuelos.xml');
    $salida ="";

	leer();

    function leer(){
    echo "<p><b>Ahora mostrandolo con estilo</b></p>";

    $xml = simplexml_load_file('vuelos.xml');
    $salida ="";

    foreach($xml->vuelo as $item){
      $salida .=
        "<b>aerolinea:</b> " . $item->aerolinea . "<br/>".
		"<b>idvuelo:</b> " . $item->idvuelo . "<br/>".
        "<b>precio:</b> " . $item->precio . "<br/>".
        "<b>fecha:</b> " . $item->fecha . "<br/>".
		"<b>tipo_vuelo:</b> " . $item->tipo_vuelo . "<br/>".
		"<b>lugar_origen:</b> " . $item->lugar_origen . "<br/>".
        "<b>lugar_destino:</b> " . $item->lugar_destino . "<br/><hr/>";
    }

    echo $salida;
  }
	*/

  ?>