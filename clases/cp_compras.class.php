<?php
class cp_compras{
//******************************************************************************
    function cp_lista($accion='',$tipo=0,$limit=''){
        //Limite de consulta
        $limit=($limit>'')?" LIMIT ".$limit:"";
        // Venta/Compra
    	//$where=($tipo>0)?" and cp_accion_id=".$tipo:"";
        //Fecha Emision
        //$where.=($_REQUEST['desde']>'')?" and cp_fec_emis between '".$_REQUEST['desde']."' and '".$_REQUEST['hasta']."' ":"";
        //Tipo cp
        $where.=($_REQUEST['cp_tipo_id']>0)?" and cp_tipo_id=".$_REQUEST['cp_tipo_id']:"";
        //Mes cp
        //$where.=(($_REQUEST['mes_id']>0)?" and MONTH(cp_fec_emis)=".$_REQUEST['mes_id']:"");
        //$where.=" and MONTH(cp_fec_emis)=".(($_REQUEST['mes_id']>0)?$_REQUEST['mes_id']:(int)date('m'));
        //Por Cliente
        //$where.=((int)$_REQUEST['cliente_id']>0)?" and cliente_id=".(int)$_REQUEST['cliente_id']:"";
        //Filtro Ajax
        switch($_REQUEST['filtro']){
            case '1':
                $where.=" and cp_nro like'".$_REQUEST['valor']."%'".$fecha;
                break;
            case '2':
                $where.=" and cp_tipo_id=".($_REQUEST['valor']+0).$fecha;
                break;
            case '3':
                $where.=" and centro_costo_id=".($_REQUEST['valor']+0).$fecha;
                break;
            case '4':                     
                $where.=" and cliente_id=".$_REQUEST['valor'].$fecha;
                break;
        }
       
            
        switch($accion){
            case 'L':
                $sql = 'select*,
                (select emp_nombre from v_cliente where cliente_id=cp.cliente_id limit 1)as cli_nombre,
                (select cp_estado_nombre from cp_estado where cp_estado_id=cp.cp_estado_id)as cp_estado_nombre,
                (select cp_tipo_nombre from cp_tipo where cp_tipo_id=cp.cp_tipo_id)as cp_tipo_nombre,
                (select mon_sigla from moneda where moneda_id=cp.moneda_id)as mon_nombre,
                (select count(*)as total from cp where bestado=1 '.$where.')as total
                from cp where bestado=1 '.$where.' order by cp_fec_emis desc'.$limit;
                break;
             case 'C': // Lista Cobranzas
                $sql = 'select*,
                (select cli_nombre from cliente where cliente_id=cp.cliente_id)as cli_nombre,
                (select cp_estado_nombre from cp_estado where cp_estado_id=cp.cp_estado_id)as cp_estado_nombre,
                (select cp_tipo_nombre from cp_tipo where cp_tipo_id=cp.cp_tipo_id)as cp_tipo_nombre,
                (select mon_sigla from moneda where moneda_id=cp.moneda_id)as mon_nombre,
                (select count(*)as total from cp where bestado=1 and cp_estado_id=1 and cp_monto_saldo>0 '.$where.')as total
                from cp where bestado=1 and cp_estado_id=1 and cp_monto_saldo>0 '.$where.' order by cp_fec_emis desc'.$limit;
                break;
            case 'T': // Total Registros
                $sql="SELECT count(*) as T from cp where bestado=1 ".$where;
                break;
        }
        //echo $sql;
        $result = mysql_query($sql) or Msg_error($sql);
        if($accion=='L'||$accion=='C'){
            while($row=mysql_fetch_array($result,1)){
                $reg[]=$row;
            }
        }elseif($accion=='T'){
            $row=mysql_fetch_array($result,1);
            $reg=$row['T'];
        }
        return $reg;
    }

    function cp_edit($id='',$accion=''){
    	$where=($id>'')?" WHERE bestado='1' AND cp_id =$id":" WHERE bestado='1'";
        $estado=($_REQUEST['cp_fec_cancel']>'')?'2':$_REQUEST['cp_estado_id'];
        switch ($accion){
            case 'C':
                $sql="SELECT COUNT(*) AS C FROM cp";
                break;
            case 'S':
                $sql="SELECT *,
                (select emp_nombre from v_cliente where cliente_id=cp.cliente_id limit 1) as cliente,
                (select cp_estado_nombre from cp_estado where cp_estado_id=cp.cp_estado_id)as cp_estado_nombre,
                (select cp_tipo_nombre from cp_tipo where cp_tipo_id=cp.cp_tipo_id)as cp_tipo_nombre
                FROM cp".$where;
                break;
           case 'M':
                $sql="select ifnull(max(cp_id),0)+1 as C from cp where bestado=1";
		break;
            case 'I':
                $sql="insert into cp(cp_accion_id,cli_ruc,cliente_id,cp_tipo_id,cp_nro,cp_fec_emis,cp_fec_venc,moneda_id,cp_monto_tot,cp_descrip,cp_estado_id,cp_adjunto,cp_fec_cancel,cp_medio_id,cp_medio_valor,centro_costo_id,cp_monto_sub,cp_monto_igv,cp_monto_saldo,ocs_id)
                        values(".($_REQUEST['cp_accion_id']+0).",'".$_REQUEST['cli_ruc']."',".($_REQUEST['cliente_id']+0).",".($_REQUEST['cp_tipo_id']).",'".$_REQUEST['cp_nro']."','".$_REQUEST['cp_fec_emis']."','".$_REQUEST['cp_fec_venc']."',
                            ".($_REQUEST['moneda_id']+0).",".($_REQUEST['cp_monto_tot']+0).",'".$_REQUEST['cp_descrip']."',".($estado+0).",'".$_REQUEST['cp_adjunto']."','".$_REQUEST['cp_fec_cancel']."',".($_REQUEST['cp_medio_id']+0).",'".$_REQUEST['cp_medio_valor']."',".($_REQUEST['centro_costo_id']+0).",".(int)$_REQUEST['cp_monto_sub'].",".(int)$_REQUEST['cp_monto_igv'].",".(int)$_REQUEST['cp_monto_tot'].",".(int)$_REQUEST['ocs_id'].")";
                mysql_query($sql) or Msg_error($sql);
                $sql="select LAST_INSERT_ID()";
                break;
            case 'U':
                $sql="update cp set 
                        cliente_id=".($_REQUEST['cliente_id']+0).",
                        cp_tipo_id=".($_REQUEST['cp_tipo_id']+0).",
                        cp_nro='".$_REQUEST['cp_nro']."',
                        cp_fec_emis='".$_REQUEST['cp_fec_emis']."',
                        cp_fec_venc='".$_REQUEST['cp_fec_venc']."',
                        moneda_id=".($_REQUEST['moneda_id']+0).",
                        cp_monto_tot=".($_REQUEST['cp_monto_tot']+0).",
                        cp_monto_saldo=".($_REQUEST['cp_monto_tot']+0).",
                        cp_monto_sub=".($_REQUEST['cp_monto_sub']+0).",
                        cp_monto_igv=".($_REQUEST['cp_monto_igv']+0).",
                        cp_descrip='".$_REQUEST['cp_descrip']."',
                        cp_adjunto='".$_REQUEST['cp_adjunto']."',
                        cp_fec_cancel='".$_REQUEST['cp_fec_cancel']."',
                        cp_medio_id=".($_REQUEST['cp_medio_id']+0).",
                        cp_medio_valor='".$_REQUEST['cp_medio_valor']."',
                        centro_costo_id='".$_REQUEST['centro_costo_id']."',
                        ocs_id='".$_REQUEST['ocs_id']."',
                        cp_estado_id=".($estado+0).$where;
                break;
            case 'D':
                $sql = "update cp set bestado='0' ".$where;
                break;
		}
		if ($sql>''){
            $result = mysql_query($sql) or Msg_error($sql);
			if($accion=='S'){
				$row=mysql_fetch_array($result,1);
				return $row;
			}
			if($accion=='C' || $accion=='M'){
				$row=mysql_fetch_array($result);
				return $row['C'];
			}
                        if($accion=='I'){
				$row=mysql_fetch_array($result);
				return $row[0];
			}
        }
    }

    function cp_adjunto($id=0,$name=''){
        $sql="update cp set cp_adjunto='".$name."' where cp_id=".(int)$id;
        $result = mysql_query($sql) or Msg_error($sql);
    }
    
    function cp_copy($id=0,$accion='',$condicion=''){
		$where=" WHERE cp_id = ".(int)$id;
		switch ($accion){

		case 'I': // Duplicar
			$sql = "insert into cp(cp_nro,cp_accion_id,cliente_id,cp_tipo_id,cp_fec_emis,cp_fec_venc,moneda_id,cp_monto_tot,cp_descrip,cp_estado_id,cp_adjunto,cp_fec_cancel,cp_medio_id,cp_medio_valor,centro_costo_id)
                                select '".(string)$condicion."',cp_accion_id,cliente_id,cp_tipo_id,cp_fec_emis,cp_fec_venc,moneda_id,cp_monto_tot,cp_descrip,cp_estado_id,cp_adjunto,cp_fec_cancel,cp_medio_id,cp_medio_valor,centro_costo_id
                                from cp ".$where;

                        break;               
                case 'D': // Detalle
			$sql = "insert into cp_detalle(cp_id,producto_id,pro_nombre,pro_cantidad,pro_garantia_fin,pro_nroserie,pro_precio_venta,pro_precio_compra,moneda_id,unidad_id,pro_descripcion,pro_subtotal,bestado)
                                select ".(int)$condicion.",producto_id,pro_nombre,pro_cantidad,pro_garantia_fin,pro_nroserie,pro_precio_venta,pro_precio_compra,moneda_id,unidad_id,pro_descripcion,pro_subtotal,bestado
                                from cp_detalle".$where;
			break;
		}                
		if($sql>''){
			$result = mysql_query($sql) or Msg_error($sql);                        
		}

	}

    function cliente_contacto($accion='',$id=''){
        switch ($accion){
            case "C":
                $sql="SELECT COUNT(*) AS C FROM contacto WHERE cliente_id=$id+0 AND bestado='1'";
                break;
            case "S":
                $sql="SELECT * FROM contacto WHERE bestado='1' AND cliente_id=$id ORDER BY con_nombre";
                break;
        }
        $result=mysql_query($sql) or Msg_error($sql);
		if($accion=="S"){
			while($row=mysql_fetch_array($result,1)){$prg[]=$row;}
			return $prg;
		}
		if($accion=="C"){
			$row=mysql_fetch_array($result);
			return $row['C'];
		}
	}
    function contacto_ddl($id=''){
    	$where=($id>'')?" WHERE bestado='1' AND cliente_id=$id":" WHERE bestado='1'";
        $sql = "SELECT * FROM contacto ".$where;
		$result = mysql_query($sql) or Msg_error($sql);
		while($row=mysql_fetch_array($result,1)){$prg[]=$row;}
		return $prg;
    }

    function cp_tipo_ddl($id=''){
    	$where=($id>'')?" WHERE bestado='1' AND cp_tipo_id=$id":" WHERE bestado='1'";
        $sql = "SELECT * FROM cp_tipo ".$where;
		$result = mysql_query($sql) or Msg_error($sql);
		while($row=mysql_fetch_array($result,1)){$prg[]=$row;}
		return $prg;
    }

    function nroreq_contacto($id='',&$contacto_id=''){
    	$sql="SELECT * FROM contacto WHERE bestado='1' AND contacto_id=(SELECT MAX(contacto_id) FROM produccion WHERE bEstado='1' AND pro_nroreq=$id)";
    	$result=mysql_query($sql) or Msg_error($sql);
    	$row=mysql_fetch_array($result);
    	$contacto_id=$row['contacto_id'];
    	return $row;
    }

    function cp_estado_ddl($id=''){
    	$where=($id>'')?" WHERE bestado='1' AND cp_estado_id=$id":" WHERE bestado='1'";
        $sql = "SELECT * FROM cp_estado ".$where;
		$result = mysql_query($sql) or Msg_error($sql);
		while($row=mysql_fetch_array($result,1)){$prg[]=$row;}
		return $prg;
    }

    function cp_medio_ddl($id=''){
    	$where=($id>'')?" WHERE bestado='1' AND cp_medio_id=$id":" WHERE bestado='1'";
        $sql = "SELECT * FROM cp_medio ".$where;
		$result = mysql_query($sql) or Msg_error($sql);
		while($row=mysql_fetch_array($result,1)){$prg[]=$row;}
		return $prg;
    }

    function cp_pro_ddl($id=''){    	
        $sql = "select cp_detalle_id,pro_nombre from cp_detalle where cp_id in(select cp_id from cp where cliente_id=".($id+0).")";
		$result = mysql_query($sql) or Msg_error($sql);
		while($row=mysql_fetch_array($result,1)){$prg[]=$row;}
		return $prg;
    }

    function centro_costo_ddl($id=''){
        $where=($id>'')?" WHERE bestado='1' AND centro_costo_id=$id":" WHERE bestado='1'";
        $sql = "SELECT * FROM centro_costo ".$where;
		$result = mysql_query($sql) or Msg_error($sql);
		while($row=mysql_fetch_array($result,1)){$prg[]=$row;}
		return $prg;
    }

    function cp_cliente_ddl(){
        $sql = "select cliente_id,(select cli_nombre from cliente where cliente_id=cp.cliente_id)as cli_nombre
                from cp
                where cp_fec_emis between '".$_REQUEST['desde']."' and '".$_REQUEST['hasta']."' and bestado='1'
                group by cliente_id,cli_nombre";
		$result = mysql_query($sql) or Msg_error($sql);
		while($row=mysql_fetch_array($result,1)){$prg[]=$row;}
		return $prg;
    }

    function cliente_saldo_ddl($tipo=0){
        $sql = "select cliente_id,(select cli_nombre from cliente where cliente_id=cp.cliente_id)as cli_nombre
                from cp
                where bestado=1 and cp_monto_saldo>0 and cp_accion_id=".(int)$tipo." group by cliente_id,cli_nombre";
		$result = mysql_query($sql) or Msg_error($sql);
		while($row=mysql_fetch_array($result,1)){$prg[]=$row;}
		return $prg;
    }
    function moneda_lista($id=0){
        $where=($id>0)?" where bestado='1' and moneda_id=".$id:" WHERE bestado='1'";
    	$sql="SELECT * FROM moneda ".$where;
    	$result=mysql_query($sql);
    	if(!$result){Msg_error($sql);}
    	while($row=mysql_fetch_array($result,1)) {$prg[]=$row;}
    	return $prg;
    }
    function moneda_tc($moneda_id=0){
        $where=($moneda_id>0)?" where bestado='1' and moneda_id=".$moneda_id:" WHERE bestado='1'";
    	$sql="SELECT * FROM moneda_tc ".$where;
    	$result=mysql_query($sql);
    	if(!$result){Msg_error($sql);}
    	while($row=mysql_fetch_array($result,1)) {$prg[]=$row;}
    	return $prg;
    }
    
    function mon_tipo_cambio($moneda_id_ini=0,$moneda_id_fin=0,$mon_monto=0,$mon_tipo='',$mon_formato=''){
        $sql="SELECT tec_tipo_cambio('$moneda_id_ini','$moneda_id_fin','$mon_monto','$mon_tipo','$mon_formato');";
        $result=mysql_query($sql);
        $row=mysql_fetch_row($result);
        return $row[0];
    }


//*****************************[fin clase]**************************************
}
?>