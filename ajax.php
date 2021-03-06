<?php
include('include/comun.php');
session_start();
function ajax($accion){
    switch($accion){
        case 'prog_alerta':
            $a=$_REQUEST['accion'];
            $id=$_REQUEST['id'];
            switch ($a) {
                case '1':
                    alerta_mail();
                    break;
                default:
                    programacion::edit('F',$id);
                    $row_array['mensaje']='ok';
                    break;
            }
            
            
            echo json_encode($row_array);
            break;
        case 'cot_pre_trash':
            $ids=$_REQUEST['ids'];
            $sql="update cotizacion_pre set bestado='0' where cotizacion_pre_id in(".$ids.")";
            mysql_query($sql);
            $row_array['success']='ok';
            echo json_encode($row_array);
            break;
        case 'drop_registro':
            $estado=$_REQUEST['estado'];
            $tbl_id=$_REQUEST['tbl_id'];
            $ids=$_REQUEST['ids'];
            $tbl=tabla::edit('S',$tbl_id);
            if($estado=='1'){
                $sql="update ".$tbl['tbl_nombre']." set bestado='1' where ".$tbl['tbl_col_pk']." in(".$ids.")";
            }else{
                $sql="update ".$tbl['tbl_nombre']." set bestado='0' where ".$tbl['tbl_col_pk']." in(".$ids.")";
            }            
            mysql_query($sql);
            $row_array['success']='ok';
            echo json_encode($row_array);
            break;
        case 'activar_cot':
            $tbl_id=$_REQUEST['tbl_id'];
            $id=$_REQUEST['id'];
            $param=tabla::tbl_edit_tbl($tbl_id,0,'P');
            $sql="select tec_cotizacion_pre('A','condicion',0,0,".(int)$id.",".$param.")";
            mysql_query($sql);
            $row_array['sucess']='ok';
            echo json_encode($row_array);
            break;
        case 'cot_estado':
            $ano=$_REQUEST['ano_id'];
            $mes=$_REQUEST['mes_id'];
            echo cotizacion_estado($ano,$mes);
            break;
        case 'doc_fin_resumen':
            $tipo=$_REQUEST['tipo'];
            $moneda_id=$_REQUEST['moneda_id'];
            $accion=$_REQUEST['accion'];
            switch($accion){
                case 'D': // Doc. financieros
                    echo doc_financiero_lista($tipo,$moneda_id);
                    break;
                case 'L': // letras
                    echo letra_det_lista($tipo,$moneda_id);
                    break;
                case 'T': // Filtra tablero segun moneda
                    echo tab_doc_financiero($moneda_id);
                    break;
            }
            
            break;
        case 'evento':
            $accion=$_REQUEST['accion'];
            $fecha=$_REQUEST['fecha'];            
            switch($accion){
                case 'F': // Fecha
                    echo evento_lista($fecha);
                    break;
                case 'E': // eventos
                    $sql="select DATE_FORMAT(evento_fecha, '%Y-%c-%e')as fecha from eventos where bestado='1'
                          union
                          select DATE_FORMAT(rec_cal_fecha, '%Y-%c-%e')as fecha from recurso_calendario where bestado='1'";
                    $result=mysql_query($sql);
                    $array=array();
                    while($row=mysql_fetch_array($result,1)){
                        $array[]=$row['fecha'];
                    }
                    echo json_encode($array);
                    break;
            }
                       
            break;
        case 'recurso':
            $accion=$_REQUEST['accion'];
            $fecha=$_REQUEST['fecha'];
            switch($accion){
                case 'F': // Fecha
                    echo recurso_lista($fecha);
                    break;                
            }
                       
            break;
        case 'search_recurso':
            $return_arr = array();

            $valor=$_REQUEST['term'];
            $tipo=$_REQUEST['tipo'];
            $sql="SELECT recurso_id,recurso_nombre FROM v_recurso WHERE bestado='1' and recurso_tipo_id=".(int)$tipo." and recurso_nombre LIKE '%$valor%' limit 20";
            $result = mysql_query($sql);
            while ($row = mysql_fetch_assoc($result)){
                //echo $row['empresa_id']."#".$row['emp_nombre']."#<br />\n ";
                $row_array['id'] = $row['recurso_id'];
                $row_array['value'] = $row['recurso_nombre'];                
                array_push($return_arr,$row_array);
            }
            mysql_free_result($result);
            echo json_encode($return_arr);
            break;
        case 'recurso_ddl':
            $tipo=$_REQUEST['tipo'];
            echo recurso_ddl($tipo,0);
            break;
        case 'filtrar_lista':
            $tabla_id=$_REQUEST['tabla_id'];
            $reg=tabla_col::edit('F',$tabla_id);
            $operador_id=$_SESSION['SIS'][2];
            foreach ($reg as $col) {
                switch($col['col_control']){
                    case 'MES':
                        $valor=$_REQUEST['mes_'.$col['tabla_col_nombre']];
                        break;
                    case 'AXO':
                        $valor=$_REQUEST['axo_'.$col['tabla_col_nombre']];
                        break;
                    default:
                        $valor=$_REQUEST[$col['tabla_col_nombre']];
                        break;
                }
                
                $sql="select tec_tabla_col_filtro('I','0','".$tabla_id."','".$col['tabla_col_id']."','".$col['tabla_col_nombre']."','".$valor."','".$operador_id."')";
                $result=mysql_query($sql);
            }
            $row_array['mensaje']='ok';
            echo json_encode($row_array);
            break;
        case 'letra_amort':
            $accion=$_REQUEST['accion'];
            $letra_detalle_id=$_REQUEST['reg_id'];
            $fecha=$_REQUEST['fecha'];
            $monto=$_REQUEST['monto'];
            $tipo=$_REQUEST['acc_tomar'];
            $nro=$_REQUEST['nro'];
            $fecha_venc=$_REQUEST['fecha_venc'];
            switch($accion){
                case 'F':
                    echo letra_amortizacion($letra_detalle_id);
                    break;
                case 'I':
                    if($tipo=="1"){ // generar amortizacion
                        $sql="select tec_letra_det_amortizacion('I','0','0','0','0','".$letra_detalle_id."','".$fecha."','".$monto."')";
                        $result=mysql_query($sql);
                        $row_array['mensaje']="ok";
                    }else{ // Generar nueva letra
                        $sql="select tec_letra_detalle('N','','0','0','".$letra_detalle_id."','0','".$nro."','".$fecha."','".$fecha_venc."','".$monto."','','','','','')";
                        $result=mysql_query($sql);
                        $row_array['mensaje']='ok';
                        
                    }
                                        
                    echo json_encode($row_array);
                    break;
            }
            
            break;
        case 'cp_detalle':
            $accion=$_GET['accion'];
            switch ($accion){
                case 'I':
                    cp_comp_det::edit(0,'I');                    
                    break;
                case 'D':
                    cp_comp_det::edit($_GET['id'],'D');                    
                    break;               
            }
            cp_det_lista($_REQUEST['cp_id']);
            break;
        case 'convert_mon': // Convertir monedas
            $moneda_ini=$_REQUEST['moneda_id_ini'];
            $moneda_fin=$_REQUEST['moneda_id_fin'];
            //$moneda=array(1=>"PEN",2=>"USD",3=>"EUR");
            $cantidad_ini=$_REQUEST['cantidad_ini'];
            $cantidad=conversor_divisas($moneda_ini,$moneda_fin, $cantidad_ini,'V');
            $cambio=conversor_divisas($moneda_ini,$moneda_fin, 0,'C');
            $soles=conversor_divisas($moneda_fin,1, 0,'C');
            $row_array['moneda_valor']=$cantidad;
            $row_array['moneda_cambio']=$cambio;
            $row_array['moneda_soles']=$soles;
            echo json_encode($row_array);
            break;
        case 'cp_compras':
            $accion=$_REQUEST['accion'];
            switch($accion){
                case 'I':
                    break;
                case 'U':
                    break;
                case 'L':
                    echo cp_compras_lista('','');
                    break;
                case 'D':
                    break;
            }
            break;
        case 'grabar_lista':
            $tabla_id=(int)$_GET['tabla_id'];
            $tabla=tabla::edit("S",$tabla_id);
            $sql="";
            if(is_array($tabla)){
                $sql="select*from ".$tabla['tbl_nombre']." where bestado=1 ";
                if($reg=tabla::tabla_col_lista($tabla_id)){
                    foreach($reg as &$col){
                        if($col['tbl_col_filtro']=='1'){
                            $sql.=" and ".$col['tabla_col_nombre']."=".$_REQUEST[$col['tabla_col_nombre']];
                        }
                    }
                    $result=mysql_query($sql);
                    while($row = mysql_fetch_array($result)){
                        $sql="update ".$tabla['tbl_nombre']." set ";
                        foreach($reg as &$col){
                            if($col['tbl_col_lst_editable']=='1'){
                                $valor=$col['tabla_col_nombre']."_".$row[$tabla['tbl_col_pk']];
                                //$valor=$col['tabla_col_nombre'];
                                $sql.=$col['tabla_col_nombre']."='".$_REQUEST[$valor]."' where ".$tabla['tbl_col_pk']."=".$row[$tabla['tbl_col_pk']];
                                mysql_query($sql);
                            }
                        }
                    }
                }
            }
            $return_arr=array();
            $row_array['mensaje'] ="Datos grabados correctamente.";
            //array_push($return_arr,$row_array);
            echo json_encode($row_array);
            break;
        case 'emp_perfil':
            $id=explode(',',$_REQUEST['ids']);
            $perfil=$_REQUEST['emp_perfil_id'];
            for($c=0;$c<count($id);$c++){
                $sql="select tec_anfi_empresa('I','',0,'".$_SESSION['SIS'][5]."','".$id[$c]."','".$perfil."')";
                mysql_query($sql);
            }
            $row_array['mensaje'] ="Se agrego al perfil seleccionado.";            
            echo json_encode($row_array);
            break;
        case 'destino':
            $destino_id=$_REQUEST['id'];
            switch($destino_id){
                case 1: // Cotizacion
                    echo cotizacion_ddl();
                    break;
                case 2: // Proforma Importacion
                    echo proforma_ddl();
                    break;
            }
            break;
        case 'compras':
            // 69 = oc
            $param=tabla::tbl_edit_tbl(69,0,'P');
            
            $sql="select tec_oc ('IMP','condicion',11,1,".(int)$_REQUEST['oc_id'].",".$param.")";
            mysql_query($sql);
            $row_array['result'] = "&nbsp;Datos grabados correctamente. <b>".$_REQUEST['oc_id']."</b>";
            echo json_encode($row_array);
            break;
        case 'generar_oc': // [compras] - [compras_detalle]
            
            $ids=explode(",",$_REQUEST['ids']);
            $compras_id=(int)$_REQUEST['compras_id'];
            $comp_nro=$_REQUEST['comp_nro'];
            $proveedor_id=0;
            if($compras_id>0){
                $sql="select proveedor_id from compras where compras_id=".$compras_id;
                $result=mysql_query($sql);
                $row=mysql_fetch_array($result);
                $proveedor_id=$row['proveedor_id'];
            }
            else{
                // Selecciono el 1ero
                $sql="select*,
                     (select proveedor_id mbre from v_proveedor where proveedor_id=(select proveedor_id from mm where mm_id=(select marca_id from producto where producto_id=compras_detalle.producto_id)))as proveedor_id
                     from compras_detalle where comp_detalle_id=".$ids[0];
                $result=mysql_query($sql);
                $row=mysql_fetch_array($result);
                $proveedor_id=$row['proveedor_id'];
                
                $sql="insert into compras(comp_nro,proveedor_id,comp_fecha_ini)values('".$comp_nro."',".$proveedor_id.",NOW())";
                mysql_query($sql);
                $sql="select LAST_INSERT_ID()as compras_id";
                $result=mysql_query($sql);
                $row=mysql_fetch_array($result);
                $compras_id=$row['compras_id'];
            }
                                  
            $sql="select*,
                  (select proveedor_id mbre from v_proveedor where proveedor_id=(select proveedor_id from mm where mm_id=(select marca_id from producto where producto_id=compras_detalle.producto_id)))as proveedor_id
                  from compras_detalle where comp_detalle_id in(".$_REQUEST['ids'].")";
            
            $result=mysql_query($sql);
            while ($row1 = mysql_fetch_array($result)){
                if($proveedor_id==$row1['proveedor_id']){
                    mysql_query("update compras_detalle set compras_id=".(int)$compras_id." where comp_detalle_id=".$row1['comp_detalle_id']);
                }
            }                                                
            $sql="update compras set proveedor_id=".(int)$proveedor_id." where compras_id=".$compras_id;
            $result=mysql_query($sql);
            $row_array['result'] = $compras_id;
            echo json_encode($row_array);
            break;
        case 'search_oc':
            $return_arr = array();

            $valor=$_REQUEST['term'];
            $sql="SELECT compras_id,comp_nro FROM compras WHERE bestado='1' and comp_nro LIKE '%$valor%' limit 20";
            $result = mysql_query($sql);
            while ($row = mysql_fetch_assoc($result)){
                //echo $row['empresa_id']."#".$row['emp_nombre']."#<br />\n ";
                $row_array['id'] = $row['compras_id'];
                $row_array['value'] = $row['comp_nro'];
                $row_array['nro'] = $row['comp_nro'];
                array_push($return_arr,$row_array);
            }
            mysql_free_result($result);
            echo json_encode($return_arr);
            break;
        case 'search_prov':
            $return_arr = array();
            $valor=$_REQUEST['term'];
            $sql="SELECT proveedor_id,emp_nombre,concat(emp_ruc,' ',emp_nombre)as proveedor FROM v_proveedor WHERE bestado='1' and emp_nombre LIKE '%$valor%' or emp_ruc LIKE '%$valor%' limit 20";
            $result = mysql_query($sql);
            while ($row = mysql_fetch_assoc($result)){                
                $row_array['id'] = $row['proveedor_id'];
                $row_array['value'] = $row['proveedor'];
                $row_array['proveedor'] = $row['emp_nombre'];
                array_push($return_arr,$row_array);
            }
            mysql_free_result($result);
            echo json_encode($return_arr);
            break;
        case 'search_cli':
            $return_arr = array();
            $valor=$_REQUEST['term'];
            $sql="SELECT * FROM v_cliente WHERE bestado='1' and emp_nombre LIKE '%$valor%' or emp_ruc LIKE '%$valor%' limit 20";
            $result = mysql_query($sql);
            while ($row = mysql_fetch_assoc($result)){                
                $row_array['id'] = $row['cliente_id'];
                $row_array['value'] = $row['emp_nombre'];
                $row_array['ruc'] = $row['emp_ruc'];
                array_push($return_arr,$row_array);
            }
            mysql_free_result($result);
            echo json_encode($return_arr);
            break;
        case 'search_prod':
            $return_arr = array();
            $valor=$_REQUEST['term'];
            $sql="SELECT *FROM producto WHERE bestado='1' and prod_nombre LIKE '%$valor%' limit 20";
            $result = mysql_query($sql);
            while ($row = mysql_fetch_assoc($result)){                
                $row_array['id'] = $row['producto_id'];
                $row_array['value'] = $row['prod_nombre'];
                $row_array['precio'] = $row['prod_precio_venta'];
                array_push($return_arr,$row_array);
            }
            mysql_free_result($result);
            echo json_encode($return_arr);
            break;
        case 'cot_mail':
            //$mail_para=explode(',',$_REQUEST['mail_para']);
				$mail_mul=array();            
            $mail_para=explode(',',$_POST['mail_para']);
            for($i=0;$i<count($mail_para);$i++) 
            {
					$mail_mul[$i]['mul']=$mail_para[$i];            	
            }
            $mail_asunto=$_REQUEST['mail_asunto'];
            $mail_mensaje=$_REQUEST['mail_mensaje'];
            $mail_adjunto=$_REQUEST['mail_adjunto'];
            //foreach ($mail_para as $para) {
                //if(send_mail_coti('andy@solucionestecperu.com',$para,$mail_asunto,$mail_mensaje,$mail_adjunto,'','')){
                $resEnv=send_mail_coti('electrowerkeserver@gmail.com',$mail_mul,$mail_asunto,$mail_mensaje,$mail_adjunto,'','');
                if($resEnv==true)
                {
                    $row_array['result'] = "Se envio correctamente.";
                }
            	else
            	{
                    $row_array['result'] = $_POST['mail_para'].",".$mail_asunto.",".$mail_mensaje.",".$mail_adjunto."No se pudo enviar.";
                } 
            //}
            
            echo json_encode($row_array);
            break;
        case 'acceso_directo':
            $id=$_REQUEST['id'];
            $accion=$_REQUEST['accion'];
            switch($accion){
                case 'I':
                    atajos::edit('I',$id);
                    $row_array['mensaje'] = "Se ha creado el acceso directo.";
                    echo json_encode($row_array);
                    break;
                case 'D':
                    atajos::edit('D',$id);
                    echo atajos_lista($_SESSION['SIS'][2]);
                    break;
            }            
            
            break;
        case 'cambiar_version':
            $tbl_id=$_REQUEST['tbl_id'];
            $id=$_REQUEST['id'];
            $tabla=tabla::edit('S',$tbl_id);
            $pk=$tabla['tbl_col_pk'];
            
            $param=tabla::tbl_edit_tbl($tbl_id,0,'P');
            $sql="select ".$tabla['tbl_sp']."('V','','".$_SESSION['SIS'][2]."','".$_SESSION['SIS'][5]."','".$id."',".$param.")";
            $result=mysql_query($sql);
            $row=mysql_fetch_array($result);
            $return=explode("|", $row[0]);
            $id=$return[0]; // Id
            $row_array['id'] =$id;
            $row_array['pk'] =$pk;
            echo json_encode($row_array);
            break;
        case 'adjudicado':
            $accion=$_REQUEST['accion'];
            $tbl_id=$_REQUEST['tbl_id'];
            $id=$_REQUEST['sw']; // Si/No = 1/0
            $condicion=$_REQUEST['ids'];
            $tabla=tabla::edit('S',$tbl_id);            
            $param=tabla::tbl_edit_tbl($tbl_id,0,'P');
            $sql="select ".$tabla['tbl_sp']."('".$accion."','".$condicion."','".$_SESSION['SIS'][2]."','".$_SESSION['SIS'][5]."','".$id."',".$param.")";
            $result=mysql_query($sql);
            $row=mysql_fetch_array($result);
            $row_array['id'] =$row[0];
            echo json_encode($row_array);                        
            break;
        case 'enviar_correo_pdf':
            $accion=$_REQUEST['accion'];
            $tipo=$_REQUEST['tipo'];
            $id=$_REQUEST['id'];
            $mail_para=$_REQUEST['mail_para'];
            $mail_asunto=$_REQUEST['mail_asunto'];
            $mail_mensaje=$_REQUEST['mail_mensaje'];
            $mail_adjunto=$_REQUEST['mail_adjunto'];
            switch($accion){
                case 'F': // Mostrar formulario
                    echo EnviarCorreo($tipo,$id);
                    break;
                case 'S': // Send mail
                    if(send_mail('Sistema',$mail_para,$mail_asunto,$mail_mensaje,$mail_adjunto,'','')){
                        $row_array['result'] = "Se envio correctamente.";
                    }else{
                        $row_array['error'] = "No se pudo enviar.";
                    }
                    echo json_encode($row_array);
                    break;
            }
            break;
        case 'reasignar':
            $accion=$_REQUEST['accion'];
            $tbl_id=$_REQUEST['tbl_id'];
            $operador_id_desde=$_REQUEST['operador_id_desde'];
            $operador_id_hacia=$_REQUEST['operador_id_hacia'];
            $tipo=$_REQUEST['tipo']; // Tipo de asignacion (Seleccionados/Todos)
            $ids=$_REQUEST['ids']; // lista de ids (1,2,3,..)
            $id=explode(',',$ids);
            switch($accion){
                case 'F': // Mostrar formulario
                    echo Reasignar($tbl_id,$id[0]);
                    break;
                case 'S': // Ejecutar SP                    
                    $sql="CALL sp_reasignar('".$tbl_id."','".$_SESSION['SIS'][2]."','".$_SESSION['SIS'][5]."','".$tipo."','".$ids."','".$operador_id_desde."','".$operador_id_hacia."')";
                    mysql_query($sql);
                    $row_array['mensaje']="La operaciÃ³n se completo correctamente.";
                    echo json_encode($row_array);
                    break;
            }
            break;
        case 'prof_version':
            $id=$_REQUEST['imp_proforma_id'];
            $sql="select tec_imp_proforma('V','','','','".$id."','','','','','','','','')";
            $result=  mysql_query($sql);
            $row=mysql_fetch_array($result);
            $row_array['imp_proforma_id']=$row[0];
            echo json_encode($row_array);
            break;
        case 'prof_cot':
            $id=$_REQUEST['id'];
            $accion=$_REQUEST['accion'];
            switch($accion){
                case 'FRM': // muestra el formulario para generar cotizacion
                    echo proforma_cotizacion($id);
                    break;
                case 'G': // Genera la cotizacion
                    $sql="select tec_imp_proforma('C','','','','".$id."','','','','','','','','','','','','')";
                    $result=mysql_query($sql);
                    $row=mysql_fetch_array($result);
                    $row_array['imp_proforma_id']=$row[0];
                    echo json_encode($row_array);
                    break;
            }
            
            break;
        case 'imp_prof_gasto':
            $id=$_REQUEST['id'];
            $imp_prof_det_id=$_REQUEST['imp_prof_det_id'];
            $accion=$_REQUEST['accion'];
            switch($accion){
                case 'FRM':
                    echo imp_gasto_form($id);
                    break;
                case 'I':
                case 'U':                   
                case 'D':
                    imp_proforma::imp_gasto($accion,$id);
                    $reg=imp_proforma::imp_gasto('C',$imp_prof_det_id);
                    $row_array['total']=$reg;                    
                    echo json_encode($row_array);
                    break;
                case 'L':
                    echo imp_gasto_lista($imp_prof_det_id);
                    break;
            }
            break;
        case 'genera_cot':
            //header("Cache-Control: no-store, no-cache, must-revalidate");
            $empresa_id=(int)$_SESSION['SIS'][5];
            $orientacion=($empresa_id=2)?'P':'L';
            $id=$_REQUEST['id'];
            $valor=CotizacionSalida($id,$empresa_id);
            $pdf=ExportarPDF2(1,$valor['html'],'cotizacion/',$valor['codigo'].".pdf",$orientacion);
            $row_array['mensaje']=$pdf.'?var='.rand();
            echo json_encode($row_array);
            break;
        case 'cp_pdf':            
            $empresa_id=(int)$_SESSION['SIS'][5];
            $orientacion=($empresa_id=2)?'P':'L';
            $id=$_REQUEST['id'];            
            $pdf=ComprobantePDF($id,1);
            $row_array['pdf']=$pdf.'?var='.rand();
            echo json_encode($row_array);
            break;
        case 'genera_prof':
            $empresa_id=(int)$_SESSION['SIS'][5];
            $orientacion='P';
            $id=$_REQUEST['id'];
            $tipo=$_REQUEST['tipo'];
            switch($tipo)
            {
                case 'C': // Cotizacion

                    if($_SESSION['SIS'][5]==1)
                    {
                        $orientacion='P';
                    }
                    else if($_SESSION['SIS'][5]==2)
                    {
                        $orientacion='L';
                    }
                    else
                    {
                       $orientacion='P';
                    }

                    $valor=CotizacionSalida($id,$_SESSION['SIS'][5]);
                    $ruta='cotizaciones';

                break;
                
                case 'P': // Proforma

                    if($_SESSION['SIS'][5]==1)
                    {
                        $orientacion='P';
                    }
                    else if($_SESSION['SIS'][5]==2)
                    {
                        $orientacion='L';
                    }
                    else
                    {
                        $orientacion='P';
                    }

                    $valor=ProformaSalida($id,$_SESSION['SIS'][5]);
                    $ruta='proformas';

                break;

                case 'OC': // Orden Compra Internacional

                    if($_SESSION['SIS'][5]==1)
                    {
                        $orientacion='P';
                    }
                    else if($_SESSION['SIS'][5]==2)
                    {
                        $orientacion='L';
                    }
                    else
                    {
                       $orientacion='P';
                    }
                    
                    $valor=OCSalida($id,$_SESSION['SIS'][5]);
                    $ruta='compras';
                
                break;
                
                case 'OCL': // Orden Compra Cliente

                    $valor=OCLSalida($id,$_SESSION['SIS'][5]);
                    $ruta='oc';
                
                break;
            }

            $pdf=ExportarPDF2(1,$valor['html'],$ruta.'/',$valor['codigo'].".pdf",$orientacion);
            $row_array['mensaje']=$pdf;
            echo json_encode($row_array);
            break;

        case 'select':
            $reg=tabla_col::edit('S',$_GET['col_id']);
            
            $valor=$_GET['valor'];
            if($reg['fuente_tbl']>''){
                $objeto=explode("|",$reg['fuente_tbl']);
                // 0=tabla , 
                // 1=id a mostrar , 
                // 2= nombre o valor a mostrar , 
                // 3=condicion ,
                // 4=campo para aplicar condicion
                // 5=orden
                $dependencia=($objeto[4]>'')?$objeto[4]:$reg['tbl_col_dependencia'];
                if($objeto[3]>'' && $objeto[3]<>'null'){ // condicion
                    $objeto[3]=  str_replace('SIS5', $_SESSION['SIS'][5],$objeto[3]); // empresa_id
                    $where.=" and ".$objeto[3];
                }
                if(is_array($objeto)){
                    
                    switch($reg['col_control']){
                        case 'LST':
                            //tabla|id|valor
                            $sql="select ".$objeto[1]." as id,".$objeto[2]." as valor 
                                  from ".$objeto[0]." where bestado='1' and ".$dependencia."=".(int)$valor." ".$where." order by valor";
                            $result=mysql_query($sql);
                            echo "<option value=''></option>";
                            while($row=mysql_fetch_array($result)){
                                echo "<option value='".$row['id']."'>".$row['valor']."</option>";
                            }
                            break;
                        case 'TXT':
                        case 'TXA':
                            //tabla|campo_codicion|campo_valor
                            $sql="select * from ".$objeto[0]." where bestado='1' and ".$objeto[1]."=".(int)$valor;
                            $result=mysql_query($sql);
                            $row=mysql_fetch_array($result);
                            echo $row[$objeto[2]];
                            break;
                    }
                    
                    
                }
            }
            break;
        case 'gen_prof': // Genera proforma desde Cotizacion
            $ids=$_REQUEST['ids'];
            //$ids=str_replace(',','|',$ids);
            $tbl_id=$_REQUEST['tbl_id'];
            $param=tabla::tbl_edit_tbl($tbl_id,0,'P');
            
            $sql="select tec_cot_detalle('P','".$ids."','".$_SESSION['SIS'][2]."','".$_SESSION['SIS'][5]."','0',".$param.");";
            $result=mysql_query($sql);
            $row=mysql_fetch_array($result);
            $row_array['imp_proforma_id']=$row[0];
            $row_array['sql']=$sql;
            echo json_encode($row_array);
            break;
        case 'search_emp':
            /*$valor=$_REQUEST['q'];
            $sql="SELECT * FROM empresa WHERE bestado='1' and emp_nombre LIKE '%".$valor."%' or emp_ruc LIKE '%".$valor."%'";
            $result = mysql_query($sql); 
            while ($row = mysql_fetch_assoc($result)){
                echo $row['empresa_id']."#".$row['emp_ruc']."#".$row['emp_nombre']."#".$row['emp_nombre']."\n ";
            }
            mysql_free_result($result);*/
            
            $return_arr = array();

            $valor=$_REQUEST['term'];
            $sql="SELECT * FROM empresa WHERE bestado='1' and emp_nombre LIKE '%$valor%' or emp_ruc LIKE '%$valor%' limit 20";
            $result = mysql_query($sql);
            while ($row = mysql_fetch_assoc($result)){
                //echo $row['empresa_id']."#".$row['emp_nombre']."#<br />\n ";
                $row_array['id'] = $row['empresa_id'];
                $row_array['value'] = $row['emp_nombre'];
                $row_array['ruc'] = $row['emp_ruc'];
                $row_array['nombre'] = $row['emp_nombre'];
                $row_array['direccion'] = $row['emp_direccion'];
                array_push($return_arr,$row_array);

            }
            mysql_free_result($result);
            echo json_encode($return_arr);
            
            break;
        case 'search_form':
            // Control "FRM" 
            //$fuente=tabla|id|valor|campo1,campo2|rotulo1,rotulo2
            $fuente=$_GET['fuente'];
            $objeto=explode("|",$fuente);
            $campos=explode(",",$objeto[3]);           
            $return_arr = array();
                        
            $valor=$_REQUEST['term'];
            $where=array();
            for($c=0;$c<count($campos);$c++){
                $where[]=" ".$campos[$c]." LIKE '%".$valor."%' ";
            }
                                               
            $sql="SELECT * FROM ".$objeto[0]." WHERE bestado='1' and ".implode("or",$where)." limit 20";
            $result = mysql_query($sql);            
            while ($row = mysql_fetch_assoc($result)){                
                $row_array['id'] = $row[$objeto[1]];
                $row_array['value'] = $row[$objeto[2]];
                for($c=0;$c<count($campos);$c++){
                    $row_array[$campos[$c]] = $row[$campos[$c]];
                }                                
                array_push($return_arr,$row_array);                               
            }
            mysql_free_result($result);
            echo json_encode($return_arr);
            
            break;        
        case 'auto_jq':
            $return_arr = array();

            $valor=$_REQUEST['term'];
            $sql="SELECT * FROM empresa WHERE emp_nombre LIKE '%$valor%'";
            $result = mysql_query($sql); 
            while ($row = mysql_fetch_assoc($result)){
                //echo $row['empresa_id']."#".$row['emp_nombre']."#<br />\n ";            
                $row_array['id'] = $row['empresa_id'];
                $row_array['value'] = $row['emp_direccion'];
                $row_array['value2'] = $row['emp_direccion'];
                array_push($return_arr,$row_array);

            }
            mysql_free_result($result);
            echo json_encode($return_arr);
            break;
        case 'tipo_cambio':
            $moneda_id=($_REQUEST['moneda_id']>0)?$_REQUEST['moneda_id']:2;
            if($moneda_id>0){
                $moneda_tc=producto::moneda_tc();
                switch($moneda_id){
                    case 2: // Soles
                        $valor_moneda=$moneda_tc['m_us'];
                        break;
                    case 3: // Dolares
                        $valor_moneda=$moneda_tc['m_e'];
                        break;
                    default: // Euros
                        $valor_moneda=2.7;
                        break;
                }
                $row_array['tc'] = $valor_moneda;                        
            }
            echo json_encode($row_array);
            break;
        case 'auto':
            $return_arr = array();

            $valor=$_REQUEST['term'];
            $objeto=$_REQUEST['fuente'];
            $array=tabla::tbl_autocomplete('L',$valor,0,$objeto);            
            foreach ($array as &$row) {
                $row_array['id'] = $row['id'];
                $row_array['value'] = $row['valor']; 
                array_push($return_arr,$row_array);
            }                                    
            echo json_encode($return_arr);
//            $valor=$_REQUEST['q'];
//            $objeto=$_REQUEST['fuente'];
//            $array=tabla::tbl_autocomplete('L',$valor,0,$objeto);            
//            foreach ($array as &$value) {                            
//                echo $value['id']."#".$value['valor']."#<br />\n ";            
//            }
                        

            break;
        case 'prod_subclasif':
            echo prod_subclasif_ddl($_GET['id']);
            break;
        case 'prod_categoria':
            echo prod_cat_ddl($_GET['id']);
            break;
        case 'prod_propiedad':
            echo prod_clasifpropiedad_ddl($_GET['id']);
            break;
        case 'tbl_lista':
            $accion=$_GET['accion'];
            $tbl_id=$_GET['tabla_id'];
            $id=$_GET['id'];
            if($_REQUEST['accion']=='D'){
                tabla::tbl_edit_tbl($tbl_id,$id,'D');
            }
            
            echo tbl_lista($tbl_id);
            #echo print "Jano...!";
            break;
        case 'local_list':
            local_lista($_GET['empresa_id']);
            break;
        case 'contacto_list':
            contacto_lista($_GET['empresa_id']);
            break;
	case 'tbl_col_lista':
            $orden=$_REQUEST['orden'];
            if($_GET['accion']=='D'){                
                tabla_col::edit('D',$_GET['col_id']);
            }
            tbl_col_lista($_GET['tabla_id'],$orden);
            break;
	case 'tbl_grupo_lista':
            if($_GET['accion']=='D'){
                tabla_grupo::edit('D',$_GET['tbl_grupo_id']);
            }
            tbl_grupo_lista($_GET['tabla_id']);
            break;
        case 'tbl_accion_lista':
            if($_GET['accion']=='D'){
                tabla_accion::edit('D',$_GET['tbl_accion_id']);
            }
            tbl_accion_lista($_GET['tabla_id']);
            break;
        case 'show_image':
            echo "<img src='images/add.png'/>";
            break;
        case 'tbl_buscar':
            echo tbl_busqueda($_GET['id']);
            break;
        case 'lst_busqueda_producto':
           
            $query1='';
            $query2='';
            for($i=1;$i<=8;$i++){
                $campo=$_REQUEST['campo'.$i];
                $valor=$_REQUEST['valor'.$i];
                $cond=$_REQUEST['cond'.$i];
                $ope=$_REQUEST['ope'.$i];
                if($valor>'' && $campo>''){
                    switch($ope){
                        case '%_%':
                            $where=" like '%".$valor."%' ";break;
                        case '%_':
                            $where=" like '%".$valor."' ";break;
                        case '_%':
                            $where=" like '".$valor."%' ";break;
                        case '=':
                            $where="='".$valor."'";break;
                        case '!=':
                            $where="!='".$valor."' ";break;
                        case '>':
                            $where=">'".$valor."' ";break;
                        case '<':
                            $where="<'".$valor."' ";break;
                    }
                    
                    if($i<=5){
                        $query1.=" ".$cond." ".$campo.$where;
                    }else{
                        $query2.=" ".$cond." (prod_clasifpropiedad_id=".$campo." and prod_propiedad_valor".$where.")";
                        
                    }                                                            
                }
            }
            if($query2>''){
                $zz=$query2;
                $sql="select producto_id from prod_propiedad where bestado='1' ".$query2;
                $result=mysql_query($sql);
                $array=array();
                while($row=mysql_fetch_array($result)){
                    $array[]=$row['producto_id'];
                }
                mysql_free_result($result);
                if(count($array)>0){
                    $query2=" and producto_id in(".implode(',',$array).")";
                }else{
                    $query2="";
                }
            }
            
            $row_array['mensaje'] = $query1.$query2;
            
            echo json_encode($row_array);
            break;  
        case 'lst_busqueda':
            $id=$_REQUEST['tabla_id'];            
            $reg=tabla::tabla_col_lista_alfa($id);
            $query='';
            foreach($reg as &$r){
                $campo=$r['tabla_col_nombre'];
                $valor=$_POST[$campo];
                $cond=$_REQUEST['cond_'.$campo];
                $ope=$_REQUEST['ope_'.$campo];
                if($valor>''){
                    switch($ope){
                        case '%_%':
                            $where=" like '%".$valor."%' ";break;
                        case '%_':
                            $where=" like '%".$valor."' ";break;
                        case '_%':
                            $where=" like '".$valor."%' ";break;
                        case '=':
                            $where="='".$valor."'";break;
                        case '!=':
                            $where="!='".$valor."' ";break;
                        case '>':
                            $where=">'".$valor."' ";break;
                        case '<':
                            $where="<'".$valor."' ";break;
                    }
                    if($r['tbl_col_externo']=='1'){
                        $sql=$r['lst_formula']." where bestado='1' ".$cond." ".$campo.$where;
                        $result=mysql_query($sql);
                        $campo_pk=mysql_field_name($result,0);
                        $array=array();
                        while($row=mysql_fetch_array($result)){
                            $array[]=$row[$campo_pk];
                        }
                        if(count($array)>0){
                            $query.=" and ".$campo_pk." in(".implode(',',$array).")";
                        }
                        //$query.=$sql;
                    }else{
                        $query.=" ".$cond." ".$campo.$where;
                    }
                }
            }
            
            $row_array['mensaje'] = $query;
            
            echo json_encode($row_array);
            break;        
        case 'image':
            $uploaddir = './uploads/';
            $file = $uploaddir . basename($_FILES['uploadfile']['name']);
            if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)){
                echo "success";   
            }else{
                echo "error";   
            }  
            break;
        case 'upload_tardanza':
            $json=array();
            $info=pathinfo($_FILES['file']['name']);
            $ano=$_REQUEST['ano'];
            $mes=$_REQUEST['mes'];
            $nombre="data_".$ano.$mes.".".$info['extension'];            
                if (move_uploaded_file($_FILES['file']['tmp_name'],'data/'.$nombre)) {
                    $fp = fopen ( "data/".$nombre , "r" ); 
                    while (( $data = fgetcsv ( $fp , 1000 , "," )) !== FALSE ) { // Mientras hay líneas que leer...
                        $i = 0; 
                        //foreach($data as $row) {

                            //$data[0]= codigo
                            //$data[5]= total
                            if(is_numeric($data[0])){
                                $sql="select trab_tardanza('I','".$data[0]."','".$ano."','".$mes."','".$data[5]."')";
                                mysql_query($sql);
                                //$str.=$sql."<br>";
                            }

                        
                        //}

                    } 
                    fclose ( $fp );
                    $json['success']="ok";
                } else {
                    $json['error']="Error, no se pudo cargar el archivo.";
                }            
            echo json_encode($json);
            break;
        case 'upload':
            // defino la carpeta para subir
            
            $json=array();
            
            
            
            $tabla_id=$_REQUEST['tabla_id'];
            $reg_id=$_REQUEST['reg_id'];
            
            if(!is_dir("archivos/".$tabla_id)){
                mkdir("archivos/".$tabla_id);
            }            
            if(!is_dir("archivos/".$tabla_id."/".$reg_id)){
                mkdir("archivos/".$tabla_id."/".$reg_id);
            }
                        
            $ruta="archivos/".$tabla_id."/".$reg_id;
            
            if(!is_dir){
                $json['error']="Error, no existe el directorio '".$ruta."'";
            }else{
            
            //$id=$_GET['id'];
            $tipo=$_REQUEST['tipo'];
            $nombre=$_REQUEST['nombre'];
            $descrip=$_REQUEST['descrip'];
            $size=$_FILES['file']['size'];
            $info=pathinfo($_FILES['file']['name']);
            
            if($nombre>''){
                $nombre=$nombre.'.'.$info['extension'];
            }else{
                $nombre=$info['filename'].'.'.$info['extension'];
            }
            
            switch($tipo){
                case 1:
                    $mensaje="Error: Solo se permiten imagenes.";
                    $formato='/^(jpg|png|jpeg|gif)$/';
                    break;
                case 2:
                    $mensaje="Error: Solo se permiten documentos.";
                    $formato='/^(doc|xls|pdf|ppt)$/';
                    break;
            }
            
            if (!preg_match($formato, strtolower($info['extension']))){
                $json['error']=$mensaje;
                echo json_encode($json);
                exit();
            }
            
                
                                    
                $uploaddir=$ruta;

                if(!is_dir($uploaddir)){
                    mkdir($uploaddir);
                }
                                                
                // defino el nombre del archivo
                $uploadfile = $uploaddir.'/'.$nombre;
                
                // Inserto el registro "Archivo"
                $sql="insert into archivo(arc_nombre,arc_descripcion,arc_ruta,arc_fecha,arc_tipo_id,arc_medida,arc_extension,tabla_id,tabla_reg_id)
                    values('".$nombre."','".$descrip."','".$uploadfile."',NOW(),".(int)$tipo.",".(int)$size.",'".$info['extension']."',".(int)$tabla_id.",".(int)$reg_id.")";
                $result=mysql_query($sql);
                //$sql="select LAST_INSERT_ID()";
                //$result=mysql_query($sql);
                //$row=mysql_fetch_row($result);
                //return $row[0];
                
                // Lo mueve a la carpeta elegida
                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
                    $json['success']="El archivo ha sido cargado correctamente.";
                } else {
                    $json['error']="Error, no se pudo cargar el archivo.";
                }
            }
            echo json_encode($json);
            break;
        case 'subir_archivo':
            $ruta=$_POST['ruta'];
            $operador_id=$_POST['operador_id'];
            $file=($_POST['control']>'')?'file_'.$_POST['control']:'file';          
            if(!is_dir($ruta)){
                mkdir($ruta);
            } 
            
            $size=$_FILES['file']['size'];
            $info=pathinfo($_FILES['file']['name']);
            
            $name=date("y").date("m").date("d").date("H").date("i")."_".$operador_id;
            
            //$nombre=$info['filename'].'.'.$info['extension'];
            $nombre=$name.'.'.$info['extension'];
            
            $uploadfile = $ruta.'/'.$nombre;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
                $json['success']="El archivo ha sido cargado correctamente.";
                $json['file']=$uploadfile;
            }else{
                $json['error']="Error, no se pudo cargar el archivo.".$uploadfile;
            }
            
            echo json_encode($json);
            
            break;
        case 'archivo_lista':
            switch ($_GET['accion']){
                case 'D':
                    archivo::edit('D',$_GET['archivo_id']);
                    break;
            }
           
            archivo_lista($_REQUEST['tabla_id'],$_REQUEST['tabla_reg_id']);
            break;
        case 'ubigeo':
            echo ubigeo_ddl($_GET['tipo'],$_GET['codigo']);
            break;
        case 'mant_lista':
            echo tbl_lista($_GET['tabla_id']);
            break;
        case 'file':
            $dir=opendir($_GET['dir']);
            //echo $dir;
            //echo '<table border="1">';
            echo '<ol id="selectable">';
            while ($file = readdir($dir)) {
                if($file == "." || $file == ".." || $file == "index.php" )	
                    continue;
                    $archivo=$_GET['dir']."/".$file;
                    //echo "tam. ".size($archivo);
                    //$info=pathinfo($archivo);
                    //if(filetype($archivo)!='dir'){
                        //echo '<tr><td align="center">'.$file.'</td><td>'.filesize($archivo).'</td></tr>';
                        //echo '<div style="margin:5;float:left;padding:3px;height:60px;border:solid 1px gray">'.$file.'</div>';
                        echo '<li class="ui-default" onclick="select(this)" ondblclick="getValor(\''.$archivo.'\');">'.$file.'</li>';
                    //}
                                                    
            }
            closedir($dir);
            //echo '</table>';
            echo '</ol>';
            break;
		case 'estadisticas::create':
			echo estadisticas::create($_GET['accion']);
		break;		
		default:
			$key=array_shift($_GET);
			echo call_user_func_array($key,$_GET);
		break;		
    }
}

ajax($_REQUEST['a']);

?>
