<?php

	include ("../inc/conexionRack.php");
	
	/* crea un array con datos arbitrarios que seran enviados de vuelta a la aplicacion */
	$resultados = array();
	$resultados["hora"] = date("F j, Y, g:i a");
	$resultados["generador"] = "Enviado desde revolucion.mobi" ;
	
	
	/* Extrae los valores enviados desde la aplicacion movil */
	$usuarioEnviado = $_GET['usuario'];
	$passwordEnviado = $_GET['password'];
	
	
	//echo "<br>HOLLLLLLLAAA    $ubica<br>";
	
	if (!$conn) {
	$m = ocierror();
	echo $m['message']."<br>";
	echo "&nbsp; &nbsp; &nbsp; Error conectando a la Base de Datos<br><br>";
	exit;
	} 
	else {
		
		//  RECUPERANDO LOS VALORES
		$QrySec="Select NOMBRE, TEL, POSICION from TBL_CELULAR WHERE USUARIO LIKE '$usuarioEnviado' AND PSW LIKE '$passwordEnviado'";
		$RecSec=oci_parse($conn, $QrySec);
		oci_execute($RecSec);
		$i=0;
		$resultados["datos"]="<table border='1'>  <tr><td><b>NOMBRE</b></td><td><b>TELEFONO</b></td><td><b>COORDENA</b></td></tr>";
		while($rowSec=oci_fetch_array($RecSec)){
			$resultados["nombre"][$i] = $rowSec["NOMBRE"];
			$resultados["tel"][$i] = $rowSec["TEL"];
			$resultados["posicion"][$i] = $rowSec["POSICION"];
			
			$resultados["datos"].="<tr><td>$rowSec[NOMBRE]</td><td>$rowSec[TEL]</td><td id='pos$i'>$rowSec[POSICION]</td></tr>";
			
			$i++;
		}
		oci_free_statement($RecSec);
		
		$resultados["datos"].="</table>
		<br><br>

		";
							
		if($i <= 0){
			
			$resultados["mensaje"] = "NO TIENE REGISTRO GUARDADOS";
			$resultados["validacion"] = "$QrySec";
		}
		else{
			/*esta informacion se envia solo si la validacion es correcta */
			$resultados["mensaje"] = "$QrySec";
			$resultados["validacion"] = "ok";
			$resultados["cuantos"] = $i;
		}			
		
		
	}
 
	oci_close($conn);
 

 
/*convierte los resultados a formato json*/
$resultadosJson = json_encode($resultados);
 
/*muestra el resultado en un formato que no da problemas de seguridad en browsers */
echo $_GET['jsoncallback'] . '(' . $resultadosJson . ');';
 
?>
