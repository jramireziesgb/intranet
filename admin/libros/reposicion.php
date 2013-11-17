<? 
include ("../../config.php"); 
if (isset($_POST['niv'])) {$niv = $_POST['niv'];}else{$niv="";}	
include_once ("../../funciones.php"); 
include("../../pdf/fpdf.php");
define('FPDF_FONTPATH','../../pdf/font/');
# creamos la clase extendida de fpdf.php 
// Variables globales para el encabezado y pie de pagina
$GLOBALS['CENTRO_NOMBRE'] = $nombre_del_centro;
$GLOBALS['CENTRO_DIRECCION'] = $direccion_del_centro;
$GLOBALS['CENTRO_CODPOSTAL'] = $codigo_postal_del_centro;
$GLOBALS['CENTRO_LOCALIDAD'] = $localidad_del_centro;
$GLOBALS['CENTRO_TELEFONO'] = $telefono_del_centro;
$GLOBALS['CENTRO_FAX'] = $fax_del_centro;
$GLOBALS['CENTRO_CORREO'] = $email_del_centro;


if(substr($codigo_postal_del_centro,0,2)=="04") $GLOBALS['CENTRO_PROVINCIA'] = 'Almer�a';
if(substr($codigo_postal_del_centro,0,2)=="11") $GLOBALS['CENTRO_PROVINCIA'] = 'C�diz';
if(substr($codigo_postal_del_centro,0,2)=="14") $GLOBALS['CENTRO_PROVINCIA'] = 'C�rdoba';
if(substr($codigo_postal_del_centro,0,2)=="18") $GLOBALS['CENTRO_PROVINCIA'] = 'Granada';
if(substr($codigo_postal_del_centro,0,2)=="21") $GLOBALS['CENTRO_PROVINCIA'] = 'Huelva';
if(substr($codigo_postal_del_centro,0,2)=="23") $GLOBALS['CENTRO_PROVINCIA'] = 'Ja�n';
if(substr($codigo_postal_del_centro,0,2)=="29") $GLOBALS['CENTRO_PROVINCIA'] = 'M�laga';
if(substr($codigo_postal_del_centro,0,2)=="41") $GLOBALS['CENTRO_PROVINCIA'] = 'Sevilla';

# creamos la clase extendida de fpdf.php 
class GranPDF extends FPDF {
	function Header() {
		$this->Image ( '../../img/encabezado.jpg',15,15,50,'','jpg');
		$this->SetFont('ErasDemiBT','B',10);
		$this->SetY(15);
		$this->Cell(90);
		$this->Cell(80,4,'CONSEJER�A DE EDUCACI�N, CULTURA Y DEPORTE',0,1);
		$this->SetFont('ErasMDBT','I',10);
		$this->Cell(90);
		$this->Cell(80,4,$GLOBALS['CENTRO_NOMBRE'],0,1);
		$this->Ln(8);
	}
	function Footer() {
		$this->Image ( '../../img/pie.jpg', 10, 245, 25, '', 'jpg' );
		$this->SetY(265);
		$this->SetFont('ErasMDBT','',10);
		$this->SetTextColor(156,156,156);
		$this->Cell(70);
		$this->Cell(80,4,$GLOBALS['CENTRO_DIRECCION'],0,1);
		$this->Cell(70);
		$this->Cell(80,4,$GLOBALS['CENTRO_CODPOSTAL'].', '.$GLOBALS['CENTRO_LOCALIDAD'].' ('.$GLOBALS['CENTRO_PROVINCIA'] .')',0,1);
		$this->Cell(70);
		$this->Cell(80,4,'Tlf: '.$GLOBALS['CENTRO_TELEFONO'].'   Fax: '.$GLOBALS['CENTRO_FAX'],0,1);
		$this->Cell(70);
		$this->Cell(80,4,'Correo: '.$GLOBALS['CENTRO_CORREO'],0,1);
		$this->Ln(8);
	}
}

			# creamos el nuevo objeto partiendo de la clase
			$MiPDF=new GranPDF('P','mm',A4);
$MiPDF->AddFont('NewsGotT','','NewsGotT.php');
$MiPDF->AddFont('NewsGotT','B','NewsGotTb.php');
$MiPDF->AddFont('ErasDemiBT','','ErasDemiBT.php');
$MiPDF->AddFont('ErasDemiBT','B','ErasDemiBT.php');
$MiPDF->AddFont('ErasMDBT','','ErasMDBT.php');
$MiPDF->AddFont('ErasMDBT','I','ErasMDBT.php');
	$MiPDF->SetMargins(20,20,20);
	$MiPDF->SetDisplayMode('fullpage');

// Alumnos que deben reponer libros
$repo1 = "select distinct textos_alumnos.claveal from textos_alumnos, FALUMNOS where FALUMNOS.claveal = textos_alumnos.claveal and nivel = '$niv' and (estado = 'M' or estado = 'N') and devuelto = '1' order by nivel, grupo";
$repo0 = mysql_query($repo1);
while ($repo = mysql_fetch_array($repo0)) {
	$claveal = $repo[0];
// Datos del alumno	
	$sqlal="SELECT concat(Nombre,' ',Apellidos),Unidad,Domicilio,Localidad,codpostal,Tutor FROM alma, FTUTORES WHERE alma.nivel = FTUTORES.nivel and alma.grupo = FTUTORES.grupo and claveal='".$claveal."'";
	$resultadoal = mysql_query($sqlal);
	$registroal = mysql_fetch_row($resultadoal);
	$nivel = substr($registroal[1],0,2);

// Libros en mal estado o perdidos
$sqlasig="SELECT distinct asignaturas.nombre, textos_gratis.titulo, textos_gratis.editorial, importe from textos_alumnos, textos_gratis, asignaturas where textos_alumnos.claveal='$claveal' and asignaturas.codigo = textos_alumnos.materia and textos_gratis.materia=asignaturas.nombre and (estado = 'M' or estado = 'N')  and textos_gratis.nivel='$nivel'";
$resulasig=mysql_query($sqlasig);
#recogida de variables.
$hoy=formatea_fecha(date('Y-m-d'));
$alumno=$registroal[0];
$unidad=$registroal[1];
$domicilio=$registroal[2];
$localidad=$registroal[3];
$codigo=$registroal[4];
$tutor="Tutor/a: ".$registroal[5];
$director_del_centro='Francisco Medina Infante';
$jefatura_de_estudios='Francisco Javier M�rquez Garcia';
$secretario_del_centro='Mar�a Lourdes Barrutia Navarrete';
$direccion_del_centro='Direcci�n del Centro';
$fecha = date('d/m/Y');
$texto2=" Se debe reponer o en su caso abonar el importe indicado ";

$titulo2="NOTIFICACI�N DE REPOSICI�N DE LIBROS DE TEXTO";
$cuerpo21="D. $secretario_del_centro, como Secretario del centro $nombre_del_centro, y con el visto bueno de la Direccci�n, ";
$cuerpo22="CERTIFICA que el/la alumno/a: $alumno matriculado/a en el curso $unidad, revisados sus libros con fecha $fecha, debe ";
$cuerpo22.="reponer (o en su caso abonar el importe segun tarifa marcada por la Junta de Andaluc�a) los siguientes libros: ";
$importante2='En caso de no atender a este requerimiento el/la alumno/a no podr� disfrutar del programa de gratuidad el curso pr�ximo.'; 

# insertamos la primera p�gina del documento
$MiPDF->Addpage();
#### Cabecera con dirección
$MiPDF->SetFont('Times','',11);
$MiPDF->SetTextColor(0,0,0);
$MiPDF->Text(96,55,$tutor);
$MiPDF->Text(120,60,$domicilio);
$MiPDF->Text(120,65,$codigo." (".$localidad.")");

	$total=0;
	$MiPDF->Ln(60);
	$MiPDF->SetFont('Times','B',12);
	$MiPDF->Multicell(0,4,$titulo2,0,'C',0);
	$MiPDF->SetFont('Times','',11);
	$MiPDF->Ln(4);
	$MiPDF->Multicell(0,4,$cuerpo21,0,'J',0);
	$MiPDF->Ln(3);
	$MiPDF->Multicell(0,4,$cuerpo22,0,'J',0);
	$MiPDF->Ln(3);
	$MiPDF->SetFont('Times','I',10);
	$MiPDF->Ln(2);
	while($regasig=mysql_fetch_row($resulasig)){
		$MiPDF->SetFont('Times','I',10);
		$MiPDF->SetX(170);
		$MiPDF->cell(0,4,$regasig[3].' Euros',0,'D',0);
	$MiPDF->SetX(20);
	$MiPDF->Multicell(150,4,'- '.$regasig[0].' --> T�tulo: '.$regasig[1].' ('.$regasig[2].')',0,'I',0);
	
	$total=$total+$regasig[3];
	}#del while
	mysql_query("update textos_alumnos set devuelto = '1', fecha = now() where claveal = '$claveal'");		
		$MiPDF->SetFont('Times','BI',10);
		$MiPDF->SetX(158);
	$MiPDF->Multicell(0,4,' Total: '.$total.' Euros',0,'D',0);
		$MiPDF->SetFont('Times','',11);
	$MiPDF->Ln(5);
	$MiPDF->Multicell(0,4,'En '.$localidad_del_centro.', a '.$hoy,0,'C',0);
	$MiPDF->Ln(5);
	$MiPDF->Multicell(0,4,'Secretario/a:                        Sello del Centro                         Director/a:',0,'C',0);
	$MiPDF->Ln(14);
	$MiPDF->Multicell(0,4,$secretario_del_centro.'                                             '.$director_del_centro,0,'C',0);
	$MiPDF->Ln(4);
	$MiPDF->Multicell(0,4,$importante2,0,'J',0);
	
}

$MiPDF->Output();
			
?>

