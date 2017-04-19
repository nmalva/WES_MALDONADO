<?php
session_start();
ob_start();
include_once('../classes/class.bd.php');


	//-----------------Campos---------------
	

	$campos["arc_id"]=$_POST["arc_id"];
	delete_archivo($campos["arc_id"]);
	delete_registro($campos["arc_id"]);
	

	//---------------Functions--------------
	

	//-----------------Insert---------------
	function insert(){
	
	}
	//-----------------Update---------------
	function update(){
		
			
	}
	
    function delete_registro($arc_id){
    	$class_bd= new bd();
    	$sql = "DELETE FROM `Archivos` WHERE `arc_id`= {$arc_id}";
    	$class_bd->ejecutar($sql);

    	//echo $sql;

    }
    
    function delete_archivo($arc_id){
    	$class_bd= new bd();
    	$sql="SELECT * FROM Archivos WHERE arc_id={$arc_id}";
    	$resultado=$class_bd->ejecutar($sql);
    	
    	echo $sql;
    	$r=$class_bd->retornar_fila($resultado);
    	$ruta=$r["arc_ruta"];
		$nombre = $r["arc_nombre"];

		echo $ruta;

		$ruta_completa="../../files/".$ruta.$nombre;

		unlink($ruta_completa);

		//echo $ruta_completa;
		
		

    }
    function insert_compartir_casos($new_id, $session_usu_id){
        }

?>