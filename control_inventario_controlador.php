<?php
    error_reporting(0);
    session_start();
    header('Content-Type: text/html; charset=utf-8');
    $cedula = $_SESSION['cedula'];
    $fechahoy = date('Y-m-d G:i:s', strtotime('-5 hour, -00 minutes, -00 seconds,', strtotime(date('Y-m-d G:i:s'))));
    $fecha_str = strtotime($fechahoy);
    $fecha_num = date('YmdHis', $fecha_str); //20210121210000
    $fecha_print = date('d/m/Y H:i:s', $fecha_str);
    $ticket = "{$cedula}{$fecha_num}";
    
    require_once '../../../../controller_cinco.php';
    $conn_azul = connect_PDO('azul');
    if(isset($_FILES['save_csv_entradaCodigo']['name'])){
        $error = '';
        $msgError = '';
        $ttl_error = '';

        if(is_uploaded_file($_FILES['save_csv_entradaCodigo']['tmp_name'])){
                $edit = $_SESSION['cedula'];
                $data_entra = json_decode($_POST['entra_data']);
                $sede = $data_entra->sede;
                $sql_perm = $conn_inv->prepare("SELECT parametros FROM `parametros_control_inventario` WHERE categoria = '$data_entra->categoria'");
                $sql_perm->execute();
                $error_s = $sql_perm->errorInfo();
                $result_perm = $sql_perm->fetch(PDO::FETCH_ASSOC);
                $permi = json_decode($result_perm['parametros']);
                $permi_sedes = array_search($sede, array_column($permi->sedes, 'sede'));
                $permi_admin = in_array($_SESSION['cedula'], $permi->sedes[$permi_sedes]->permisos->admin);
                $permi_edit = in_array($edit, $permi->sedes[$permi_sedes]->permisos->editor);
                if(!$permi_admin  && !$permi_edit){
                    $msgError = "No Tiene Permisos para Editar";
                    $error = 'Permiso Edición';
                }
                else{
                    $csv_file = fopen($_FILES['save_csv_entradaCodigo']['tmp_name'], 'r');
                    $linea_texto = FGETS($csv_file, 65535); 
                    $explode_valores = explode(";", $linea_texto);
                    $columnas=count($explode_valores);
                    $separador = ";";
                    $edit = $_SESSION['cedula'];
                    if ($columnas < 1) {
                        $explode_valores = explode(",", $linea_texto);
                        $separador = ",";
                    }
                    $titulos = file($_FILES['save_csv_entradaCodigo']['tmp_name']);
                    $num_line = 0;
                    $tt_cod = '';
                    foreach($titulos as $lin_num => $linea){
                        if($num_line == 0){
                            $datos = explode($separador, $linea);
                            $tt_cod = strtoupper(strClear($datos[0]));
                        }
                        $num_line++;
                    }
                    $arr_cel_vacio = array();
                    $fila_csv = 2;
                    $json_err = array();
                    $serial_arr = array();
                    $serial_dupl = array();
                    $cant_ser = array();
                    $arr_guardar = array();
                    $json_err = array();
                    $arr_cod = array();
                    $arr_no_exi = array();
                    $arr_cod_serial = array();
                    $arr_ser_base = array();
                    $save_ok = true;

                    $sql = $conn_inv->prepare("SELECT id, codigo, descripcion, precio, tipo, categoria, historial_precios FROM `control_inventario_base_codigos`  WHERE categoria = '$data_entra->categoria'");
                    $sql->execute();
                    $sql_error = $sql->errorInfo();
                    $sql_count = $sql->rowCount();
                    $arr_resul_cod = $sql->fetchAll(PDO::FETCH_ASSOC);
                    
                    $sql_insert_cod = 'INSERT INTO control_inventario_movimientos_entrada(fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, odc, serial, cantidad, precio, observaciones, edit, fecha_edit) VALUES ';
                    $sqlMarks = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                    
                    $marks = [];
                    $values = [];
                    $arrnew = [];
                    $id = '';
                    $sql_insert_cod_serial = 'INSERT INTO control_inventario_base_seriales(fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, odc, serial, cantidad, precio, observaciones, historial, edit, fecha_edit) VALUES ';
                    $sqlMarks_serial = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                    $marks_serial = [];
                    $values_serial = [];
                    $val_file = false;
                    $msgerr1 = [];
                    if('CODIGO' == $tt_cod){
                        $val_file = true;
                    }else{
                        $error = 'y no Modifique el Formato de la Plantilla';
                        $msgError = 'Descargue Nuevamente la Plantilla';
                        $ttl_error = 'Archivo Incorrecto';
                    }
                    if($val_file){
                        $count_err_ser = 0;
                        while(($data = fgetcsv($csv_file, 65535, $separador, "'")) !== FALSE){
                            
                            $codigo = strtoupper(strClear($data[0]));
                            $cantidad = number_format((strClear($data[1])), 0);
                            $precio = strClear($data[2]);
                            $serial = strtoupper(strClear($data[3]));
                            $odc = strtoupper(strClear($data[4]));
                            $observaciones = strtoupper(strClear($data[5]));
                            $letra = 'A';
                            
                            $error_sql = '';
                            $codd .= $codigo; 
                            
                            if (strpos($codigo, 'E+') !== false) {
                                
                                $celda = $letra.$fila_csv;
                                $json_err[] = array(
                                    'celda' => $celda,
                                    'estado' => 'Error',
                                    'descripcion' => 'Error de escritura [E+]'
                                    );
                                    $save_ok = false;   
                            }
                            
                            foreach($arr_resul_cod as $clave => $valor) {
                                array_push($msgerr1, $valor['precio'], strtoupper($data_entra->categoria), $valor['id']);
                                if($valor['codigo'] == $codigo && $valor['categoria'] == strtoupper($data_entra->categoria)){
                                    if($valor['precio'] != $precio){
                                        $id = $valor['id'];
                                        
                                        $histo = json_decode($valor['historial_precios']);
                                        array_push($histo, array('fecha'=>$fechahoy, 'precio' => $precio));
                                        // array_push($arrnew, $precio, json_encode($historial), $edit, $fechahoy, $id);
                                        
                                        $arrnew[] = array(
                                            'precio' => $precio,
                                            'historial_precios' => json_encode($histo),
                                            'id' =>$id
                                        );
                                    }
                                }
                            }
                            for($i_v=0; $i_v <= ($columnas-1) ; $i_v++){
                                if($i_v<3){
                                    
                                    $celda = $letra.$fila_csv;
                                    $letra++;
                                    if(empty(trim($data[$i_v]))){
                                        
                                        $arr_cel_vacio[] = array(
                                            'celda' => $celda,
                                            'error' => 'Celda Vacía'
                                        );
                                        $save_ok = false;
                                    }
                                }
                            }
                            if(array_search($codigo, array_column($arr_resul_cod, 'codigo')) !== false){
                            
                                $pos_tipo = array_search($codigo, array_column($arr_resul_cod, 'codigo'));
                                $tipo = $arr_resul_cod[$pos_tipo]['tipo'];
                                if ($tipo == 'NORMAL'){
                                    $marks[] = $sqlMarks;
                                    array_push($values, $data_entra->fecha, $data_entra->ticket, $codigo,811042087,strtoupper($data_entra->movimiento), strtoupper($data_entra->categoria), strtoupper($data_entra->sub_cate),strtoupper($data_entra->sede),strtoupper($odc), strtoupper($serial), $cantidad, $precio, strtoupper($observaciones), $edit, $fechahoy);
                                    $pos_cod = array_search($codigo, array_column($arr_resul_cod, 'codigo'));
                                    $arr_cod[] = array(
                                        'codigo' => $codigo,
                                        'descripcion' => $arr_resul_cod[$pos_cod]['descripcion'],
                                        'cantidad' => $cantidad,
                                        'precio' => $precio,
                                        'serial' => $serial,
                                        'odc' => $odc,
                                        'observaciones' => $observaciones
                                        );
                                }
                                else{
                                    
                                    if(in_array($serial, $serial_arr)){
                                        $serial_dupl[] = array(
                                            'celda' => 'A'.$fila_csv,
                                            'error' => 'Serial: '.$serial.' Esta Duplicado'
                                        );
                                        $count_err_ser++;
                                        $save_ok = false;
                                    }
                                    else if($cantidad > 1){
                                        $cant_ser[] = array(
                                            'celda' => 'A'.$fila_csv,
                                            'error' => 'Cantidad: En El Codigo '.$codigo.' no puede Ser Mayor a Uno'
                                        );
                                        $count_err_ser++;
                                        $save_ok = false;
                                    }
                                    else{    
                                        $serial_arr[] = $serial;
                                        $marks_serial[] = $sqlMarks_serial;
                                        $fecha_for = ''; 
                                        $fecha_for = $data_entra->fecha.' '.date('H:i:s');
                                        $historial_ser = array(
                                            array(
                                                "fecha" => $fechahoy,
                                                "fecha_edit" => $fecha_for, 
                                                "movimiento" => strtoupper($data_entra->movimiento), 
                                                "ticket" => $data_entra->ticket, 
                                                "edit" => $edit, 
                                                "codigo" => $codigo,
                                                "origen" => url(), 
                                                "cedula" => 811042087, 
                                                "serial" => $serial, 
                                                "precio" => $precio,
                                                "sede" => strtoupper($data_entra->sede),
                                                "observaciones" => $observaciones
                                            )
                                        ); 
                                        // (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, odc, serial, cantidad, precio, observaciones, edit, fecha_edit)
                                        array_push($values_serial, $data_entra->fecha, $data_entra->ticket, $codigo,811042087,strtoupper($data_entra->movimiento), strtoupper($data_entra->categoria), strtoupper($data_entra->sub_cate),strtoupper($data_entra->sede),strtoupper($odc), strtoupper($serial), $cantidad, $precio, strtoupper($observaciones), json_encode($historial_ser), $edit, $fechahoy);
                                        $pos_cod_serial = array_search($codigo, array_column($arr_resul_cod, 'codigo'));
                                        $arr_cod_serial[] = array(
                                            'codigo' => $codigo,
                                            'descripcion' => $arr_resul_cod[$pos_cod_serial]['descripcion'],
                                            'cantidad' => $cantidad,
                                            'precio' => $precio,
                                            'serial' => $serial,
                                            'odc' => $odc,
                                            'observaciones' => $observaciones
                                        );
                                    }
                                }
                            }
                            else{
                                $arr_no_exi[] = array(
                                    'celda' => 'A'.$fila_csv,
                                    'error' => 'Codigo: '.$codigo.' No Existe Para Esta Categoria'
                                );
                                $save_ok = false;
                            }
                        
                            $fila_csv++;
                        }
                    }
                }
                $serial_arr = implode(',', $serial_arr);
                $sql_ser_base = $conn_inv->prepare("SELECT serial FROM `control_inventario_base_seriales`  WHERE serial IN ($serial_arr)");
                $sql_ser_base->execute();
                $sql_error = $sql_ser_base->errorInfo();
                $cnt_ser_base = $sql_ser_base->rowCount();
                if($cnt_ser_base){
                    $arr_resul_serial = $sql_ser_base->fetchAll(PDO::FETCH_ASSOC);
                    foreach($arr_resul_serial as $clave => $valor){
                        $arr_ser_base[] = array(
                            'serial' => $valor['serial'],
                            'error' => 'Serial: '.$valor['serial'].' Ya Existe En La Base'
                        );
                    };
                    $save_ok = false;
                }
                if($save_ok){
                    // Armar consulta con marcadores
                    $sqlUp = $conn_inv->prepare("UPDATE control_inventario_base_codigos SET precio = ?, historial_precios = ?, edit = ?, fecha_edit = ? WHERE id = ? LIMIT 1");
                    foreach($arrnew as $clave => $valor) {
                        $arrSet = array(
                            $valor['precio'],
                            json_encode($valor['historial_precios']),
                            $edit, 
                            $fechahoy,
                            $valor['id']
                            );
                        $sqlUp-> execute($arrSet);

                    }
                    
                    if ($count_err_ser == 0 && count($marks_serial)){    
                        
                        $sql_insert_cod_serial = $conn_inv->prepare($sql_insert_cod_serial . implode(',', $marks_serial));
                     
                        $sql_insert_cod_serial->execute($values_serial);
                        $sql_error_ins_ser = $sql_insert_cod_serial->errorInfo();
                        $sql_count_ins_ser = $sql_insert_cod_serial->rowCount();
                        if($sql_error_ins_ser[0] == 00000){
                            $countOk_ser = $sql_count_ins_ser;
                            $msgSql_ins_ser = 'Se guardaron '.$countOk_ser.' registros';
                        }
                        else{
                            $msgSql_ins_ser = 'Error al Guardar Registros, Verifique el Archivo';
                        }
                    }
                    if(count($marks)){ 
                        $sql_insert_cod = $conn_inv->prepare($sql_insert_cod . implode(',', $marks));
                        // Ejecutar consulta con arreglo de valores
                        $sql_insert_cod->execute($values);
                        $sql_error_ins = $sql_insert_cod->errorInfo();
                        $sql_count_ins = $sql_insert_cod->rowCount();
                        if($sql_error_ins[0] == 00000){
                            $countOk = $sql_count_ins;
                            $msgSql_ins = 'Se guardaron '.$countOk.' registros';
                        }
                        else{
                            $msgSql_ins = 'Error al Guardar Registros, Verifique el Archivo';
                        }
                }
                }
                if($error != ''){
                    $output = array(
                        'success' => false, 
                        'msg' => $msgError, 
                        'error'=> $error,
                        'msgSql_ins' => $msgSql_ins,
                        'title_error' => $ttl_error,
                        'titleerr' => $tt_cod,
                        'info' => $val_file,
                        'info2' => $dat,
                        'info3' => strcmp($tt_cod, $dat),
                        'info4' => $arr_resul_cod,
                        'info5' => $serial_arr,
                        'info6' => $serial_dupl,
                        'info7' => $count_err_ser,
                        'ser_base' => json_decode(json_encode($arr_ser_base))
                    );
                }
                else{
                    $output = array(
                        'success' => true, 
                        'msg' => 'Entrada realizada con exito.', 
                        'celdas_vacias' => json_decode(json_encode($arr_cel_vacio)),
                        'cnt_celvac' => count($arr_cel_vacio),
                        'err_escritura' => json_decode(json_encode($json_err)),
                        'cnt_erres' => count($json_err),
                        'codigo_nexi' => json_decode(json_encode($arr_no_exi)),
                        'cnt_nexi' => count($arr_no_exi),
                        'codigos_guar' => json_decode(json_encode($arr_cod)),
                        'codigos_guar_ser' => json_decode(json_encode($arr_cod_serial)),
                        'msgSql_ins' => $msgSql_ins,
                        'sql_error_ins' => $sql_error_ins,
                        'sql_count_ins' => $sql_count_ins,
                        'msgSql_ins_ser' => $msgSql_ins_ser,
                        'sql_error_ins_ser' => $sql_error_ins_ser,
                        'sql_count_ins_ser' => $sql_count_ins_ser,
                        'title'=> $tt_cod,
                        'serial_duplicado' => $serial_dupl,
                        'cantidad_err' => $cant_ser,
                        'info' => $serial_arr,
                        'exis_ser_base' => json_decode(json_encode($arr_ser_base)),
                        'count_ser_base'=> count($arr_ser_base)
                        
                    );
    
                }
                
                echo json_encode($output);
            
        }   
    }
    else if(isset($_FILES['save_csv_entregaCodigo']['name'])){
        $error = '';
        $msgError = '';
        $ttl_error = '';
        if(is_uploaded_file($_FILES['save_csv_entregaCodigo']['tmp_name'])){
            $edit = $_SESSION['cedula'];
            $data_entra = json_decode($_POST['entre_data']);
            $sede = $data_entra->sede;
            $sql_perm = $conn_inv->prepare("SELECT parametros FROM `parametros_control_inventario` WHERE categoria = '$data_entra->categoria'");
            $sql_perm->execute();
            $error_s = $sql_perm->errorInfo();
            $result_perm = $sql_perm->fetch(PDO::FETCH_ASSOC);
            $permi = json_decode($result_perm['parametros']);
            $permi_sedes = array_search($sede, array_column($permi->sedes, 'sede'));
            $permi_admin = in_array($_SESSION['cedula'], $permi->sedes[$permi_sedes]->permisos->admin);
            $permi_edit = in_array($edit, $permi->sedes[$permi_sedes]->permisos->editor);
            if(!$permi_admin  && !$permi_edit){
                $msgError = "No Tiene Permisos para Editar";
                $error = 'Permiso Edición';
            }
            else{
                $csv_file = fopen($_FILES['save_csv_entregaCodigo']['tmp_name'], 'r');
                $linea_texto = FGETS($csv_file, 65535); 
                $explode_valores = explode(";", $linea_texto);
                $columnas=count($explode_valores);
                $separador = ";";
                $edit = $_SESSION['cedula'];
                if ($columnas < 1) {
                    $explode_valores = explode(",", $linea_texto);
                    $separador = ",";
                }
                $titulos = file($_FILES['save_csv_entregaCodigo']['tmp_name']);
                $num_line = 0;
                $tt_cod = '';
                foreach($titulos as $lin_num => $linea){
                    if($num_line == 0){
                        $datos = explode($separador, $linea);
                        $tt_cod = strtoupper(strClear($datos[0]));
                    }
                    $num_line++;
                }
                $arr_cel_vacio = array();
                $fila_csv = 2;
                $json_err = array();
                $serial_arr = array();
                $serial_dupl = array();
                $cant_ser = array();
                $arr_guardar = array();
                $json_err = array();
                $arr_cod = array();
                $arr_no_exi = array();
                $arr_cod_serial = array();
                $arr_ser_base = array();
                $arr_cedula_base = array();
                $save_ok = true;
                $sql_cedula = $conn_azul->prepare("SELECT id, cedula FROM `cinco_base_de_personal`  WHERE  estado = 'ACTIVO'");
                $sql_cedula->execute();
                $arr_cedula = $sql_cedula->fetchAll(PDO::FETCH_ASSOC);

                $sql_ser_base = $conn_inv->prepare("SELECT id, codigo, serial, historial FROM `control_inventario_base_seriales`  WHERE categoria = '$data_entra->categoria'");
                $sql_ser_base->execute();
                $sql_error = $sql_ser_base->errorInfo();
                $cnt_ser_base = $sql_ser_base->rowCount();
                $arr_resul_serial = $sql_ser_base->fetchAll(PDO::FETCH_ASSOC);
                $sql = $conn_inv->prepare("SELECT id, codigo, descripcion, precio, tipo, categoria, historial_precios FROM `control_inventario_base_codigos`  WHERE categoria = '$data_entra->categoria'");
                $sql->execute();
                $sql_error = $sql->errorInfo();
                $sql_count = $sql->rowCount();
                $arr_resul_cod = $sql->fetchAll(PDO::FETCH_ASSOC);
                
                $sql_insert_cod = 'INSERT INTO control_inventario_movimientos_entrega(fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, serial, cantidad, observaciones, edit, fecha_edit) VALUES ';
                $sqlMarks = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $count_serial = 0;
                $marks = [];
                $values = [];
                $arrnew = [];
                $id = '';
                $val_file = false;
                $msgerr1 = [];
                $ids = array();
                $updateParams = array();
                $conpos = 0;
                if('CODIGO' == $tt_cod){
                    $val_file = true;
                }else{
                    $error = 'y no Modifique el Formato de la Plantilla';
                    $msgError = 'Descargue Nuevamente la Plantilla';
                    $ttl_error = 'Archivo Incorrecto';
                }
                $serial_b ='';
                if($val_file){
                    $count_err_ser = 0;
                    while(($data = fgetcsv($csv_file, 65535, $separador, "'")) !== FALSE){
                        $codigo = strtoupper(strClear($data[0]));
                        $cantidad = number_format((strClear($data[1])), 0);
                        $serial = strtoupper(strClear($data[2]));
                        $cc_data = strtoupper(strClear($data[3]));
                        $observaciones = strtoupper(strClear($data[4]));
                        $letra = 'A';
                        $serial_b .=  $serial;
                        $error_sql = '';
                        $codd .= $codigo; 
                        
                        if (strpos($codigo, 'E+') !== false || strpos($serial, 'E+') !== false) {
                            $celda = $letra.$fila_csv;
                            $json_err[] = array(
                                'celda' => $celda,
                                'estado' => 'Error',
                                'descripcion' => 'Error de escritura [E+]'
                            );
                            $save_ok = false;   
                        }
                        for($i_v=0; $i_v <= ($columnas-1) ; $i_v++){
                            if($i_v<2){
                                
                                $celda = $letra.$fila_csv;
                                $letra++;
                                if(empty(trim($data[$i_v]))){
                                    
                                    $arr_cel_vacio[] = array(
                                        'celda' => $celda,
                                        'error' => 'Celda Vacía'
                                    );
                                    $save_ok = false;
                                }
                            }
                        }
                        $pos_cedula = array_search($cc_data, array_column($arr_cedula, 'cedula'));
                        if(is_bool($pos_cedula)){
                            $conpos++;
                            $arr_cedula_base[] = array(
                                'cedula' => $cc_data,
                                'error' => 'Cedula: '.$cc_data.' No Existe'
                            );
                            $save_ok = false;
                        }
                        if(array_search($codigo, array_column($arr_resul_cod, 'codigo')) !== false){
                            $pos_tipo = array_search($codigo, array_column($arr_resul_cod, 'codigo'));
                            $tipo = $arr_resul_cod[$pos_tipo]['tipo'];
                            if ($tipo == 'NORMAL'){
                                $marks[] = $sqlMarks;
                                array_push($values, $data_entra->fecha, $data_entra->ticket, $codigo,$cc_data,strtoupper($data_entra->movimiento), strtoupper($data_entra->categoria), strtoupper($data_entra->sub_cate),strtoupper($data_entra->sede), strtoupper($serial), $cantidad, strtoupper($observaciones), $edit, $fechahoy);
                                $pos_cod = array_search($codigo, array_column($arr_resul_cod, 'codigo'));
                                $arr_cod[] = array(
                                    'codigo' => $codigo,
                                    'cedula' => $cc_data,
                                    );
                            }
                            else{
                                if(in_array($serial, $serial_arr)){
                                    $serial_dupl[] = array(
                                        'celda' => 'A'.$fila_csv,
                                        'error' => 'Serial: '.$serial.' Esta Duplicado'
                                    );
                                    $count_err_ser++;
                                };
                                if($cantidad > 1){
                                    $cant_ser[] = array(
                                        'celda' => 'A'.$fila_csv,
                                        'error' => 'Cantidad: En El Codigo '.$codigo.' no puede Ser Mayor a Uno'
                                    );
                                    $save_ok = false;
                                    $count_err_ser++;
                                };
                                if(array_search($serial, array_column($arr_resul_serial, 'serial')) === false){
                                    $conpos++;
                                    $arr_ser_base[] = array(
                                        'celda' => 'C'.$fila_csv,
                                        'error' => 'Serial: '.$serial.' No Existe o No Esta Disponible'
                                    );
                                    $save_ok = false;
                                }
                                else{  
                                    $fecha_for = ''; 
                                    $fecha_for = $data_entra->fecha.' '.date('H:i:s');
                                    $historial_ser = array(
                                        array(
                                            "fecha" => $fechahoy,
                                            "fecha_edit" => $fecha_for, 
                                            "movimiento" => "ENTREGA", 
                                            "ticket" => $data_entra->ticket,
                                            "codigo" => $codigo,
                                            "edit" => $edit, 
                                            "origen" => url(), 
                                            "cedula" => $cc_data, 
                                            "serial" => $serial, 
                                            "precio" => $precio,
                                            "sede" => strtoupper($data_entra->sede),
                                            "observaciones" => $observaciones
                                        )
                                    );   
                                    $serial_arr[] = $serial;
                                    $pos_codigo_ser = array_search($serial, array_column($arr_resul_serial, 'serial'));
                                    $id = $arr_resul_serial[$pos_codigo_ser]['id'];
                                    $ids[] = intval($id);
                                    $histo_base = $arr_resul_serial[$pos_codigo_ser]['historial'];
                                    $contentsDecoded = json_decode($histo_base, true);
                                    $contentsDecoded[] = $historial_ser; 
                                    $historial = json_encode($contentsDecoded, JSON_UNESCAPED_UNICODE);
                                    $updateParams['cedula'][$id] = $cc_data;
                                    $updateParams['movimiento'][$id] = "ENTREGA";
                                    $updateParams['observaciones'][$id] = $observaciones;
                                    $updateParams['historial'][$id] = $historial;
                                    $updateParams['edit'][$id] = $edit;
                                    $updateParams['fecha_edit'][$id] = $fechahoy;
                                    $count_serial++;
                                    $arr_cod_serial[] = array(
                                        'serial' => $serial,
                                        'cedula' => $cc_data
                                    );
                                }
                            }
                        }
                        else{
                            $arr_no_exi[] = array(
                                'celda' => 'A'.$fila_csv,
                                'error' => 'Codigo: '.$codigo.' No Existe Para Esta Categoria'
                            );
                            $save_ok = false;
                        }
                        $fila_csv++;
                    }
                }
            }
            if($save_ok){
                // Armar consulta con marcadore
                if ($count_err_ser == 0 && $count_serial>0){    
                    $data_qry_upd_ser = createQryUpdate('control_inventario_base_seriales', 'id', $ids, $updateParams);
                    $qry_exe_s = $conn_inv->prepare($data_qry_upd_ser);
                    $exe_ser_d = $qry_exe_s->execute();
                }
                if(count($marks)){ 
                    $sql_insert_cod = $conn_inv->prepare($sql_insert_cod . implode(',', $marks));
                    // Ejecutar consulta con arreglo de valores
                    $exe = $sql_insert_cod->execute($values);
                    $sql_error_ins = $sql_insert_cod->errorInfo();
                    $sql_count_ins = $sql_insert_cod->rowCount();
                    if($sql_error_ins[0] == 00000){
                        $countOk = $sql_count_ins;
                        $msgSql_ins = 'Se guardaron '.$countOk.' registros';
                    }
                    else{
                        $msgSql_ins = 'Error al Guardar Registros, Verifique el Archivo';
                    }
            }
            }
            if($error != ''){
                $output = array(
                    'success' => false, 
                    'msg' => $msgError, 
                    'error'=> $error,
                    'msgSql_ins' => $msgSql_ins,
                    'title_error' => $ttl_error,
                    'titleerr' => $tt_cod,
                    'info' => $val_file,
                    'posced' => $pos_cedula,
                    'info3' => strcmp($tt_cod, $dat),
                    'info4' => $arr_resul_cod,
                    'info5' => $serial_arr,
                    'info6' => $serial_dupl,
                    'info7' => $count_err_ser,
                    'all_ced' => $arr_cedula,
                    'ser_base' => json_decode(json_encode($arr_ser_base)),
                    'posi'=> $conpos,
                    'no existe' => $arr_no_exi,
                    'cedula_err' => $arr_cedula_base,
                    'save' => $save_ok
                );
            }
            else {
                $output = array(
                    'success' => true, 
                    'all_ced' => $arr_cedula,
                    'msg' => 'Entrega realizada con exito.', 
                    'celdas_vacias' => json_decode(json_encode($arr_cel_vacio)),
                    'cnt_celvac' => count($arr_cel_vacio),
                    'err_escritura' => json_decode(json_encode($json_err)),
                    'cnt_erres' => count($json_err),
                    'codigo_nexi' => json_decode(json_encode($arr_no_exi)),
                    'cnt_nexi' => count($arr_no_exi),
                    'codigos_guar' => json_decode(json_encode($arr_cod)),
                    'codigos_guar_ser' => json_decode(json_encode($arr_cod_serial)),
                    'exe_ser_d' => $exe_ser_d,
                    'sql_error_ins' => $sql_error_ins,
                    'sql_count_ins' => $sql_count_ins,
                    'msgSql_ins_ser' => $msgSql_ins_ser,
                    'cedula_err' => $arr_cedula_base,
                    'sql_count_ins_ser' => $sql_count_ins_ser,
                    'posi'=> $conpos,
                    'serial_duplicado' => $serial_dupl,
                    'cantidad_err' => $cant_ser,
                    'info' => $serial_arr,
                    'exis_ser_base' => json_decode(json_encode($arr_ser_base)),
                    'count_ser_base'=> count($arr_ser_base),
                    'result_ser' => $arr_resul_serial,
                    'prueba' => $ddd,
                    'serial_b' => $serial_b
                );
            }
            echo json_encode($output);
        }   
    }
    else if(isset($_FILES['save_csv_codigos']['name'])){
        $error = '';
        $msgError = '';
        $ttl_error = '';
        if(is_uploaded_file($_FILES['save_csv_codigos']['tmp_name'])){
            $edit = $_SESSION['cedula'];
            $import_entra = json_decode($_POST['import_data']);
            $sede = $import_entra->sede;
            $sql_perm = $conn_inv->prepare("SELECT parametros FROM `parametros_control_inventario` WHERE categoria = '$import_entra->categoria'");
            $sql_perm->execute();
            $error_s = $sql_perm->errorInfo();
            $result_perm = $sql_perm->fetch(PDO::FETCH_ASSOC);
            $permi = json_decode($result_perm['parametros']);
            $permi_sedes = array_search($sede, array_column($permi->sedes, 'sede'));
            $permi_admin = in_array($_SESSION['cedula'], $permi->sedes[$permi_sedes]->permisos->admin);
            $permi_edit = in_array($edit, $permi->sedes[$permi_sedes]->permisos->editor);
            if(!$permi_admin  && !$permi_edit ){
                $msgError = "No Tiene Permisos para Editar";
                $error = 'Permiso Edición';
            }
            else
            {   
                $csv_file = fopen($_FILES['save_csv_codigos']['tmp_name'], 'r');
                $linea_texto = FGETS($csv_file, 65535); //lee PRIMERA linea del archivo
                $explode_valores = explode(";", $linea_texto);
                $columnas=count($explode_valores);
                $separador = ";";
                $edit = $_SESSION['cedula'];
                if ($columnas < 1) {
                    $explode_valores = explode(",", $linea_texto);
                    $separador = ",";
                }
                $titulos = file($_FILES['save_csv_codigos']['tmp_name']);
                $num_line = 0;
                $tt_cod = '';
                foreach($titulos as $lin_num => $linea){
                    if($num_line == 0){
                        $datos = explode($separador, $linea);
                        $tt_cod = trim(strtoupper($datos[0]));
                    }
                    $num_line++;
                }
                $arr_cel_vacio = array();
                $ct_err = 0;
                $cont_err_va=0;
                $fila_csv = 2;
                $msgAll = "";
                $msgSql = "";
                $countOk = 0;
                $err_B='';
                $countDup = 0;
                $arrDupli = array();
                $arr_guardar = array();
                $json_err = array();
                
                $save_ok = true;

                $sql = $conn_inv->prepare("SELECT codigo FROM `control_inventario_base_codigos`  WHERE categoria = '$import_entra->categoria'");
                $sql->execute();
                $sql_error = $sql->errorInfo();
                $sql_count = $sql->rowCount();
                $arr_resul_cod = $sql->fetchAll(PDO::FETCH_ASSOC);

                // Crear cadena para consulta, sin valores ni parámetros
                $sql_insert_cod = 'INSERT INTO control_inventario_base_codigos(codigo, descripcion, precio, medida, tipo, categoria, subcategoria, area, historial_precios, edit, fecha_edit) VALUES ';
                // En lugar de parámetros con nombre, se van a usar por marcadores
                $sqlMarks = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                // Inicializar arreglo de marcadores y valores
                $marks = [];
                $values = [];
                $val_file = false;
                if($tt_cod == 'CODIGO_IMP_BASE'){
                    $val_file = true;
                }
                else{
                    $error = 'Archivo Incorrecto';
                    $msgError = 'Descargue Nuevamente la Plantilla';
                    $ttl_error = 'Debe Subir el Archivo Correcto';
                }
                if($val_file){
                while(($data = fgetcsv($csv_file, 65535, $separador, "'")) !== FALSE){
                    $codigo = mb_strtoupper(utf8_encode(strClear($data[0])));
                    $descripcion = mb_strtoupper(utf8_encode( strClear($data[1])));
                    $precio = strClear($data[2]);
                    $medida = strtoupper(strClear($data[3]));
                    $serial = strClear($data[4]);
                    $historial_pre = array(array('fecha'=>$fechahoy, 'precio' => $precio));
                    if(strtoupper($serial) == "SI"){
                        $serial = "SERIALIZADO";
                    }
                    else{
                        $serial = "NORMAL";
                    }
                    $letra = 'A';
                    
                        if (strpos($codigo, 'E+') !== false) {
                            $ct_err++;
                            $letra++;
                            $celda = $letra.$fila_csv;
                            $json_err[] = array(
                                'celda' => $celda,
                                'estado' => 'Error',
                                'descripcion' => 'Error de escritura [E+]'
                            );  
                            $save_ok = false;   
                            $msgAll = ' tiene '.$ct_err.' errores de escritura [E+] ';   
                        }
                        for($i_v=0; $i_v <= ($columnas-1) ; $i_v++){
                            if($i_v<2){
                                $celda = $letra.$fila_csv;
                                $letra++;
                                if(empty(trim($data[$i_v]))){
                                    $cont_err_va++;
                                    $arr_cel_vacio[] = array(
                                        'celda' => $celda,
                                        'error' => 'Celda Vacía'
                                        
                                    );
                                    $save_ok = false;
                                    //$row_v = false;
                                    $msgAll = ' tiene '.$cont_err_va.' celdas vacias ';
                                }
                            }
                        }
                        if(array_search($codigo, array_column($arr_resul_cod, 'codigo')) === false){
                            // Agregar marcadores y valores a sus respectivos arreglos
                            $marks[] = $sqlMarks;
                            array_push($values, $codigo, $descripcion, $precio, $medida, $serial, strtoupper($import_entra->categoria), strtoupper($import_entra->sub_cate),json_encode(['general']),json_encode($historial_pre), $edit, $fechahoy);
                            $arr_guardar[] = array(
                                'descripcion' => $descripcion,
                                'codigo' => $codigo,
                                'precio' => $precio,
                                'medida' => $medida,
                                'tipo' => $serial,
                                'categoria' => strtoupper($import_entra->categoria),
                                'subcategoria' => strtoupper($import_entra->sub_cate)
                            );
                        }
                        else{
                            $countDup++;
                            $arrDupli[] = array(
                                'celda' => 'A'.$fila_csv,
                                'error' => 'codigo duplicado',
                                'codigo' => $codigo
                            );
                            $save_ok = false;
                        };
                        $fila_csv++;
                    }       
                }
            }
            if($save_ok){
                // Armar consulta con marcadores
                $sql_insert_cod = $conn_inv->prepare($sql_insert_cod . implode(',', $marks));
                // Ejecutar consulta con arreglo de valores
                $sql_insert_cod->execute($values);
                $sql_error_ins = $sql_insert_cod->errorInfo();
                $sql_count_ins = $sql_insert_cod->rowCount();
                if($sql_error_ins[0] == 00000){
                    $countOk = $sql_count_ins;
                    $msgSql_ins = 'Se guardaron '.$countOk.' registros';
                }
                else{
                    $msgSql_ins = 'Error al Guardar Registros, Verifique el Archivo';
                }
            }
            $msgSql = "Tiene ".$countDup." codigos duplicados";
            if($error == ''){
                $output = array(
                    'success' => true,
                    'info' => $err_B,
                    'errorduplic' => $arrDupli,
                    'cantidadDuplic' => $countDup,
                    'errores_vac' => $arr_cel_vacio,
                    'errores_Es' => $json_err,
                    'cantidad_err_va' => $cont_err_va,
                    'cantidad_err_Es' => $ct_err,
                    'codigos_correctos' => $arr_guardar,
                    'msgSql' => $msgSql,
                    'msgAll' => $msgAll,
                    'sql_error_ins' => $sql_error_ins,
                    'sql_count_ins' => $sql_count_ins,
                    'msgSql_ins' => $msgSql_ins,
                    'save_ok' => $save_ok,
                    'title'=> $eee
                );
            }
            else{
                $output = array(
                    'success' => false, 
                    'msg' => $msgError, 
                    'error'=> $error,
                    'title'=> $eee,
                    'title_error' => $ttl_error
                );
            }
            echo json_encode($output, JSON_UNESCAPED_UNICODE);
        }
        
    }
    else if(isset($_FILES['save_csv_traslado']['name'])){
        $error = '';
        $msgError = '';
        $ttl_error = '';
        if(is_uploaded_file($_FILES['save_csv_traslado']['tmp_name'])){
            $edit = $_SESSION['cedula'];
            $import_entra = json_decode($_POST['entre_data_traslado']);
            $sede = $import_entra->sede;
            $sql = $conn_inv->prepare("SELECT codigo, serial, cantidad, categoria, precio, odc FROM `control_inventario_base_seriales` WHERE categoria = '$import_entra->categoria'");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql_perm = $conn_inv->prepare("SELECT parametros FROM `parametros_control_inventario` WHERE categoria = '$import_entra->categoria'");
            $sql_perm->execute();
            $error_s = $sql_perm->errorInfo();
            $result_perm = $sql_perm->fetch(PDO::FETCH_ASSOC);
            $permi = json_decode($result_perm['parametros']);
            $permi_sedes = array_search($sede, array_column($permi->sedes, 'sede'));
            $permi_admin = in_array($_SESSION['cedula'], $permi->sedes[$permi_sedes]->permisos->admin);
            $permi_edit = in_array($edit, $permi->sedes[$permi_sedes]->permisos->editor);
            if(!$permi_admin  && !$permi_edit ){
                $msgError = "No Tiene Permisos para Editar";
                $error = 'Permiso Edición';
            }
            else
            {   
                $csv_file = fopen($_FILES['save_csv_traslado']['tmp_name'], 'r');
                $linea_texto = FGETS($csv_file, 65535); //lee PRIMERA linea del archivo
                $explode_valores = explode(";", $linea_texto);
                $columnas=count($explode_valores);
                $separador = ";";
                $edit = $_SESSION['cedula'];
                if ($columnas < 1) {
                    $explode_valores = explode(",", $linea_texto);
                    $separador = ",";
                }
                $titulos = file($_FILES['save_csv_traslado']['tmp_name']);
                $num_line = 0;
                $tt_cod = '';
                foreach($titulos as $lin_num => $linea){
                    if($num_line == 0){
                        $datos = explode($separador, $linea);
                        $tt_cod = trim(strtoupper($datos[0]));
                    }
                    $num_line++;
                }
                $serial_arr = array();
                $arr_cel_vacio = array();
                $ct_err = 0;
                $cont_err_va=0;
                $fila_csv = 2;
                $arr_guardar = array();
                $json_err = array();
                $serial_dupl = array();
                $val_file = false;
                $arr_ser_base = array();
                if($tt_cod == 'SERIAL'){
                    $val_file = true;
                }
                else{
                    $error = 'Archivo Incorrecto';
                    $msgError = 'Descargue Nuevamente la Plantilla';
                    $ttl_error = 'Debe Subir el Archivo Correcto';
                }
                if($val_file){
                    while(($data = fgetcsv($csv_file, 65535, $separador, "'")) !== FALSE){
                        $serial = strClear($data[0]);
                        $letra = 'A';
                        if (strpos($serial, 'E+') !== false) {
                            $ct_err++;
                            $letra++;
                            $celda = $letra.$fila_csv;
                            $json_err[] = array(
                                'celda' => $celda,
                                'error' => 'Error de escritura [E+]'
                            );  
                        }
                        for($i_v=0; $i_v <= ($columnas-1) ; $i_v++){
                            if($i_v<2){
                                $celda = $letra.$fila_csv;
                                $letra++;
                                if(empty(trim($data[$i_v]))){
                                    $cont_err_va++;
                                    $arr_cel_vacio[] = array(
                                        'celda' => $celda,
                                        'error' => 'Celda Vacía'
                                    );
                                }
                            }
                        }
                        if(in_array($serial, $serial_arr)){
                            $serial_dupl[] = array(
                                'celda' => 'A'.$fila_csv,
                                'error' => 'Serial: '.$serial.' Esta Duplicado'
                            );
                        }
                        else if(array_search($serial, array_column($result, 'serial')) === false){
                            $conpos++;
                            $arr_ser_base[] = array(
                                'celda' => 'C'.$fila_csv,
                                'error' => 'Serial: '.$serial.' No Existe o No Esta Disponible'
                            );
                        }
                        else{
                            $pos_ser = array_search($serial, array_column($result, 'serial'));
                            $codigo = $result[$pos_ser]['codigo']; 
                            $cantidad = $result[$pos_ser]['cantidad']; 
                            $arr_guardar[] = array(
                                'celda' => 'A'.$fila_csv,
                                'serial' => $serial,
                                'codigo' => $codigo,
                                'cantidad' => $cantidad
                            ); 
                        }
                        $serial_arr[] = $serial;
                        $fila_csv++;
                    }       
                }
            }
            if($error == ''){
                $output = array(
                    'success' => true,
                    'errorduplic' => $serial_dupl,
                    'errores_vac' => $arr_cel_vacio,
                    'errores_Es' => $json_err,
                    'seriales_guar' => $arr_guardar,
                    'title'=> $result,
                    'nexi' => $arr_ser_base
                );
            }
            else{
                $output = array(
                    'success' => false, 
                    'msg' => $msgError, 
                    'error'=> $error,
                    'title'=> $eee,
                    'title_error' => $ttl_error
                );
            }
            echo json_encode($output, JSON_UNESCAPED_UNICODE);
        }
    }
    else if (isset($_POST['getSerial'])){
        $categoria = $_POST['getSerial'];
        $sql = $conn_inv->prepare("SELECT codigo, serial, cantidad, categoria, precio, odc FROM `control_inventario_base_seriales` WHERE categoria = '$categoria'");
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
    else if (isset($_POST['getCodCate'])){
        $categoria = $_POST['getCodCate'];
        $sql_cod = $conn_inv->prepare("SELECT codigo, descripcion, precio, medida, tipo, categoria, subcategoria, area FROM `control_inventario_base_codigos`  WHERE categoria = '$categoria'");
        $sql_cod->execute();
        $res_cod = $sql_cod->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($res_cod);
    }
    else if (isset($_POST['get_aliado'])){
        $categoria = $_POST['get_aliado'];
        $sql_cod = $conn_inv->prepare("SELECT id_aliado, nombre_aliado, nit FROM `base_aliados_traslados`  WHERE categoria = '$categoria'");
        $sql_cod->execute();
        $res_cod = $sql_cod->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($res_cod);
    }
    else if (isset($_POST['get_familia'])) {
        $sql = $conn_inv->prepare("SELECT parametros FROM `parametros_control_inventario`");
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        $arr_parametros = array();
        foreach ($result as $key => $value) {
            $parametros = json_decode($value['parametros']);
            $arr_parametros[] = array(
                'parametros' => $parametros
            );
        }
        
        echo json_encode($arr_parametros);
    
    }
    else if (isset($_POST['get_data_sal_bodega'])) {
        $datos = json_decode($_POST['get_data_sal_bodega']);
        $res_total = array();
        $sql_cod = $conn_inv->prepare("SELECT codigo, serial, movimiento, cantidad FROM `control_inventario_base_seriales`  WHERE categoria ='$datos->categoria' AND sede ='$datos->sede'");
        $sql_cod->execute();
        $res_cod = $sql_cod->fetchAll(PDO::FETCH_ASSOC);

        $sql_cod_n = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_baja`  WHERE  categoria ='$datos->categoria' AND sede ='$datos->sede'");
        $sql_cod_n->execute();
        $res_cod_n = $sql_cod_n->fetchAll(PDO::FETCH_ASSOC);

        $sql_cod_dev = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_devolucion`  WHERE categoria ='$datos->categoria' AND sede ='$datos->sede' AND movimiento = 'DEVOLUCION'");
        $sql_cod_dev->execute();
        $res_cod_dev = $sql_cod_dev->fetchAll(PDO::FETCH_ASSOC);

        $sql_cod_entra = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_entrada`  WHERE categoria ='$datos->categoria' AND sede ='$datos->sede'");
        $sql_cod_entra->execute();
        $res_cod_entra = $sql_cod_entra->fetchAll(PDO::FETCH_ASSOC);

        $sql_cod_entre = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_entrega`  WHERE categoria ='$datos->categoria' AND sede ='$datos->sede'");
        $sql_cod_entre->execute();
        $res_cod_entre = $sql_cod_entre->fetchAll(PDO::FETCH_ASSOC);
        
        if($res_cod != null){
            $res_total[] = $res_cod;
        }
        if($res_cod_n != null){
            $res_total[] = $res_cod_n;
        }
        if($res_cod_dev != null){
            $res_total[] = $res_cod_dev;
        }
        if($res_cod_entra != null){
            $res_total[] = $res_cod_entra;
        }
        if($res_cod_entre != null){
            $res_total[] = $res_cod_entre;
        }
        echo json_encode($res_total);
    }
    else if (isset($_POST['get_saldos'])) {
        $cedula = $_POST['get_saldos'];
        $datos = json_decode($_POST['get_data_sal']);
        $res_total = array();
        $sql_cod = $conn_inv->prepare("SELECT codigo, serial, movimiento, cantidad FROM `control_inventario_base_seriales`  WHERE cedula = '$cedula' AND categoria ='$datos->categoria' AND sede ='$datos->sede' AND movimiento = 'ENTREGA'");
        $sql_cod->execute();
        $res_cod = $sql_cod->fetchAll(PDO::FETCH_ASSOC);
        $sql_cod_n = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_entrega`  WHERE cedula = '$cedula' AND categoria ='$datos->categoria' AND sede ='$datos->sede'");
        $sql_cod_n->execute();
        $res_cod_n = $sql_cod_n->fetchAll(PDO::FETCH_ASSOC);
        $sql_cod_dev = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_devolucion`  WHERE cedula = '$cedula' AND categoria ='$datos->categoria' AND sede ='$datos->sede'");
        $sql_cod_dev->execute();
        $res_cod_dev = $sql_cod_dev->fetchAll(PDO::FETCH_ASSOC);
        if($cedula == '811042087'){
            $sql_cod_entra = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_entrada`  WHERE categoria ='$datos->categoria' AND sede ='$datos->sede'");
            $sql_cod_entra->execute();
            $res_cod_entra = $sql_cod_entra->fetchAll(PDO::FETCH_ASSOC);

            $sql_cod_ser = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_base_seriales`  WHERE categoria ='$datos->categoria' AND sede ='$datos->sede' AND movimiento = 'ENTRADA'");
            $sql_cod_ser->execute();
            $res_cod_ser = $sql_cod_ser->fetchAll(PDO::FETCH_ASSOC);
            if($res_cod_ser != null){
                $res_total[] = $res_cod_ser;
            }
            if($res_cod_entra != null){
                $res_total[] = $res_cod_entra;
            }
        }
        if($res_cod != null){
            $res_total[] = $res_cod;
        }
        if($res_cod_n != null){
            $res_total[] = $res_cod_n;
        }
        if($res_cod_dev != null){
            $res_total[] = $res_cod_dev;
        }
        echo json_encode($res_total);
    }
    else if (isset($_POST['saveCodTabla'])) {
        $datos = json_decode($_POST['saveCodTabla']);
        $edit = $_SESSION['cedula'];
        $sede = $datos->sede;
        $error = '';
        $msgError = '';
        $sql_perm = $conn_inv->prepare("SELECT parametros FROM `parametros_control_inventario` WHERE categoria = '$datos->categoria'");
        $sql_perm->execute();
        $error_s = $sql_perm->errorInfo();
        $result_perm = $sql_perm->fetch(PDO::FETCH_ASSOC);
        $permi = json_decode($result_perm['parametros']);
        $permi_sedes = array_search($sede, array_column($permi->sedes, 'sede'));
        $permi_admin = in_array($_SESSION['cedula'], $permi->sedes[$permi_sedes]->permisos->admin);
        $permi_edit = in_array($edit, $permi->sedes[$permi_sedes]->permisos->editor);
        if(!$permi_admin  && !$permi_edit ){
            $msgError = "No Tiene Permisos para Editar";
            $error = 'Permiso Edición';
            $output = array(
                'success' => false, 
                'msg' => 'No se pudo guardar la información',
                'msg_err' => $msgError
            );
        }else{
            $sql = $conn_inv->prepare("SELECT id, codigo, precio, tipo, categoria, historial_precios FROM `control_inventario_base_codigos`  WHERE categoria = '$datos->categoria'");
            $sql->execute();
            $sql_error_sele = $sql->errorInfo();
            $sql_count_sele = $sql->rowCount();
            $arr_resul_cod = $sql->fetchAll(PDO::FETCH_ASSOC);

            if(strtoupper($datos->movimiento) == "ENTRADA"){
                $cedula = 811042087;
                $sql_insert_cod_serial = 'INSERT INTO control_inventario_base_seriales (fecha, codigo, serial, cedula, movimiento, cantidad, ticket, sede, categoria, sub_categoria, precio, odc, observaciones, historial, edit, fecha_edit) VALUES ';
                $sqlMarks_serial = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $marks_serial = [];
                $values_serial = [];
                $sql_insert_cod = 'INSERT INTO control_inventario_movimientos_entrada (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, odc, serial, cantidad, precio, observaciones, edit, fecha_edit) VALUES ';
                $sqlMarks = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $marks = [];
                $values = [];
                $serial_arr = array();
                $arr_ser_base = array();
                $save_ok = true;
                $count_ser = 0;
                $count_nor = 0;
                $count_up = 0;
                $ids = array();
                $updateParams = array();

                foreach($datos->datosTabla as $value){
                    $codigo = $value->codigo;
                    $cantidad = $value->cantidad;
                    $precio = $value->precio;
                    $serial = $value->serial;
                    $odc = $value->odc;
                    $observaciones = $value->observaciones;
                    $observaciones = preg_replace("/[\r\n|\n|\r]+/", " ", $observaciones);
                    $pos_codigo = array_search($codigo, array_column($arr_resul_cod, 'codigo'));
                    $precio_base = $arr_resul_cod[$pos_codigo]['precio'];
                    $tipo= $arr_resul_cod[$pos_codigo]['tipo'];
                    if($tipo == "NORMAL"){
                        $marks[] = $sqlMarks;
                        array_push($values, 
                            $datos->fecha, 
                            $datos->ticket, 
                            $codigo, 
                            811042087, 
                            strtoupper($datos->movimiento), 
                            strtoupper($datos->categoria), 
                            strtoupper($datos->sub_cate), 
                            strtoupper($datos->sede), 
                            $odc, 
                            $serial, 
                            $cantidad, 
                            $precio, 
                            $observaciones,
                            $edit, 
                            $fechahoy
                        );
                        $count_nor++;
                    }
                    else{
                        $fecha_for = ''; 
                        $fecha_for = $datos->fecha.' '.date('H:i:s');
                        $historial_ser = array(
                            array(
                                "fecha" => $fechahoy,
                                "fecha_edit" => $fecha_for, 
                                "movimiento" => strtoupper($datos->movimiento), 
                                "ticket" => $datos->ticket, 
                                "edit" => $edit, 
                                "codigo" => $codigo,
                                "origen" => url(), 
                                "cedula" => 811042087, 
                                "serial" => $serial, 
                                "precio" => $precio,
                                "sede" => $datos->sede,
                                "observaciones" => $observaciones
                            )
                        ); 
                        $marks_serial[] = $sqlMarks_serial;
                    
                        array_push($values_serial, 
                            $datos->fecha, 
                            $codigo, 
                            $serial, 
                            811042087, 
                            strtoupper($datos->movimiento),
                            $cantidad, 
                            $datos->ticket, 
                            strtoupper($datos->sede), 
                            strtoupper($datos->categoria), 
                            strtoupper($datos->sub_cate), 
                            $precio, 
                            $odc, 
                            $observaciones, 
                            json_encode($historial_ser, JSON_UNESCAPED_UNICODE), 
                            $edit, 
                            $fechahoy
                        );
                        $count_ser++;
                    }
                    if($precio_base != $precio){
                        $id = $arr_resul_cod[$pos_codigo]['id'];
                        $ids[] = intval($id);
                        $arrnew = array(
                            'fecha'=>$fechahoy,
                            'precio' => $precio
                        );
                        $histo_base = $arr_resul_cod[$pos_codigo]['historial_precios'];
                        $contentsDecoded = json_decode($histo_base, true);
                        $contentsDecoded[] = $arrnew; 
                        $historial = json_encode($contentsDecoded, JSON_UNESCAPED_UNICODE);
                        $updateParams['precio'][$id] = $precio;
                        $updateParams['historial_precios'][$id] = $historial;
                        $updateParams['edit'][$id] = $edit;
                        $updateParams['fecha_edit'][$id] = $fechahoy;
                        $count_up++;
                    } 
                }
                if($count_up !=0){
                    $data_qry_upd = createQryUpdate('control_inventario_base_codigos', 'id', $ids, $updateParams);
                    $qry_exe = $conn_inv->prepare($data_qry_upd);
                    $exe_up = $qry_exe->execute();
                }
                if($count_ser != 0){
                    $sql_insert_cod_serial = $conn_inv->prepare($sql_insert_cod_serial . implode(',', $marks_serial));
                    $exe_ser = $sql_insert_cod_serial->execute($values_serial);
                    $sql_error_ins_ser = $sql_insert_cod_serial->errorInfo();
                    $sql_count_ins_ser = $sql_insert_cod_serial->rowCount();
                }
                if( $count_nor != 0){
                    $sql_insert_cod= $conn_inv->prepare($sql_insert_cod . implode(',', $marks));
                    $exe = $sql_insert_cod->execute($values);
                    $sql_error_ins = $sql_insert_cod->errorInfo();
                    $sql_count_ins = $sql_insert_cod->rowCount();
                }
                if($exe || $exe_ser || $exe_up){
                    $output = array(
                        'success' => true, 
                        'msg' => 'Se guardo correctamente.', 
                        'info' => $values_serial, 
                        'info2'=> $historial_ser, 
                        'info2'=>$histo_base, 
                        'info3'=>$exe_up
                    );
                }else{
                    $output = array(
                        'success' => false, 
                        'msg' => 'No se pudo guardar la información', 
                        'info2'=>$sql_error_ins, 
                        'info3'=> $codigo,
                        'info' => $count_ser.$count_nor.$tipo,
                        'info4' => $data_qry_upd,
                        'msg_err' => $msgError
                    );
                }
            }
            else if(strtoupper($datos->movimiento) == "ENTREGA"){
                $sql_ser = $conn_inv->prepare("SELECT id, codigo, serial, movimiento, historial FROM `control_inventario_base_seriales`  WHERE categoria = '$datos->categoria'");
                $sql_ser->execute();
                $sql_error_ser = $sql_ser->errorInfo();
                $sql_count_ser = $sql_ser->rowCount();
                $arr_resul_ser = $sql_ser->fetchAll(PDO::FETCH_ASSOC);
                $sql_insert_cod = 'INSERT INTO control_inventario_movimientos_entrega (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, odc, cantidad, precio, observaciones, edit, fecha_edit) VALUES ';
                $sqlMarks = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $marks = [];
                $values = [];
                $save_ok = true;
                $count_ser = 0;
                $count_nor = 0;
                
                $ids = array();
                $updateParams = array();
                
                foreach($datos->datosTabla as $value){
                    $codigo = $value->codigo;
                    $cantidad = $value->cantidad;
                    $precio = $value->precio;
                    $odc = $value->odc;
                    $observaciones = $value->observaciones;
                    $observaciones = preg_replace("/[\r\n|\n|\r]+/", " ", $observaciones);
                    $pos_codigo = array_search($codigo, array_column($arr_resul_cod, 'codigo'));
                    $tipo= $arr_resul_cod[$pos_codigo]['tipo'];

                    if($tipo == "NORMAL"){
                        $marks[] = $sqlMarks;
                        array_push($values, 
                            $datos->fecha, 
                            $datos->ticket, 
                            $codigo, 
                            $datos->cedula, 
                            strtoupper($datos->movimiento), 
                            strtoupper($datos->categoria), 
                            strtoupper($datos->sub_cate), 
                            strtoupper($datos->sede), 
                            $odc,  
                            $cantidad, 
                            $precio, 
                            $observaciones,
                            $edit, 
                            $fechahoy
                        );
                        $count_nor++;
                    }
                    else{ 
                        $fecha_for = ''; 
                        $fecha_for = $datos->fecha.' '.date('H:i:s');
                        $historial_ser = array(
                            "fecha" => $fechahoy,
                            "fecha_edit" => $fecha_for, 
                            "movimiento" => strtoupper($datos->movimiento), 
                            "ticket" => $datos->ticket, 
                            "edit" => $edit, 
                            "codigo" => $codigo,
                            "origen" => url(), 
                            "cedula" =>  $datos->cedula, 
                            "serial" => $value->serial, 
                            "precio" => $precio,
                            "sede" => $datos->sede,
                            "observaciones" => $observaciones
                        ); 

                        $pos_codigo_ser = array_search($value->serial, array_column($arr_resul_ser, 'serial'));
                        $id = $arr_resul_ser[$pos_codigo_ser]['id'];
                        $ids[] = intval($id);
                        $histo_base = $arr_resul_ser[$pos_codigo_ser]['historial'];
                        $contentsDecoded = json_decode($histo_base, true);
                        $contentsDecoded[] = json_decode(json_encode($historial_ser, JSON_UNESCAPED_UNICODE)); 
                        $historial = json_encode($contentsDecoded);

                        $updateParams['fecha'][$id] = $datos->fecha;
                        $updateParams['movimiento'][$id] = strtoupper($datos->movimiento);
                        $updateParams['cedula'][$id] = $datos->cedula;
                        $updateParams['ticket'][$id] = $datos->ticket;
                        $updateParams['historial'][$id] = $historial;
                        $updateParams['edit'][$id] = $edit;
                        $updateParams['fecha_edit'][$id] = $fechahoy;
                        $count_ser++;
                    }
                }
                if($count_ser > 0) {
                    $data_qry_upd = createQryUpdate('control_inventario_base_seriales', 'id', $ids, $updateParams);
                    $qry_exe = $conn_inv->prepare($data_qry_upd);
                    $exe_ser = $qry_exe->execute();
                    $error = $qry_exe->errorInfo();
                }
                if( $count_nor != 0){
                    $sql_insert_cod= $conn_inv->prepare($sql_insert_cod . implode(',', $marks));
                    $exe = $sql_insert_cod->execute($values);
                    $sql_error_ins = $sql_insert_cod->errorInfo();
                    $sql_count_ins = $sql_insert_cod->rowCount();
                }

                if($exe || $exe_ser){
                    $output = array(
                        'success' => true, 
                        'msg' => 'Se guardo correctamente.', 
                        'info' => $sql_error_ser,
                        'info2' => $sql_insert_cod,
                        'info3' => $sql_up_cod,
                        // 'info5' => $qry_exe->errorInfo(),
                        'info4' => $sql_error_up,
                        'ids' => $ids,
                        'param' => $updateParams
                    );
                }else{
                    $output = array(
                        'success' => false, 
                        'msg' => 'No se pudo guardar la información', 
                        'info' => $exe_ser,
                        'info2' => $qry_exe,
                        'info3' => $count_ser,
                        // 'info4' => $qry_exe->errorInfo(),
                        'msg_err' => $error,
                        'ids' => $ids,
                        'param' => $updateParams
                    );
                }
            }
            else if(strtoupper($datos->movimiento) == "DEVOLUCION"){
                $sql_ser = $conn_inv->prepare("SELECT id, codigo, serial, cedula, movimiento, historial FROM `control_inventario_base_seriales`  WHERE categoria = '$datos->categoria'");
                $sql_ser->execute();
                $sql_error_ser = $sql_ser->errorInfo();
                $sql_count_ser = $sql_ser->rowCount();
                $arr_resul_ser = $sql_ser->fetchAll(PDO::FETCH_ASSOC);
                $sql_insert_cod = 'INSERT INTO control_inventario_movimientos_devolucion (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, odc, serial, cantidad, precio, observaciones, edit, fecha_edit) VALUES ';
                $sqlMarks = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $marks = [];
                $values = [];
                $sql_insert_cod_e = 'INSERT INTO control_inventario_movimientos_entrada (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, odc, serial, cantidad, precio, observaciones, edit, fecha_edit) VALUES ';
                $sqlMarks_e = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $marks_e = [];
                $values_e = [];
                $save_ok = true;
                $count_ser = 0;
                $count_nor = 0;
                $ids = array();
                $updateParams = array();

                foreach($datos->datosTabla as $value){
                    $codigo = $value->codigo;
                    $cantidad = $value->cantidad;
                    $precio = $value->precio;
                    $serial = $value->serial;
                    $odc = $value->odc;
                    $observaciones = $value->observaciones;
                    $observaciones = preg_replace("/[\r\n|\n|\r]+/", " ", $observaciones);
                    $pos_codigo = array_search($codigo, array_column($arr_resul_cod, 'codigo'));
                    $tipo= $arr_resul_cod[$pos_codigo]['tipo'];
                    if($tipo == "NORMAL"){
                        $marks[] = $sqlMarks;
                        array_push($values, 
                            $datos->fecha, 
                            $datos->ticket, 
                            $codigo, 
                            $datos->cedula, 
                            strtoupper($datos->movimiento), 
                            strtoupper($datos->categoria), 
                            strtoupper($datos->sub_cate), 
                            strtoupper($datos->sede), 
                            $odc, 
                            $serial, 
                            $cantidad, 
                            $precio, 
                            $observaciones,
                            $edit, 
                            $fechahoy
                        );
                        $marks_e[] = $sqlMarks_e;
                        array_push($values_e, 
                            $datos->fecha, 
                            $datos->ticket, 
                            $codigo, 
                            811042087, 
                            strtoupper($datos->movimiento), 
                            strtoupper($datos->categoria), 
                            strtoupper($datos->sub_cate), 
                            strtoupper($datos->sede), 
                            $odc, 
                            $serial, 
                            $cantidad, 
                            $precio, 
                            $observaciones,
                            $edit, 
                            $fechahoy
                        );
                        $count_nor++;
                    }
                    else{ 
                        $fecha_for = ''; 
                        $fecha_for = $datos->fecha.' '.date('H:i:s');
                        $historial_ser = array(
                            "fecha" => $fechahoy,
                            "fecha_edit" => $fecha_for, 
                            "movimiento" => strtoupper($datos->movimiento), 
                            "ticket" => $datos->ticket, 
                            "edit" => $edit, 
                            "codigo" => $codigo,
                            "origen" => url(), 
                            "cedula" => $datos->cedula, 
                            "serial" => $serial, 
                            "precio" => $precio,
                            "sede" => $datos->sede,
                            "observaciones" => $observaciones
                        ); 
                        $pos_codigo_ser = array_search($serial, array_column($arr_resul_ser, 'serial'));
                        $id = $arr_resul_ser[$pos_codigo_ser]['id'];
                        $ids[] = intval($id);
                        $histo_base = $arr_resul_ser[$pos_codigo_ser]['historial'];
                        $contentsDecoded = json_decode($histo_base, true);
                        $contentsDecoded[] = $historial_ser; 
                        $historial = json_encode($contentsDecoded, JSON_UNESCAPED_UNICODE);

                        $updateParams['fecha'][$id] = $datos->fecha;
                        $updateParams['cedula'][$id] = 811042087;
                        $updateParams['movimiento'][$id] = strtoupper($datos->movimiento);
                        $updateParams['historial'][$id] = $historial;
                        $updateParams['edit'][$id] = $edit;
                        $updateParams['fecha_edit'][$id] = $fechahoy;
                        $count_ser++;
                    }
                }
                if($count_ser != 0){
                    $data_qry_upd = createQryUpdate('control_inventario_base_seriales', 'id', $ids, $updateParams);
                    $qry_exe = $conn_inv->prepare($data_qry_upd);
                    $exe_ser = $qry_exe->execute();
                }
                if( $count_nor != 0){
                    $sql_insert_cod= $conn_inv->prepare($sql_insert_cod . implode(',', $marks));
                    $exe = $sql_insert_cod->execute($values);
                    $sql_insert_cod_e= $conn_inv->prepare($sql_insert_cod_e . implode(',', $marks_e));
                    $exe_e = $sql_insert_cod_e->execute($values_e);
                    $sql_error_ins = $sql_insert_cod->errorInfo();
                    $sql_count_ins = $sql_insert_cod->rowCount();
                }
                if($exe || $exe_ser){
                    $output = array(
                        'success' => true, 
                        'msg' => 'Se guardo correctamente.', 
                        'info' => $sql_error_ser,
                        'info2' => $sql_insert_cod,
                        'info3' => $sql_up_cod,
                        'info4' => $sql_error_up
                    );
                }else{
                    $output = array(
                        'success' => false, 
                        'msg' => 'No se pudo guardar la información', 
                        'info' => $sql_error_ser,
                        'info2' => $sql_insert_cod,
                        'info3' => $sql_up_cod,
                        'info4' => $sql_error_up,
                        'msg_err' => $msgError
                    );
                }
            }
            else if(strtoupper($datos->movimiento) == "BAJA"){
                $sql_ser = $conn_inv->prepare("SELECT id, codigo, serial, movimiento, historial FROM `control_inventario_base_seriales`  WHERE categoria = '$datos->categoria'");
                $sql_ser->execute();
                $sql_error_ser = $sql_ser->errorInfo();
                $sql_count_ser = $sql_ser->rowCount();
                $arr_resul_ser = $sql_ser->fetchAll(PDO::FETCH_ASSOC);
                $sql_insert_cod = 'INSERT INTO control_inventario_movimientos_baja (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, odc, serial, cantidad, precio, observaciones, edit, fecha_edit) VALUES ';
                $sqlMarks = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $marks = [];
                $values = [];
                $sql_insert_ser = 'INSERT INTO control_inventario_base_seriales_baja (fecha, codigo, serial, cedula, movimiento, cantidad, ticket, sede, categoria, sub_categoria, precio, odc,  observaciones, historial, edit, fecha_edit) VALUES ';
                $sqlMarks_ser = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $marks_ser = [];
                $values_ser = [];

                $sql_insert_cod_dev = 'INSERT INTO control_inventario_movimientos_devolucion (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, odc, serial, cantidad, precio, observaciones, edit, fecha_edit) VALUES ';
                $sqlMarks_dev = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $marks_dev = [];
                $values_dev = [];

                $save_ok = true;
                $count_ser = 0;
                $count_nor = 0;
                $ids = array();
                $updateParams = array();

                foreach($datos->datosTabla as $value){
                    $codigo = $value->codigo;
                    $cantidad = $value->cantidad;
                    $precio = $value->precio;
                    $serial = $value->serial;
                    $odc = $value->odc;
                    $observaciones = $value->observaciones;
                    $observaciones = preg_replace("/[\r\n|\n|\r]+/", " ", $observaciones);
                    $pos_codigo = array_search($codigo, array_column($arr_resul_cod, 'codigo'));
                    $tipo= $arr_resul_cod[$pos_codigo]['tipo'];
                    if($tipo == "NORMAL"){
                        $marks[] = $sqlMarks;
                        array_push($values, 
                            $datos->fecha, 
                            $datos->ticket, 
                            $codigo, 
                            $datos->cedula, 
                            strtoupper($datos->movimiento), 
                            strtoupper($datos->categoria), 
                            strtoupper($datos->sub_cate), 
                            strtoupper($datos->sede), 
                            $odc, 
                            $serial, 
                            $cantidad, 
                            $precio, 
                            $observaciones,
                            $edit, 
                            $fechahoy
                        );
                        $marks_dev[] = $sqlMarks_dev;
                        array_push($values_dev, 
                            $datos->fecha, 
                            $datos->ticket, 
                            $codigo, 
                            $datos->cedula, 
                            'DEVOLUCION', 
                            strtoupper($datos->categoria), 
                            strtoupper($datos->sub_cate), 
                            strtoupper($datos->sede), 
                            $odc, 
                            $serial, 
                            $cantidad, 
                            $precio, 
                            $observaciones,
                            $edit, 
                            $fechahoy
                        );
                        $count_nor++;
                    }
                    else{ 
                        $fecha_for = ''; 
                        $fecha_for = $datos->fecha.' '.date('H:i:s');
                        $historial_ser = array(
                            "fecha" => $fechahoy,
                            "fecha_edit" => $fecha_for, 
                            "movimiento" => strtoupper($datos->movimiento), 
                            "ticket" => $datos->ticket, 
                            "edit" => $edit, 
                            "codigo" => $codigo,
                            "origen" => url(), 
                            "cedula" => $datos->cedula, 
                            "serial" => $serial, 
                            "precio" => $precio,
                            "sede" => $datos->sede,
                            "observaciones" => $observaciones
                        ); 
                        $pos_codigo_ser = array_search($serial, array_column($arr_resul_ser, 'serial'));
                        $id = $arr_resul_ser[$pos_codigo_ser]['id'];
                        $ids[] = intval($id);
                        $histo_base = $arr_resul_ser[$pos_codigo_ser]['historial'];
                        $contentsDecoded = json_decode($histo_base, true);
                        $contentsDecoded[] = $historial_ser; 
                        $historial = json_encode($contentsDecoded, JSON_UNESCAPED_UNICODE);

                        $updateParams['fecha'][$id] = $datos->fecha;
                        $updateParams['cedula'][$id] = 811042087;
                        $updateParams['movimiento'][$id] = strtoupper($datos->movimiento);
                        $updateParams['observaciones'][$id] = $observaciones;
                        $updateParams['historial'][$id] = $historial;
                        $updateParams['edit'][$id] = $edit;
                        $updateParams['fecha_edit'][$id] = $fechahoy;
                        $marks_ser[] = $sqlMarks_ser;
                        array_push($values_ser, 
                            $datos->fecha, 
                            $codigo, 
                            $serial, 
                            $datos->cedula, 
                            strtoupper($datos->movimiento), 
                            $cantidad, 
                            $datos->ticket, 
                            strtoupper($datos->sede), 
                            strtoupper($datos->categoria), 
                            strtoupper($datos->sub_cate), 
                            $precio, 
                            $odc, 
                            $observaciones,
                            $historial,
                            $edit, 
                            $fechahoy
                        );
                        $count_ser++;
                    }
                }
                if( $count_ser != 0){
                    $data_qry_upd = createQryUpdate('control_inventario_base_seriales', 'id', $ids, $updateParams);
                    $qry_exe = $conn_inv->prepare($data_qry_upd);
                    $exe_up = $qry_exe->execute();

                    $sql_insert_ser= $conn_inv->prepare($sql_insert_ser . implode(',', $marks_ser));
                    $exe_ser = $sql_insert_ser->execute($values_ser);
                    $sql_error_ins_ser = $sql_insert_ser->errorInfo();
                    $sql_count_ins_ser = $sql_insert_ser->rowCount();
                }
                if( $count_nor != 0){
                    $sql_insert_cod= $conn_inv->prepare($sql_insert_cod . implode(',', $marks));
                    $exe = $sql_insert_cod->execute($values);
                    $sql_insert_cod_dev= $conn_inv->prepare($sql_insert_cod_dev . implode(',', $marks_dev));
                    $exe_dev = $sql_insert_cod_dev->execute($values_dev);
                    $sql_error_ins = $sql_insert_cod->errorInfo();
                    $sql_count_ins = $sql_insert_cod->rowCount();
                }
                if($exe || $exe_ser || $exe_up){
                    $output = array(
                        'success' => true, 
                        'msg' => 'Se guardo correctamente.', 
                        'info' => $sql_error_ser,
                        'info2' => $sql_insert_cod,
                        'info3' => $sql_up_cod,
                        'info4' => $sql_error_up,
                        'info5' => $sql_insert_ser
                    );
                }else{
                    $output = array(
                        'success' => false, 
                        'msg' => 'No se pudo guardar la información', 
                        'info' => $sql_error_ser,
                        'info2' => $sql_insert_cod,
                        'info3' => $sql_up_cod,
                        'info4' => $sql_error_up,
                        'msg_err' => $msgError,
                        'info5' => $sql_insert_ser
                    );
                }
            }
        }
        
        echo json_encode($output);
    }
    else if (isset($_POST['save_aliado'])) {
        $datos = json_decode($_POST['save_aliado']);
        $edit = $_SESSION['cedula'];
        $msgexit = 'Se Guardó Correctamente.';
        $msg1 = '';
        $categoria = $datos->categoria;
        $id_aliado = $datos->nit.$categoria;
        $sql_val = $conn_inv->prepare("SELECT id, nit, categoria FROM base_aliados_traslados WHERE id_aliado = '$id_aliado'");
        $exe_sql = $sql_val->execute();
        $res = $sql_val->fetch(PDO::FETCH_ASSOC);
        if($res == null){
            $msg1 = 'No se Pudo Guadar la Información';
            $sql = $conn_inv->prepare("INSERT INTO base_aliados_traslados (id_aliado, nombre_aliado, nit, nombre_contacto, telefono_contacto, correo_contacto, categoria, direccion, ciudad, edit, fecha_edit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $dataSave = array(
                $id_aliado,
                $datos->nom_aliado,
                $datos->nit,
                $datos->nom_contact,
                $datos->tel,
                $datos->email,
                $categoria,
                $datos->direccion,
                $datos->ciudad,
                $edit,
                $fechahoy
            );
            $exe = $sql->execute($dataSave);
            $error = $sql->errorInfo();
        }
        else{
            $msg1 = 'Ya Existe Para la Categoria';
        };
        if($exe){
            $output = array('success' => true, 'msg' => $msgexit, 'info' => $error, 'info2'=> $res, 'info3' => $datos->nit);
        }
        else{
            $output = array('success' => false, 'msg' => $msg1, 'info' => $error, 'info2'=> $res, 'info3' => $datos->nit);
        }
    
        echo json_encode($output);
    }
    else if (isset($_POST['save_familia'])) {
        $datos = json_decode($_POST['save_familia']);
        $edit = $_SESSION['cedula'];
        $msgexit = 'Se Guardó Correctamente.';
        $categoria = $datos->categoria;
        $permisos = array();
        $sql_val_fam = $conn_inv->prepare("SELECT id, parametros, permisos FROM parametros_control_inventario WHERE categoria = '$datos->categoria' ");
        $exe = $sql_val_fam->execute();
        $res = $sql_val_fam->fetch(PDO::FETCH_ASSOC);
        
        if($res != null) {
            $permiB = json_decode($res['permisos']);
            $params = json_decode($res['parametros']);
            $sed1 = $params->sedes;
            $sql = $conn_inv->prepare("UPDATE parametros_control_inventario SET parametros = ?, permisos = ?, edit = ?, fecha_edit = ? WHERE id = ?");
            $sedes = $datos->sedes;
            $arr_sed = array();
            $arr_content = array();
            $permisos = array(
                'admin' => [$edit],
                'editor' => [],
                'lector' => []
            );
            // foreach($sed1 as $val){
            //     $arr_content[] = $val->sede;
            //     $arr_sed[] = $val->sede;

            // }
            // foreach($sedes as $sed){
            //     if(!in_array($sed->sede, $arr_content)){
            //         $arr_sed[] = array (
            //             'sede'=> $sed->sede,
            //             'permisos' => $permisos
            //         );
            //     }
            // }
            // $datos->sedes = $arr_sed;
            $dataSave = array(
                json_encode($datos),
                json_encode($permiB),
                $edit,
                $fechahoy,
                $res['id']
            );
            $msgexit = 'Se Actualizó Correctamente.';
        }else{
            $sql = $conn_inv->prepare("INSERT INTO parametros_control_inventario (categoria, parametros, permisos, edit, fecha_edit) VALUES (?, ?, ?, ?, ?)");
            
            $sedes = $datos->sedes;
            $arr_sed = array();
            $permisos = array(
                        'admin' => [$edit],
                        'editor' => [],
                        'lector' => []
                );
            foreach($sedes as $sed){
                
                $arr_sed[] = array (
                    'sede'=> $sed->sede,
                    'permisos' => $permisos
                );
            }
            // $datos[] = $arr_sed; 
            // $datos = json_encode($datos, JSON_UNESCAPED_UNICODE);
            $datos->sedes = $arr_sed;
            $datos->permisos = $permisos;
            $dataSave = array(
                $categoria,
                json_encode($datos),
                json_encode($arr_sed),
                $edit,
                $fechahoy
            );
        }
    
        $exe = $sql->execute($dataSave);
        $error = $sql->errorInfo();
    
        if($exe){
            $output = array('success' => true, 'msg' => $msgexit, 'info' => $error, 'info2' => $arr_content);
        }
        else{
            $output = array('success' => false, 'msg' => 'No se pudo guardar la información', 'info' => $error);
        }
    
        echo json_encode($output);
    }
    else if (isset($_POST['save_permi'])) {
        $datos = json_decode($_POST['save_permi']);
        $edit = $_SESSION['cedula'];
        $msgexit = 'Se Actualizó Correctamente.';
        $sql_val_fam = $conn_inv->prepare("SELECT id, parametros FROM parametros_control_inventario WHERE categoria = '$datos->categoria' ");
        $exe = $sql_val_fam->execute();
        $res = $sql_val_fam->fetch(PDO::FETCH_ASSOC);
        $sql = $conn_inv->prepare("UPDATE parametros_control_inventario SET parametros = ?, edit = ?, fecha_edit = ? WHERE id = ?");
        $dataSave = array(
            json_encode($datos),
            $edit,
            $fechahoy,
            $res['id']
        );
        $exe = $sql->execute($dataSave);
        $error = $sql->errorInfo();
        if($exe){
            $output = array('success' => true, 'msg' => $msgexit, 'info' => $error);
        }
        else{
            $output = array('success' => false, 'msg' => 'No se pudo guardar la información', 'info' => $error);
        }
    
        echo json_encode($output);
    }
    else if (isset($_POST['save_sal'])){
        $datos = json_decode($_POST['save_sal']);
        $datos_sal = json_decode($_POST['data_save_sal']);
        $edit = $_SESSION['cedula'];
        $sql_perm = $conn_inv->prepare("SELECT parametros FROM `parametros_control_inventario` WHERE categoria = '$datos->categoria'");
        $sql_perm->execute();
        $error_s = $sql_perm->errorInfo();
        $result_perm = $sql_perm->fetch(PDO::FETCH_ASSOC);
        $permi = json_decode($result_perm['parametros']);
        $permi_sedes = array_search($sede, array_column($permi->sedes, 'sede'));
        $permi_admin = in_array($_SESSION['cedula'], $permi->sedes[$permi_sedes]->permisos->admin);
        $permi_edit = in_array($edit, $permi->sedes[$permi_sedes]->permisos->editor);
        if(!$permi_admin  && !$permi_edit ){
            $msgError = "No Tiene Permisos para Editar";
            $error = 'Permiso Edición';
        }
        else{
            $sql_insert_entra = $conn_inv->prepare ("INSERT INTO control_inventario_movimientos_entrada (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, cantidad, observaciones, edit, fecha_edit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $sql_insert_cod = $conn_inv->prepare ("INSERT INTO control_inventario_movimientos_baja (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, cantidad, observaciones, edit, fecha_edit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $sql_insert_cod_c = $conn_inv->prepare ("INSERT INTO control_inventario_movimientos_baja (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, cantidad, observaciones, edit, fecha_edit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $sql_insert_cod_dev_n = $conn_inv->prepare ("INSERT INTO control_inventario_movimientos_devolucion (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, cantidad, observaciones, edit, fecha_edit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $sql_insert_cod_dev_b = $conn_inv->prepare ("INSERT INTO control_inventario_movimientos_devolucion (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, cantidad, observaciones, edit, fecha_edit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $sql_insert_cod_dev_c = $conn_inv->prepare ("INSERT INTO control_inventario_movimientos_devolucion (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, cantidad, observaciones, edit, fecha_edit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $sql_ser = $conn_inv->prepare("SELECT id, codigo, serial, cedula, movimiento, historial FROM `control_inventario_base_seriales`  WHERE categoria = '$datos->categoria' AND serial = '$datos_sal->serial'");
            $sql_ser->execute();
            $sql_error_ser = $sql_ser->errorInfo();
            $sql_count_ser = $sql_ser->rowCount();
            $arr_resul_ser = $sql_ser->fetch(PDO::FETCH_ASSOC);

            if($datos_sal->serial == '' && $datos_sal->cobro != ''){
                $dataSave1c = array(
                    $datos->fecha, 
                    $datos->ticket, 
                    $datos_sal->codigo, 
                    $datos->cedula, 
                    "COBRO", 
                    strtoupper($datos->categoria), 
                    strtoupper($datos->sub_cate), 
                    strtoupper($datos->sede), 
                    $datos_sal->cobro, 
                    $datos_sal->observaciones,
                    $edit, 
                    $fechahoy
                );
                $dataSavec = array(
                    $datos->fecha, 
                    $datos->ticket, 
                    $datos_sal->codigo, 
                    $datos->cedula, 
                    "DEVOLUCION", 
                    strtoupper($datos->categoria), 
                    strtoupper($datos->sub_cate), 
                    strtoupper($datos->sede), 
                    $datos_sal->cobro, 
                    $datos_sal->observaciones,
                    $edit, 
                    $fechahoy
                );
                $exe_c = $sql_insert_cod_c->execute($dataSave1c);
                $error_c = $sql_insert_cod_c->errorInfo();
                $exe_dev_n = $sql_insert_cod_dev_c->execute($dataSavec);
            }
            if($datos_sal->serial == '' && $datos_sal->baja != ''){
                $dataSave1b = array(
                    $datos->fecha, 
                    $datos->ticket, 
                    $datos_sal->codigo, 
                    $datos->cedula, 
                    "BAJA", 
                    strtoupper($datos->categoria), 
                    strtoupper($datos->sub_cate), 
                    strtoupper($datos->sede), 
                    $datos_sal->baja, 
                    $datos_sal->observaciones,
                    $edit, 
                    $fechahoy
                );
                $dataSaveb = array(
                    $datos->fecha, 
                    $datos->ticket, 
                    $datos_sal->codigo, 
                    $datos->cedula, 
                    "DEVOLUCION", 
                    strtoupper($datos->categoria), 
                    strtoupper($datos->sub_cate), 
                    strtoupper($datos->sede), 
                    $datos_sal->baja, 
                    $datos_sal->observaciones,
                    $edit, 
                    $fechahoy
                );
                $exe = $sql_insert_cod->execute($dataSave1b);
                $error = $sql_insert_cod->errorInfo();
                $exe_dev_n = $sql_insert_cod_dev_b->execute($dataSaveb);
                
            }
            if ($datos_sal->serial == '' && $datos_sal->devolucion != ''){
                $dataSave = array(
                    $datos->fecha, 
                    $datos->ticket, 
                    $datos_sal->codigo, 
                    $datos->cedula, 
                    "DEVOLUCION", 
                    strtoupper($datos->categoria), 
                    strtoupper($datos->sub_cate), 
                    strtoupper($datos->sede), 
                    $datos_sal->devolucion, 
                    $datos_sal->observaciones,
                    $edit, 
                    $fechahoy
                );
                $datasave1 = array(
                    $datos->fecha, 
                    $datos->ticket, 
                    $datos_sal->codigo, 
                    811042087, 
                    "ENTRADA", 
                    strtoupper($datos->categoria), 
                    strtoupper($datos->sub_cate), 
                    strtoupper($datos->sede), 
                    $datos_sal->devolucion, 
                    $datos_sal->observaciones,
                    $edit, 
                    $fechahoy
                );
                $exe_dev_n = $sql_insert_cod_dev_n->execute($dataSave);
                $exe_entra = $sql_insert_entra->execute($datasave1);
                $error = $sql_insert_cod_dev_n->errorInfo();
            }
            if($datos_sal->serial != '' && $datos_sal->devolucion != ''){
                $sql = $conn_inv->prepare("UPDATE control_inventario_base_seriales SET cedula = ?, movimiento = ?, observaciones = ?, historial = ?, edit = ?, fecha_edit = ? WHERE id = ?");
                $fecha_for = ''; 
                $fecha_for = $datos->fecha.' '.date('H:i:s');
                $observaciones = $datos_sal->observaciones;
                $observaciones = preg_replace("/[\r\n|\n|\r]+/", " ", $observaciones);
                $historial_ser = array(
                    "fecha" => $fechahoy,
                    "fecha_edit" => $fecha_for, 
                    "movimiento" => "DEVOLUCION", 
                    "ticket" => $datos->ticket, 
                    "edit" => $edit, 
                    "codigo" => $datos_sal->codigo,
                    "origen" => url(), 
                    "cedula" => $datos->cedula, 
                    "serial" => $datos_sal->serial,
                    "sede" => $datos->sede,
                    "observaciones" => $observaciones,
                );
                $id = $arr_resul_ser['id'];
                $histo_base = $arr_resul_ser['historial'];
                $contentsDecoded = json_decode($histo_base, true);
                $contentsDecoded[] = $historial_ser; 
                $historial = json_encode($contentsDecoded, JSON_UNESCAPED_UNICODE);
                $dataSave = array(
                    '811042087',
                    "ENTRADA",
                    $observaciones,
                    $historial,
                    $edit,
                    $fechahoy,
                    $id
                );
                $exe_up = $sql->execute($dataSave);
                $error_dev = $sql->errorInfo();
            }
            if($datos_sal->serial != '' && ($datos_sal->baja != '' || $datos_sal->cobro != '')){
                $sql_ser_up = $conn_inv->prepare("UPDATE control_inventario_base_seriales SET movimiento = ?, observaciones = ?, historial = ?, edit = ?, fecha_edit = ? WHERE id = ?");
                $sql_insert_cod = $conn_inv->prepare ("INSERT INTO control_inventario_base_seriales_baja (fecha, codigo, serial, cedula, movimiento, cantidad, ticket, sede, categoria, sub_categoria, observaciones, historial, edit, fecha_edit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $movi = '';
                if($datos_sal->baja != ''){
                    $movi = "BAJA";
                }
                else if($datos_sal->cobro != ''){
                    $movi = "COBRO";
                }
                $fecha_for = ''; 
                $fecha_for = $datos->fecha.' '.date('H:i:s');
                $historial_ser = array(
                    "fecha" => $fechahoy,
                    "fecha_edit" => $fecha_for, 
                    "movimiento" => $movi, 
                    "ticket" => $datos->ticket, 
                    "edit" => $edit,
                    "codigo" => $datos_sal->codigo, 
                    "origen" => url(), 
                    "cedula" => $datos->cedula, 
                    "serial" => $datos_sal->serial,
                    "sede" => $datos->sede,
                    "observaciones" => $datos_sal->observaciones,
                );
                $id = $arr_resul_ser['id'];
                $histo_base = $arr_resul_ser['historial'];
                $contentsDecoded = json_decode($histo_base, true);
                $contentsDecoded[] = $historial_ser; 
                $historial = json_encode($contentsDecoded, JSON_UNESCAPED_UNICODE);

                $dataSave1 = array(
                    $datos->fecha, 
                    $datos_sal->codigo, 
                    $datos_sal->serial,
                    $datos->cedula, 
                    $movi,
                    1,
                    $datos->ticket,
                    strtoupper($datos->sede), 
                    strtoupper($datos->categoria), 
                    strtoupper($datos->sub_cate), 
                    $datos_sal->observaciones,
                    $historial,
                    $edit, 
                    $fechahoy
                );
                $dataSave = array(
                    $movi,
                    $datos_sal->observaciones,
                    $historial,
                    $edit,
                    $fechahoy,
                    $id
                );
                $exe_ser_up = $sql_ser_up->execute($dataSave);
                $exe_ser_up1 = $sql_insert_cod->execute($dataSave1);
            }
        }
        if($exe || $exe_dev_n || $exe_up || $exe_ser_up || $exe_ser_up1 || $exe_c){
                $output = array(
                    'success' => true, 
                    'msg' => 'Se guardo correctamente.', 
                    'info' => $error,
                    'info2' => $error,
                    'info4' => $datasave2,
                    'info5' => $error_c_up
                );
        }
        else{
            if($msgError != ''){
                $msg = $msgError;
            }
            else{
                $msg = 'No Se Pudo Guardar La Informacion';
            }
            $output = array(
                'success' => false, 
                'msg' => $msg, 
                'info' => $dataSave,
                'info2' => $error_dev,
                'info3' => $exe_up,
                'info4' => $sql_error_up,
                'msg_err' => $msgError,
                'info5' => $sql_insert_ser
            );
        }
        echo json_encode($output);
    }
    elseif (isset($_POST['save_salAll'])){
        $datos = json_decode($_POST['save_salAll']);
        $datos_sal = json_decode($_POST['data_save_salAll']);
        $edit = $_SESSION['cedula'];
        $msgError = '';
        $sql_perm = $conn_inv->prepare("SELECT parametros FROM `parametros_control_inventario` WHERE categoria = '$datos->categoria'");
        $sql_perm->execute();
        $error_s = $sql_perm->errorInfo();
        $result_perm = $sql_perm->fetch(PDO::FETCH_ASSOC);
        $permi = json_decode($result_perm['parametros']);
        $permi_sedes = array_search($sede, array_column($permi->sedes, 'sede'));
        $permi_admin = in_array($_SESSION['cedula'], $permi->sedes[$permi_sedes]->permisos->admin);
        $permi_edit = in_array($edit, $permi->sedes[$permi_sedes]->permisos->editor);
        if(!$permi_admin  && !$permi_edit ){
            $msgError = "No Tiene Permisos para Editar";
            $error = 'Permiso Edición';
        }
        else{
            $sql_insert_entra = 'INSERT INTO control_inventario_movimientos_entrada (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, cantidad, observaciones, edit, fecha_edit) VALUES ';
            $sqlMarks_end = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $marks_end = [];
            $values_end = [];

            $sql_insert_cod = 'INSERT INTO control_inventario_movimientos_baja (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, cantidad, observaciones, edit, fecha_edit) VALUES ';
            $sqlMarks = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $marks = [];
            $values = [];

            $sql_insert_cod_c = 'INSERT INTO control_inventario_movimientos_baja (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, cantidad, observaciones, edit, fecha_edit) VALUES ';
            $sqlMarks_c = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $marks_c = [];
            $values_c = [];

            $sql_insert_cod_dev_n = 'INSERT INTO control_inventario_movimientos_devolucion (fecha, ticket, codigo, cedula, movimiento, categoria, sub_categoria, sede, cantidad, observaciones, edit, fecha_edit) VALUES ';
            $sqlMarks_dev_n = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $sqlMarks_dev_b = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $sqlMarks_dev_c = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $marks_dev_n = [];
            $marks_dev_b = [];
            $marks_dev_c = [];
            $values_dev_n = [];
            $values_dev_b = [];
            $values_dev_c = [];

            $sql_insert_cod_ser = 'INSERT INTO control_inventario_base_seriales_baja (fecha, codigo, serial, cedula, movimiento, cantidad, ticket, sede, categoria, sub_categoria, observaciones, historial, edit, fecha_edit) VALUES ';
            $sqlMarks_ser = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $marks_ser = [];
            $values_ser = [];

            $sql_ser = $conn_inv->prepare("SELECT id, codigo, serial, cedula, movimiento, historial FROM `control_inventario_base_seriales`  WHERE categoria = '$datos->categoria'");
            $sql_ser->execute();
            $sql_error_ser = $sql_ser->errorInfo();
            $sql_count_ser = $sql_ser->rowCount();
            $arr_resul_ser = $sql_ser->fetchAll(PDO::FETCH_ASSOC);
            $count_ser = 0;
            $ids = array();
            $updateParams = array();
            $idsids_ser = array();
            $updateParams_ser = array();
            $count_nor = 0;
            $count_nor_c = 0;
            $count_nor_dev = 0;
            $count_ser_d = 0;
            foreach($datos_sal as $value){
                $codigo = $value->codigo;
                $serial = $value->serial;
                $baja = $value->baja;
                $devolucion = $value->devolucion;
                $observaciones = $value->observaciones;
                $cobro = $value->cobro;
                if($serial == '' && $baja != "" ){
                    $marks[] = $sqlMarks;
                    array_push($values, 
                        $datos->fecha, 
                        $datos->ticket, 
                        $codigo, 
                        $datos->cedula, 
                        "BAJA", 
                        strtoupper($datos->categoria), 
                        strtoupper($datos->sub_cate), 
                        strtoupper($datos->sede),
                        $baja, 
                        $observaciones,
                        $edit, 
                        $fechahoy
                    );
                    $marks_dev_n[] = $sqlMarks_dev_n;
                    array_push($values_dev_n, 
                        $datos->fecha, 
                        $datos->ticket, 
                        $codigo, 
                        $datos->cedula, 
                        "DEVOLUCION", 
                        strtoupper($datos->categoria), 
                        strtoupper($datos->sub_cate), 
                        strtoupper($datos->sede),
                        $baja, 
                        $observaciones,
                        $edit, 
                        $fechahoy
                    );
                    $count_nor_dev++;
                    $count_nor++;
                }
                if($serial == '' && $cobro != ""){
                    $marks_c[] = $sqlMarks_c;
                    array_push($values_c, 
                        $datos->fecha, 
                        $datos->ticket, 
                        $codigo, 
                        $datos->cedula, 
                        "COBRO", 
                        strtoupper($datos->categoria), 
                        strtoupper($datos->sub_cate), 
                        strtoupper($datos->sede),
                        $cobro, 
                        $observaciones,
                        $edit, 
                        $fechahoy
                    );
                    $marks_dev_n[] = $sqlMarks_dev_n;
                    array_push($values_dev_n, 
                        $datos->fecha, 
                        $datos->ticket, 
                        $codigo, 
                        $datos->cedula, 
                        "DEVOLUCION", 
                        strtoupper($datos->categoria), 
                        strtoupper($datos->sub_cate), 
                        strtoupper($datos->sede),
                        $baja, 
                        $observaciones,
                        $edit, 
                        $fechahoy
                    );
                    $count_nor_dev++;
                    $count_nor_c++;
                }
                if($serial == '' && $devolucion != "" ){
                    $marks_dev_n[] = $sqlMarks_dev_n;
                    array_push($values_dev_n, 
                        $datos->fecha, 
                        $datos->ticket, 
                        $codigo, 
                        $datos->cedula, 
                        "DEVOLUCION", 
                        strtoupper($datos->categoria), 
                        strtoupper($datos->sub_cate), 
                        strtoupper($datos->sede),
                        $devolucion, 
                        $observaciones,
                        $edit, 
                        $fechahoy
                    );
                    $marks_end[] = $sqlMarks_end;
                    array_push($values_end, 
                        $datos->fecha, 
                        $datos->ticket, 
                        $codigo, 
                        811042087, 
                        "ENTRADA", 
                        strtoupper($datos->categoria), 
                        strtoupper($datos->sub_cate), 
                        strtoupper($datos->sede),
                        $devolucion, 
                        $observaciones,
                        $edit, 
                        $fechahoy
                    );

                    $count_nor_dev++;

                }
                if($serial != '' && $devolucion != "" ){ 
                    $fecha_for = ''; 
                    $fecha_for = $datos->fecha.' '.date('H:i:s');
                    $historial_ser = array(
                        "fecha" => $fechahoy,
                        "fecha_edit" => $fecha_for, 
                        "movimiento" => "DEVOLUCION", 
                        "ticket" => $datos->ticket, 
                        "edit" => $edit, 
                        "codigo" => $codigo,
                        "origen" => url(), 
                        'cedula' => $datos->cedula, 
                        "serial" => $serial, 
                        "sede" => $datos->sede,
                        "observaciones" => $observaciones
                    ); 
                    $pos_codigo_ser = array_search($serial, array_column($arr_resul_ser, 'serial'));
                    $id = $arr_resul_ser[$pos_codigo_ser]['id'];
                    $ids[] = intval($id);
                    $histo_base = $arr_resul_ser[$pos_codigo_ser]['historial'];
                    $contentsDecoded = json_decode($histo_base, true);
                    $contentsDecoded[] = $historial_ser; 
                    $historial = json_encode($contentsDecoded, JSON_UNESCAPED_UNICODE);
                    $updateParams['cedula'][$id] = 811042087;
                    $updateParams['movimiento'][$id] = "ENTRADA";
                    $updateParams['observaciones'][$id] = $observaciones;
                    $updateParams['historial'][$id] = $historial;
                    $updateParams['edit'][$id] = $edit;
                    $updateParams['fecha_edit'][$id] = $fechahoy;
                    $count_ser_d++;
                }
                if($serial != '' && ($cobro != "" || $baja != "")){
                    $movi = '';
                    if($baja != ''){
                        $movi = "BAJA"; 
                    }
                    else{
                        $movi = "COBRO";
                    }
                    $fecha_for = ''; 
                    $fecha_for = $datos->fecha.' '.date('H:i:s');
                    $historial_ser = array(
                        "fecha" => $fechahoy,
                        "fecha_edit" => $fecha_for, 
                        "movimiento" => $movi, 
                        "ticket" => $datos->ticket, 
                        "edit" => $edit, 
                        "codigo" => $codigo,
                        "origen" => url(), 
                        $datos->cedula, 
                        "serial" => $serial, 
                        "sede" => $datos->sede,
                        "observaciones" => $observaciones
                    ); 
                    $pos_codigo_ser = array_search($serial, array_column($arr_resul_ser, 'serial'));
                    $id = $arr_resul_ser[$pos_codigo_ser]['id'];
                    $ids_ser[] = intval($id);
                    $histo_base = $arr_resul_ser[$pos_codigo_ser]['historial'];
                    $contentsDecoded = json_decode($histo_base, true);
                    $contentsDecoded[] = $historial_ser; 
                    $historial = json_encode($contentsDecoded, JSON_UNESCAPED_UNICODE);
                    // (fecha, codigo, serial, cedula, movimiento, cantidad, ticket, sede, categoria, sub_categoria, observaciones, historial, edit, fecha_edit)
                    $marks_ser[] = $sqlMarks_ser;
                    array_push($values_ser, 
                        $datos->fecha, 
                        $codigo, 
                        $serial,
                        $datos->cedula, 
                        $movi, 
                        1,
                        $datos->ticket, 
                        strtoupper($datos->sede),
                        strtoupper($datos->categoria), 
                        strtoupper($datos->sub_cate), 
                        $observaciones,
                        $historial,
                        $edit, 
                        $fechahoy
                    );

                    $updateParams_ser['movimiento'][$id] = $movi;
                    $updateParams_ser['observaciones'][$id] = $observaciones;
                    $updateParams_ser['historial'][$id] = $historial;
                    $updateParams_ser['edit'][$id] = $edit;
                    $updateParams_ser['fecha_edit'][$id] = $fechahoy;
                    $count_ser++;
                }
            }
            if($count_ser_d != 0){
                $data_qry_upd_ser = createQryUpdate('control_inventario_base_seriales', 'id', $ids, $updateParams);
                $qry_exe_s = $conn_inv->prepare($data_qry_upd_ser);
                $exe_ser_d = $qry_exe_s->execute();
                $sql_insert_cod_ser = $conn_inv->prepare($sql_insert_cod_ser. implode(',', $marks_ser));
                $exe_ser_db = $sql_insert_cod_ser->execute($values_ser);
                $sql_error_ins_db = $sql_insert_cod_ser->errorInfo();
                $sql_count_ins_db = $sql_insert_cod_ser->rowCount();
            }
            if($count_ser != 0){
                $data_qry_upd = createQryUpdate('control_inventario_base_seriales', 'id', $ids_ser, $updateParams_ser);
                $qry_exe = $conn_inv->prepare($data_qry_upd);
                $exe_ser = $qry_exe->execute();
            }
            if( $count_nor != 0){

                $sql_insert_cod= $conn_inv->prepare($sql_insert_cod . implode(',', $marks));
                $exe = $sql_insert_cod->execute($values);
                $sql_error_ins = $sql_insert_cod->errorInfo();
                $sql_count_ins = $sql_insert_cod->rowCount();

                // $sql_insert_cod_dev_n = $conn_inv->prepare($sql_insert_cod_dev_n . implode(',', $marks_dev_b));
                // $exe_dev_ba = $sql_insert_cod_dev_n->execute($values_dev_b);
            }
            if( $count_nor_c != 0){
                $sql_insert_cod_c= $conn_inv->prepare($sql_insert_cod_c. implode(',', $marks_c));
                $exe_c = $sql_insert_cod_c->execute($values_c);
                $sql_error_ins_c = $sql_insert_cod_c->errorInfo();
                $sql_count_ins_c = $sql_insert_cod_c->rowCount();

                // $sql_insert_cod_dev_n = $conn_inv->prepare($sql_insert_cod_dev_n . implode(',', $marks_dev_c));
                // $exe_dev_c = $sql_insert_cod_dev_n->execute($values_dev_c);
            }
            if( $count_nor_dev != 0){
                $sql_insert_cod_dev_n = $conn_inv->prepare($sql_insert_cod_dev_n . implode(',', $marks_dev_n));
                $exe_dev_n = $sql_insert_cod_dev_n->execute($values_dev_n);
                $sql_error_ins_n = $sql_insert_cod_dev_n->errorInfo();
                $sql_count_ins_n = $sql_insert_cod_dev_n->rowCount();

                $sql_insert_entra = $conn_inv->prepare($sql_insert_entra . implode(',', $marks_end));
                $exe_exe = $sql_insert_entra->execute($values_end);
                $sql_error_exe = $sql_insert_entra->errorInfo();
                $sql_count_exe = $sql_insert_entra->rowCount();
            }
        }    
        if($exe || $exe_ser || $exe_dev_n || $exe_c || $exe_ser_db || $exe_ser_d){
            $output = array(
                'success' => true, 
                'msg' => 'Se guardo correctamente.', 
                'info6' => $marks_ser,
                'info2' => $sql_insert_cod,
                'info3' => $sql_up_cod,
                'info4' => $sql_error_up,
                'info5' => $sql_insert_ser
            );
        }else{
            if($msgError != ''){
                $msg = $msgError;
            }
            else{
                $msg = 'No Se Pudo Guardar La Informacion';
            }
            $output = array(
                'success' => false, 
                'msg' => $msg, 
                'info' => $exe_ser,
                'info2' => $exe_ser_d,
                'info3' => $sql_up_cod,
                'info4' => $sql_error_up,
                'msg_err' => $count_ser_d,
                'info5' => $count_ser
            );
        }
        echo json_encode($output);
    }
    else if (isset($_POST['saveCod'])) {
        $error_dupli ='';
        $error_per = '';
        $msgError = '';
        $datos = json_decode($_POST['saveCod']);
        $edit = $_SESSION['cedula'];
        $sede = $datos->sede;
        $sql_perm = $conn_inv->prepare("SELECT parametros FROM `parametros_control_inventario` WHERE categoria = '$datos->categoria'");
        $sql_perm->execute();
        $error_s = $sql_perm->errorInfo();
        $result_perm = $sql_perm->fetch(PDO::FETCH_ASSOC);
        $permi = json_decode($result_perm['parametros']);
        $permi_sedes = array_search($sede, array_column($permi->sedes, 'sede'));
        $permi_admin = in_array($_SESSION['cedula'], $permi->sedes[$permi_sedes]->permisos->admin);
        $permi_edit = in_array($edit, $permi->sedes[$permi_sedes]->permisos->editor);
        if(!$permi_admin  && !$permi_edit ){
            $msgError = "No Tiene Permisos para Editar";
            $error_per = 'Permiso Edición';
        };
        $sql_val_fam = $conn_inv->prepare("SELECT id, codigo, area FROM control_inventario_base_codigos WHERE codigo = '$datos->codigo' ");
        $exe = $sql_val_fam->execute();
        $res = $sql_val_fam->fetch(PDO::FETCH_ASSOC);
        if($res != null){
            $sql = $conn_inv->prepare("UPDATE control_inventario_base_codigos SET area = ? WHERE id = ?");
            $dataSave = array(
                json_encode($datos->area),
                $res['id']
            );
            $exe = $sql->execute($dataSave);
            $msg = 'Se Actualizó Correctamente'; 
        }
        else{
        $historial_pre = array(array('fecha'=>$fechahoy, 'precio' => $datos->precio));
            $sql = $conn_inv->prepare("INSERT INTO control_inventario_base_codigos (codigo, descripcion, precio, medida, tipo, categoria, subcategoria, area, historial_precios, edit, fecha_edit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $dataSave = array (
                $datos->codigo,
                $datos->descripcion,
                $datos->precio,
                $datos->medida,
                $datos->tipo,
                strtoupper($datos->categoria),
                strtoupper($datos->subcategoria),
                json_encode(['general']),
                json_encode($historial_pre),
                $edit,
                $fechahoy
            );
            $msg = 'Se guardo correctamente.';
            $exe = $sql->execute($dataSave);
            $error = $sql->errorInfo();
        }
        if($exe){
            $output = array('success' => true, 'msg' => $msg, 'duplicado' => $error_dupli, 'info' => $error, 'info2' => $permi_admin, 'id' => $res['id']);
        }else{
            $output = array('success' => false, 
            'msg' => 'No se pudo guardar la información', 
            'info' => $error,
            'info2' => $error_per,
            'msg_err' => $msgError,
            'duplicado' => $error_dupli,
            'info3' => $permi_sedes,
            'permi' => $permi,
            'permi_id2' => $permi->sedes[$permi_sedes]->permisos->admin,
            'permi_id3' => $permi_admin
        );
        }
        echo json_encode($output);
    }
?>


