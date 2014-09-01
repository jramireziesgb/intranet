<?php
session_start();
include("../../config.php");
// COMPROBAMOS LA SESION
if ($_SESSION['autentificado'] != 1) {
	$_SESSION = array();
	session_destroy();
	header('Location:'.'http://'.$dominio.'/intranet/salir.php');
	exit();
}

if($_SESSION['cambiar_clave']) {
	header('Location:'.'http://'.$dominio.'/intranet/clave.php');
}

if ((stristr($_SESSION['cargo'],'1') == false) && (stristr($_SESSION['cargo'],'2') == false)) {
	die ("<h1>FORBIDDEN</h1>");
}

if (isset($_GET['id'])) $id = $_GET['id'];

if (!$id) {
	die ("<h1>FORBIDDEN</h1>");
}

require_once("../../pdf/dompdf_config.inc.php"); 

// REGISTRAMOS LA ACCION
mysql_query("UPDATE evaluaciones_actas SET impresion=1 WHERE id=$id");

// OBTENEMOS LOS DATOS
$result = mysql_query("SELECT unidad, evaluacion, texto_acta FROM evaluaciones_actas WHERE id=$id");

if (mysql_num_rows($result)) {
	$row = mysql_fetch_array($result);
	
	$unidad = $row['unidad'];
	$evaluacion = $row['evaluacion'];
	$texto_acta = $row['texto_acta'];
	$texto_acta = '<style type="text/css">
	body {
		font-size: 10pt;
	}
	#footer {
		position: fixed;
	 left: 0;
		right: 0;
		bottom: 0;
		color: #aaa;
		font-size: 0.9em;
		text-align: right;
	}
	.page-number:before {
	  content: counter(page);
	}
	</style>
	<div id="footer">
	  P�gina <span class="page-number"></span>
	</div>'.$texto_acta;
	$html = mb_convert_encoding($texto_acta, 'UTF-8', 'ISO-8859-1');
	
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->render();
	$dompdf->stream("Acta de evaluaci�n $evaluacion - $unidad.pdf", array("Attachment" => 0));
}
?>