<?php
if (isset($_GET['month'])) { $month = $_GET['month']; $month = preg_replace ("/[[:space:]]/", "", $month); $month = preg_replace ("/[[:punct:]]/", "", $month); $month = preg_replace ("/[[:alpha:]]/", "", $month); }
if (isset($_GET['year'])) { $year = $_GET['year']; $year = preg_replace ("/[[:space:]]/", "", $year); $year = preg_replace ("/[[:punct:]]/", "", $year); $year = preg_replace ("/[[:alpha:]]/", "", $year); if ($year < 1990) { $year = 1990; } if ($year > 2035) { $year = 2035; } }
if (isset($_GET['today'])) { $today = $_GET['today']; $today = preg_replace ("/[[:space:]]/", "", $today); $today = preg_replace ("/[[:punct:]]/", "", $today); $today = preg_replace ("/[[:alpha:]]/", "", $today); }

$month = (isset($month)) ? $month : date("n",time());
$year = (isset($year)) ? $year : date("Y",time());
$today = (isset($today))? $today : date("j", time());
$daylong = date("l",mktime(1,1,1,$month,$today,$year));
$monthlong = date("F",mktime(1,1,1,$month,$today,$year));
$dayone = date("w",mktime(1,1,1,$month,1,$year))-1;
$numdays = date("t",mktime(1,1,1,$month,1,$year));
$alldays = array('Lun','Mar','Mie','Jue','Vie','S�b','Dom');
$next_year = $year + 1;
$last_year = $year - 1;
include("nombres.php");
 
if ($today > $numdays) { $today--; }

// Estructura de la Tabla
echo "<table class='table table-bordered table-striped' style=''><tr><th style='text-align:center'>
	<a href='".$_SERVER['PHP_SELF']."?year=$last_year&today=$today&month=$month&profesor=$profesor&unidad=$unidad&alumno=$alumno'>
<i class='fa fa-arrow-circle-left fa-2x pull-left' name='calb2' style='margin-right:20px;'> </i> </a>
<h3 style='display:inline'>$year</h3>
<a href='".$_SERVER['PHP_SELF']."?year=$next_year&today=$today&month=$month&profesor=$profesor&unidad=$unidad&alumno=$alumno'>
<i class='fa fa-arrow-circle-right fa-2x pull-right' name='calb1' style='margin-left:20px;'> </i> </a></th></tr></table>";
echo "<table class='table table-bordered' style=''>
      <tr>";
$meses = array("1"=>"Ene" ,"2"=>"Feb" ,"3"=>"Mar" ,"4"=>"Abr" ,"5"=>"May" ,"6"=>"Jun" ,"7"=>"Jul" ,"8"=>"Ago" ,"9"=>"Sep" ,"10"=>"Oct" ,"11"=>"Nov" ,"12"=>"Dic");
foreach ($meses as $num_mes => $nombre_mes) {

	if ($num_mes==$month) {
		echo "<th style='background-color:#08c'>
		<a href=\"".$_SERVER['PHP_SELF']."?profesor=$profesor&unidad=$unidad&alumno=$alumno&year=$year&month=".$num_mes."\" style='color:#efefef'>".$nombre_mes."</a> </th>";
	}
	else{
		echo "<th>
		<a href=\"".$_SERVER['PHP_SELF']."?profesor=$profesor&unidad=$unidad&alumno=$alumno&year=$year&month=".$num_mes."\">".$nombre_mes."</a> </th>";
	}
	if ($num_mes=='6') {
		echo "</tr><tr>";
	}
}
echo "</tr>
    </table>";
 
//Nombre del Mes
echo "<table class='table table-bordered' style=''><tr>";
echo "<td colspan=\"7\" valign=\"middle\" align=\"center\"><h4 align='center'>" . $monthlong .
"</h4></td>";
echo "</tr><tr>";


//Nombre de D�as
foreach($alldays as $value) {
	echo "<th  style='background-color:#eee'>
	$value</th>";
}
echo "</tr><tr>";


//D�as vac�os
if ($dayone < 0) $dayone = 6;
for ($i = 0; $i < $dayone; $i++) {
	echo "<td>&nbsp;</td>";
}


//D�as

for ($zz = 1; $zz <= $numdays; $zz++) {

	if ($i >= 7) {  print("</tr>\n<tr>\n"); $i=0; }

	if ($result_found != 1) {

		//Buscar falta en el d�a y marcarla

		$sql_currentday = "$year-$month-$zz";
		// echo $sql_currentday;
		$eventQuery = "SELECT FALTA FROM FALTAS, FALUMNOS WHERE FALUMNOS.CLAVEAL = FALTAS.CLAVEAL and FALTAS.FECHA = '$sql_currentday' and FALTAS.claveal = '$alumno' and FALTA not like 'R'";
		//echo $eventQuery;
		$eventExec = mysqli_query($db_con, $eventQuery);
		if($row = mysqli_fetch_array($eventExec)) {
			if (strlen($row[0]) > 0) {
				if ($row[0] == "F") {
						
					echo "<td style=\"background-color:#9d261d\"><a href=\"".$_SERVER['PHP_SELF']."?profesor=$profesor&unidad=$unidad&alumno=$alumno&year=$year&today=$zz&month=$month&F=1\" class=\"normal\"><span style=color:white>$zz</a></span></td>\n";
					$result_found = 1;
				}
				elseif($row[0] == "J") {
					//        echo "<td valign=\"middle\" align=\"center\" style=\"background-color:#009933\"><span style=color:white>$zz</span></td>\n";
					echo "<td style=\"background-color:#46a546\"><a href=\"".$_SERVER['PHP_SELF']."?falta=J&profesor=$profesor&unidad=$unidad&alumno=$alumno&year=$year&today=$zz&month=$month&F=1\"><span style=color:white>$zz</a></span></td>\n";
					$result_found = 1;
				}
			}
		}
	}

	if ($result_found != 1) {
		$timestamp0 = strtotime($sql_currentday);
		$timestamp1 = strtotime($fin_curso);
		$timestamp2= strtotime($inicio_curso);
		
		
		$dia_festivo="";
		$repe=mysqli_query($db_con, "select fecha from festivos where date(fecha) = date('$sql_currentday')");
		if (mysqli_num_rows($repe) > '0') {
			$dia_festivo='1';
		}

		if($dia_festivo=='1')
		{
			echo "<td><span class='text-warning'>$zz</span></td>";
		}
		
		elseif($timestamp0 > $timestamp1){
			echo "<td><span class='text-warning'>$zz</span></td>";
		} 
		
		elseif($timestamp0 < $timestamp2){
			echo "<td><span class='text-warning'>$zz</span></td>";
		} 
		
		elseif (($i== "5") or ($i== "6")){
			echo "<td><span class='text-warning'>$zz</span></td>";
		}
		else{
			echo "<td>";
			//echo "<a href=\"".$_SERVER['PHP_SELF']."?profesor=$profesor&unidad=$unidad&alumno=$alumno&year=$year&today=$zz&month=$month\" class=\"normal\">$zz</a>";
			?>
<!-- Button trigger modal -->
<a href="#" data-toggle="modal" data-target="#myModal<? echo "_".$zz;?>">
			<? echo $zz;
			?> </a>

<!-- Modal -->
<div class="modal fade" id="myModal<? echo "_".$zz;?>" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span
	aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
<h4 class="modal-title text-info" id="myModalLabel<? echo "_".$zz;?>">Selecciona
las Horas de la Ausencia.</h4>
</div>
<div class="modal-body">
<a onClick="seleccionar_todo<? echo "_".$zz;?>()"
	class="btn btn-success" style="display: inline">Marcar todas</a>
<form action="index.php" method="POST"
	name="marcar_falta<? echo "_".$zz;?>" style="display: inline">&nbsp;&nbsp;
<div class="checkbox" style="display: inline; align: center;"><label
	class="checkbox-inline"> <input type="checkbox"
	name="1<? echo "_".$zz;?>" value="1">1�</label> <label
	class="checkbox-inline"> <input type="checkbox"
	name="2<? echo "_".$zz;?>" value="2">2�</label> <label
	class="checkbox-inline"> <input type="checkbox"
	name="3<? echo "_".$zz;?>" value="3">3�</label> <label
	class="checkbox-inline"> <input type="checkbox"
	name="4<? echo "_".$zz;?>" value="4">4�</label> <label
	class="checkbox-inline"> <input type="checkbox"
	name="5<? echo "_".$zz;?>" value="5">5�</label> <label
	class="checkbox-inline"> <input type="checkbox"
	name="6<? echo "_".$zz;?>" value="6">6�</label></div>

</div>
<div class="modal-footer"><input type="hidden" name="profesor"
	value="<? echo $profesor;?>"> <input type="hidden" name="unidad"
	value="<? echo $unidad;?>"> <input type="hidden" name="alumno"
	value="<? echo $alumno;?>"> <input type="hidden" name="year"
	value="<? echo $year;?>"> <input type="hidden" name="month"
	value="<? echo $month;?>"> <input type="hidden" name="today"
	value="<? echo $zz;?>"> <input type="hidden" name="F" value="1"> <input
	type="submit" class="btn btn-danger" name="Enviar" value="Registrar">
<button class="btn btn-default" data-dismiss="modal">Cerrar</button>
</form>
</div>
</div>
</div>
</div>
<script>
function seleccionar_todo<? echo "_".$zz;?>(){
	for (i=0;i<document.marcar_falta<? echo "_".$zz;?>.elements.length;i++)
		if(document.marcar_falta<? echo "_".$zz;?>.elements[i].type == "checkbox")	
			document.marcar_falta<? echo "_".$zz;?>.elements[i].checked=1
}
</script>
			<?
			echo "</td>";
		}



	}
	$i++; $result_found = 0;
}

$create_emptys = 7 - (($dayone + $numdays) % 7);
if ($create_emptys == 7) { $create_emptys = 0; }

if ($create_emptys != 0) {
	echo "<td colspan=\"$create_emptys\">&nbsp;</td>";
}
echo "</tr>
      </table>";
?>
