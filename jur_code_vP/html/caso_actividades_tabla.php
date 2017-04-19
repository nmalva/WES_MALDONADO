<?php include ("includes/title.php");?>
<?php include ("includes/security_session.php");?>
<!DOCTYPE html>
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
<!--  BEGIN INCLUDE CLASSES -->
<?php include_once("../classes/class.casos.php");?>
<?php include_once("../classes/class.actividades.php");?>
<?php include_once("../classes/class.usuarios.php");?>
<?php include_once("../classes/class.utiles.php");?>
<?php include_once("../classes/class.bd.php");?>
<!--  END INCLUDE CLASSES -->

<!--  BEGIN GLOBAL VARIABLES -->
<?php 
$session_usu_id= $_SESSION["usu_id"];
$get_cas_id=$_GET["cas_id"];
?>
<!--  END GLOBAL VARIABLES -->

<!--  BEGIN PHP FUNCTIONS -->
<?php 
function getCasoInfo($cas_id){
    $class_bd=new bd();
    $sql="SELECT * FROM Casos 
          INNER JOIN Clientes on Casos.cli_id=Clientes.cli_id
          WHERE cas_id={$cas_id}";
    
    $resultado=$class_bd->ejecutar($sql);
    $r=$class_bd->retornar_fila($resultado);   
	$string.= " <i style='font-size:17px;' class='icon-user'>  Cliente: ". $r["cli_nombre"]." ".$r["cli_apellido"]."</i>";
	$string.=" <i style='font-size:10px;'> - ".$r["cas_caratula"]." - </i>";
	echo $string;	
}

function get_datos_usu_id($usu_id){
    $class_usuarios=new Usuarios($usu_id);
    $usu_apellido=$class_usuarios->getUsu_apellido();
    return ($usu_apellido);
}

function escribir_actividades($get_cas_id){
    $class_bd=new bd();
    $class_utiles= new utiles();
    $sql="SELECT * FROM Actividades 
            INNER JOIN TipoActividad on Actividades.tia_id = TipoActividad.tia_id
            INNER JOIN Usuarios      on Actividades.usu_idemisor = Usuarios.usu_id
          WHERE cas_id={$get_cas_id}";
    
    $resultado=$class_bd->ejecutar($sql);
    
    while ($r=$class_bd->retornar_fila($resultado))
    {
            if ($r["act_estado"]==0){
            $estado="Abierto";
            $color="";
            }
            if ($r["act_estado"]==1){
                $estado="Verificar";
                $color="#58b8a9";
            }
            if ($r["act_estado"]==2){
                $estado="Reveer";
                $color="#f36a5a";
            }
            if ($r["act_estado"]==3){
               $estado="Cerrado";
                $color="#292829";
            }
        $usu_apellido=get_datos_usu_id($r["usu_idresponsable"]);
        
 
        $fecha_modificacion=($r["act_fechacierre"]==NULL ? "" : $class_utiles->fecha_mysql_php_datetime($r["act_fechacierre"]));
        
        $table="<tr href='caso_actividades_modal.php?act_id={$r["act_id"]}&cas_id={$get_cas_id}' data-target='#ajax' data-toggle='modal' style='cursor:pointer'>";
        $table.="<td style='color:{$color};'>{$r["act_id"]}</td>";
        $table.="<td style='color:{$color};'>{$r["tia_nombre"]} {$r["cli_apellido"]}</td>";
        $table.="<td style='color:{$color};'>{$r["usu_apellido"]}</td>";
        $table.="<td style='color:{$color};'>{$usu_apellido}</td>";
        $table.="<td style='color:{$color};'>{$class_utiles->CortarTexto(stripslashes($r["act_comentario"]), 0,40)}...</td>";
        $table.="<td style='color:{$color};'>{$class_utiles->fecha_mysql_php_datetime($r["act_fecha"])}</td>";
        $table.="<td style='color:{$color};'>{$fecha_modificacion}</td>";
        $table.="<td style='color:{$color};'>{$estado}</td>";
        $table.="<td style='display:none;'>{$r["act_fecha"]}</td>";
        $table.="</tr>";

        echo $table;
        
    }
}

function escribir_archivos_caso($get_cas_id){
    $class_bd=new bd();
    $class_utiles= new utiles();
    $sql="SELECT * FROM Archivos 
    		WHERE cas_id={$get_cas_id}";

    $resultado=$class_bd->ejecutar($sql);
    
    while ($r=$class_bd->retornar_fila($resultado))
    {
           
        $fecha= $class_utiles->fecha_mysql_php_datetime($r["arc_timestamp"]);
        $ruta='../../files/'.$r["arc_ruta"]."".$r{"arc_nombre"};
        
        $table="<tr href='{$ruta}' style='cursor:pointer'>";
        $table.="<td style='color:{$color};'>{$r["act_id"]}</td>";
        $table.="<td style='color:{$color};'><a href='{$ruta}' target='_blank'>{$r["arc_nombre"]}</a></td>";
        $table.="<td style='color:{$color};'>{$r["arc_peso"]} KB</td>";
        $table.="<td style='color:{$color};'>{$fecha}</td>";
        $table.="<td onclick='eliminar_archivo({$r["arc_id"]});' style='cursor:pointer; style='text'><i style='font-size:15px;' class='fa fa-times'></td>";
        $table.="</tr>";

        echo $table;
        
    }
 }

 function escribir_archivos_actividad(){
 
 	$act_id=$_SESSION["act_id"];
    $class_bd=new bd();
    $class_utiles= new utiles();
    $sql="SELECT * FROM Archivos 
    		WHERE act_id={$act_id}";

    $resultado=$class_bd->ejecutar($sql);
    
    while ($r=$class_bd->retornar_fila($resultado))
    {
           
        $fecha= $class_utiles->fecha_mysql_php_datetime($r["arc_timestamp"]);
        $ruta='../../files/'.$r["arc_ruta"]."".$r{"arc_nombre"};
        
        $table="<tr href='{$ruta}' style='cursor:pointer'>";
        $table.="<td style='color:{$color};'>{$r["act_id"]}</td>";
        $table.="<td style='color:{$color};'><a href='{$ruta}' target='_blank'>{$r["arc_nombre"]}</a></td>";
        $table.="<td style='color:{$color};'>{$r["arc_peso"]} KB</td>";
        $table.="<td style='color:{$color};'>{$fecha}</td>";
        $table.="<td style='color:{$color};' onclick='eliminar_archivo({$r["arc_id"]});'>{$_SESSION["act_id"]}</td>";
        $table.="</tr>";

        echo $table;
        
    }




 }
?>
<head>

<!--  PAGE TITLE  -->
<?php include ("includes/pagetitle.php");?>
<!--  END PAGE TITLE  -->

<!-- BEGIN GLOBAL MANDATORY STYLES -->
<?php include("includes/globalstyle.html");?>
<!-- END GLOBAL MANDATORY STYLES -->

<!-- BEGIN PAGE LEVEL STYLES USED BY TABLE-->
<link rel="stylesheet" type="text/css"
	href="../../assets/global/plugins/select2/select2.css" />
<link rel="stylesheet" type="text/css"
	href="../../assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css" />
<link rel="stylesheet" type="text/css"
	href="../../assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css" />
<link rel="stylesheet" type="text/css"
	href="../../assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN THEME STYLES -->
<?php include ("includes/themestyle.html")?>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico" />
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-menu-fixed" class to set the mega menu fixed  -->
<!-- DOC: Apply "page-header-top-fixed" class to set the top menu fixed  -->

<body>
	<!-- BEGIN HEADER -->
	<div class="page-header">
		<!-- BEGIN HEADER TOP -->
	    <?php include("includes/headertop.php");?>
	<!-- END HEADER TOP -->
		<!-- BEGIN HEADER MENU -->
	   <?php include("includes/headermenu.php");?>
	<!-- END HEADER MENU -->
	</div>
	<!-- END HEADER -->
	<!-- BEGIN PAGE CONTAINER -->
	<div class="page-container">
		<!-- BEGIN PAGE HEAD -->
		<div class="page-head">
			<div class="container">
				<!-- BEGIN PAGE TITLE -->
				<div class="page-title">
					<h1>
                        <?php getCasoInfo($get_cas_id);?>
					</h1>
				</div>
				<!-- END PAGE TITLE -->
				<!-- BEGIN PAGE TOOLBAR -->
				<div class="page-toolbar">
					<!-- BEGIN THEME PANEL -->
					<div class="btn-group btn-theme-panel">
						
					</div>
					<!-- END THEME PANEL -->
				</div>
				<!-- END PAGE TOOLBAR -->
			</div>
		</div>
		<!-- END PAGE HEAD -->
		<!-- BEGIN PAGE CONTENT -->
		<div class="page-content">
			<div class="container">
				<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
				<div class="modal fade" id="portlet-config" tabindex="-1"
					role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"
									aria-hidden="true"></button>
								<h4 class="modal-title">Modal title</h4>
							</div>
							<div class="modal-body">Widget settings form goes here</div>
							<div class="modal-footer">
								<button type="button" class="btn blue">Save changes</button>
								<button type="button" class="btn default" data-dismiss="modal">Close</button>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
				<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
				<!-- BEGIN PAGE BREADCRUMB -->
        			<ul class="page-breadcrumb breadcrumb">
        				<li>
        					<a href="caso_tabla.php">Retornar</a><i class="fa fa-circle"></i>
        				</li>
        				<li>
        					<a href='caso_actividades_modal.php?cas_id=<?php echo $get_cas_id;?>' data-target='#ajax' data-toggle='modal'>Nueva Actividad</a>
        					
        				</li>
        				
        			</ul>
			<!-- END PAGE BREADCRUMB -->
				<!-- BEGIN PAGE CONTENT INNER -->
				<div class="row">
					<div class="col-md-12">
						<!-- BEGIN EXAMPLE TABLE PORTLET-->
						<div class="portlet light">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-cogs font-green-sharp"></i> <span
										class="caption-subject font-green-sharp bold uppercase"> TABLA DE ACTIVIDADES DEL CASO ID - <?php echo  $get_cas_id;?></span>
								</div>
								<div class="tools"></div>
							</div>
							<div class="portlet-body">
								<table class="table table-striped table-bordered table-hover"
									id="sample_3">
									<thead>
										<tr>
											<th>Id Act</th>
											<th>Tipo Actividad</th>
											<th>Emisor</th>
											<th>Responsable</th>
											<th>Comentario</th>
											<th>Fecha Limite</th>
											<th>Ultima Modificación</th>
											<th>Estado</th>
											<th style="display:none;">Fecha Orden</th>
										</tr>
									</thead>
									<tbody>
            							<?php 
            							    escribir_actividades($get_cas_id);	
            							?>
        							</tbody>
								</table>
							</div>
						</div>
						<!-- END EXAMPLE TABLE PORTLET-->
					</div>
				</div>
				<!-- END PAGE CONTENT INNER -->
			</div>
		</div>
		<!-- END PAGE CONTENT -->		
	</div>
	<!-- END PAGE CONTAINER -->

	<a class="btn default" data-toggle="modal" href="#basic_archivos">
									Todos los Archivos </a>


	<!-- BEGIN MODAL -->
	<div class="modal fade" id="ajax" role="basic" aria-hidden="true">
		<div class="page-loading page-loading-boxed">
			<img src="../../assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
			<span>
			&nbsp;&nbsp;Loading... </span>
		</div>
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
			</div>
		</div>
	</div>
	<!-- END MODAL -->
	


	<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Carga de Archivos</h4>
				</div>
				<div class="modal-body">

						<fieldset class="left">
					       <!-- <legend>Seleccione los Archivos a Subir</legend>-->
					        <p>Seleccione un archivo y presione el botón Subir </p>
					        <form name="form5" enctype="multipart/form-data" method="post" action="upload.php" />
					            <p><input type="file" size="32" name="my_field" value="" id="xhr_field" /></p>
					            <div id="xhr_status"></div>
					            <p class="button"><input type="hidden" name="action" value="xhr" />
					            <input type="submit" name="Submit" value="Subir" id="xhr_upload"/></p>
					        </form>
					        <div id="xhr_result"></div>
				    	</fieldset>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn default" data-dismiss="modal">Close</button>
					
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

		<div class="modal fade" id="basic_archivos" tabindex="-1" role="basic" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Archivos adjuntos al caso</h4>
				</div>
				<div class="modal-body">
						
						<table class="table table-striped table-bordered table-hover"
									id="sample_3">
									<thead>
										<tr>
											<th>Id Actividad</th>
											<th>Nombre</th>
											<th>Tamaño</th>
											<th>Fecha</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
            							<?php 
            							    escribir_archivos_caso($get_cas_id); 	
            							?>
        							</tbody>
								</table>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn default" data-dismiss="modal">Close</button>
					
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>





	<!-- BEGIN PRE-FOOTER -->
<?php include("includes/prefooter.html")?>
<!-- END PRE-FOOTER -->
<!-- BEGIN FOOTER -->
<?php include("includes/footer.html");?>
<!-- END FOOTER -->

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<?php include("includes/coreplugins.html");?>
<!-- END CORE PLUGINS -->


<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript"
	src="../../assets/global/plugins/select2/select2.min.js"></script>
<script type="text/javascript"
	src="../../assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript"
	src="../../assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript"
	src="../../assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript"
	src="../../assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript"
	src="../../assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript"
	src="../../assets/global/plugins/moment.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
	
	

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../assets/global/scripts/metronic.js"
	type="text/javascript"></script>
<script src="../../assets/admin/layout3/scripts/layout.js"
	type="text/javascript"></script>
<script src="../../assets/admin/layout3/scripts/demo.js"
	type="text/javascript"></script>
<script src="../../assets/admin/pages/scripts/table-advanced.js"></script>


<script>
jQuery(document).ready(function() {       
   Metronic.init(); // init metronic core components
   Layout.init(); // init current layout    
   Demo.init(); // init demo features
   TableAdvanced.init();
});

//--START JAVASCRIPT FUNCTIONS--
function redireccionar_caso_formulario(cas_id){
	pagina = "caso_formulario.php?cas_id="+ cas_id;
	setTimeout(redireccionar, 100, pagina);	
}
function redireccionar(pagina) {
	{
	location.href=pagina;
	}             
}
//--END JAVASCRIPT FUNCTIONS--
</script>


 <script type="text/javascript">

    window.onload = function () {

      function xhr_send(f, e, doc_type) {
        if (f) {
          xhr.onreadystatechange = function(){
            if(xhr.readyState == 4){
              document.getElementById(e).innerHTML = xhr.responseText;
            }
          }
          xhr.open("POST", "../classes/class.upload_v32/upload.php?action=xhr&act_id=<?php echo $_SESSION['act_id'];?>&doc_type=" + doc_type);

          xhr.setRequestHeader("Cache-Control", "no-cache");
          xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
          xhr.setRequestHeader("X-File-Name", f.name);
          xhr.send(f);
        }
      }

      function xhr_parse(f, e) {
        if (f) {
          document.getElementById(e).innerHTML = "File selected : " + f.name + "(" + f.type + ", " + f.size + ")";
        } else {
          document.getElementById(e).innerHTML = "No file selected!";
        }
      }

      function dnd_hover(e) {
        e.stopPropagation();
        e.preventDefault();
        e.target.className = (e.type == "dragover" ? "hover" : "");  
      }

      var xhr = new XMLHttpRequest();

      if (xhr && window.File && window.FileList) {

        // xhr example
        var xhr_file = null;
        document.getElementById("xhr_field").onchange = function () {
          xhr_file = this.files[0];
          xhr_parse(xhr_file, "xhr_status");
        }
        document.getElementById("xhr_upload").onclick = function (e) {
          e.preventDefault();
          xhr_send(xhr_file, "xhr_result");
        }

        // drag and drop example
        var dnd_file = null; 
        document.getElementById("dnd_drag").style.display = "block";
        document.getElementById("dnd_field").style.display = "none";
        document.getElementById("dnd_drag").ondragover = function (e) {
          dnd_hover(e);
        }
        document.getElementById("dnd_drag").ondragleave = function (e) {
          dnd_hover(e);
        }
        document.getElementById("dnd_drag").ondrop = function (e) {
          dnd_hover(e);
          var files = e.target.files || e.dataTransfer.files;
          dnd_file = files[0];
          xhr_parse(dnd_file, "dnd_status");
        }
        document.getElementById("dnd_field").onchange = function (e) {
          dnd_file = this.files[0];
          xhr_parse(dnd_file, "dnd_status");
        }
        document.getElementById("dnd_upload").onclick = function (e) {
          e.preventDefault();
          xhr_send(dnd_file, "dnd_result");
        }

      }
    }
    </script>


<script type="text/javascript">
function eliminar_archivo(arc_id){
        mensaje =   "Seguro desea borrar el documento?";
        confirmar=confirm(mensaje); 
        if (confirmar) {
        //alert(arc_id);
            //showLoadingNewupdate();
            $.ajax({
                type:"POST",
                url: "../abm/abm.archivos.php",
                data:{arc_id:arc_id}, 
                cache: false,
                success : function (msg) {
                    alert (msg);
                   // hideLoadingNewupdate();
                    //new_can_id = parseInt(msg);
                    //if (new_can_id==-10)
                    //   alert ("Error: There is already a Candidate registered width the same DNI in a recentrly closed exam or in an open Exam");
                    //else{
                       alert ("El archivo fue eliminado")
                     //  redirect(new_can_id);
                    }    
                
            });  
        }   
        
    }   
</script>

</body>
<!-- END BODY -->
</html>