<?php

	include ("../inc/conexionRack.php");
	
	/* Extrae los valores enviados desde la aplicacion movil */
	$usuarioEnviado = strtoupper($_GET['usuario']);
	$passwordEnviado = $_GET['password'];
	
	if (!$conn) {
	$m = ocierror();
	echo $m['message']."<br>";
	echo "&nbsp; &nbsp; &nbsp; Error conectando a la Base de Datos<br><br>";
	exit;
	} 
	else {
		
		$iResp = 0;
		$txtSql7_ACC="SELECT COUNT(1)ESTA FROM CT_USUARIOS_CEL WHERE CVE_USUARIO = '$usuarioEnviado' AND CVE_PASS = '$passwordEnviado'";
		//echo $txtSql7;
		$RecSql7_ACC = oci_parse($conn, $txtSql7_ACC);
		oci_execute($RecSql7_ACC);
		if($rowSec7_ACC=oci_fetch_array($RecSql7_ACC)){	
			$iResp = $rowSec7_ACC["ESTA"];
			
		}		
		oci_free_statement($RecSql7_ACC);
		
		
	}
 

 oci_close($conn);

 
/* crea un array con datos arbitrarios que seran enviados de vuelta a la aplicacion */
$resultados = array();
$resultados["hora"] = date("F j, Y, g:i a");
//$resultados["generador"] = "Enviado desde revolucion.mobi" ;
$resultados["generador"] = $iResp ;
 
 
/* verifica que el usuario y password concuerden correctamente */
if( $iResp >= 1 ){
	/*esta informacion se envia solo si la validacion es correcta */
	$resultados["mensaje"] = "Validacion Correcta";
	$resultados["validacion"] = "ok";
 
}else{
	/*esta informacion se envia si la validacion falla */
	$resultados["mensaje"] = "Usuario y password incorrectos";
	$resultados["validacion"] = "error";
}
 
 
/*convierte los resultados a formato json*/
$resultadosJson = json_encode($resultados);
 
/*muestra el resultado en un formato que no da problemas de seguridad en browsers */
echo $_GET['jsoncallback'] . '(' . $resultadosJson . ');';
 
?>
