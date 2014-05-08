<?php

	include ("../inc/conexionRack.php");
	
	/* crea un array con datos arbitrarios que seran enviados de vuelta a la aplicacion */
	$resultados = array();
	$resultados["hora"] = date("F j, Y, g:i a");
	$resultados["generador"] = "Enviado desde revolucion.mobi" ;
	
	
	/* Extrae los valores enviados desde la aplicacion movil */
	$usuarioEnviado = utf8_decode($_GET['usuario']);
	$passwordEnviado = utf8_decode($_GET['password']);
	$sNombre = utf8_decode($_GET['sNombre']);
	$sTel = utf8_decode($_GET['sTel']);
	$ubica = utf8_decode($_GET['ubica_lat']);
	$ubica .= ",".utf8_decode($_GET['ubica_lon']);
	
	//echo "<br>HOLLLLLLLAAA    $ubica<br>";
	
	if (!$conn) {
	$m = ocierror();
	echo $m['message']."<br>";
	echo "&nbsp; &nbsp; &nbsp; Error conectando a la Base de Datos<br><br>";
	exit;
	} 
	else {
		
		// Incrementas el ID	
		$QrySec="Select S_TBL_CELULAR.NEXTVAL as INCREMENTA from DUAL";
		$RecSec=oci_parse($conn, $QrySec);
		oci_execute($RecSec);
		$rowSec=oci_fetch_array($RecSec);
		$iNumSec = $rowSec["INCREMENTA"];
		oci_free_statement($RecSec);
		
			
		
		$txtSql6="INSERT INTO TBL_CELULAR(ID_CEL, NOMBRE, TEL, POSICION, USUARIO, PSW)";
		$txtSql6.=" VALUES($iNumSec, '$sNombre', '$sTel', '$ubica', '$usuarioEnviado', '$passwordEnviado')";
		$RecSql6 = oci_parse($conn, $txtSql6);
		oci_execute($RecSql6);
		
		//echo $txtSql6;
		$band = 0;
		
		$txt2="select count(1)NR from TBL_CELULAR where ID_CEL=$iNumSec";	
		//echo "<br>".$txt2;		
		$rc=oci_parse($conn, $txt2);
		oci_execute($rc);
		$row=oci_fetch_array($rc);
		$mas = $row["NR"];
		if($mas <= 0){
			$gestor = fopen ("/var/www/html/PRODEM/celular/error.txt", "a");
			$sTexto=$txtSql6."\n\n";				
			fwrite($gestor, $sTexto);
			fclose ($gestor);
			/*esta informacion se envia si la validacion falla */
			$resultados["mensaje"] = "ERROR!!!!";
			$resultados["validacion"] = "error";
		}
		else{
			/*esta informacion se envia solo si la validacion es correcta */
			$resultados["mensaje"] = "GUARDADO!!!";
			$resultados["validacion"] = "ok";
			$resultados["id_registro"] = $iNumSec;
		}			
		
		
	}
 
	oci_close($conn);
 

 

 
 

 
/*convierte los resultados a formato json*/
$resultadosJson = json_encode($resultados);
 
/*muestra el resultado en un formato que no da problemas de seguridad en browsers */
echo $_GET['jsoncallback'] . '(' . $resultadosJson . ');';
 
?>
