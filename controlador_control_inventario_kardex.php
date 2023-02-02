<?php
    error_reporting(0);
    session_start();
    header('Content-Type: text/html; charset=utf-8');
    $cedula_se = $_SESSION['cedula'];
    $fechahoy = date('Y-m-d G:i:s', strtotime('-5 hour, -00 minutes, -00 seconds,', strtotime(date('Y-m-d G:i:s'))));
    $fecha_str = strtotime($fechahoy);
    $fecha_num = date('YmdHis', $fecha_str); //20210121210000
    $fecha_print = date('d/m/Y H:i:s', $fecha_str);
    $ticket = "{$cedula_se}{$fecha_num}";
    
    require_once '../../../../controller_cinco.php';
    $conn_azul = connect_PDO('azul');
    if (isset($_POST['get_data_kar_bodega'])) {
        $datos = json_decode($_POST['get_data_kar_bodega']);
        $datosAll = json_decode($_POST['get_cod_kardex']);
        $categoria = $datos->categoria;
        $sede = $datos->sede;
        $add_bus = " categoria ='$categoria' AND sede ='$sede' ";
        $res_total = array();
        $cedula = $datos->cedula;
        $fecha = $datos->fecha;
        $codigo = $datosAll->codigo;
        $serial = $datosAll->serial;
        $movimiento = $datos->movimiento;
        
        if($datos->action == 'buscar'){
            if($serial != ''){ 
                $add_bus .= " AND serial = '$serial' ";
                $sql_cod = $conn_inv->prepare("SELECT historial  FROM `control_inventario_base_seriales`  WHERE $add_bus");
                $sql_cod->execute();
                $res_cod = $sql_cod->fetch(PDO::FETCH_ASSOC);
                // if ($res_cod != null) {
                    $res_total = json_decode($res_cod['historial']);
                // }
            }
            else{
                if($cedula != ''){ $add_bus .= " AND cedula = '$cedula' ";}
                if($fecha != ''){ $add_bus .= " AND fecha = '$fecha' ";}
                if($codigo != ''){ $add_bus .= " AND codigo = '$codigo' ";}
                if($movimiento != ''){ $add_bus .= " AND movimiento = '$movimiento' ";}

                $sql_cod = $conn_inv->prepare("SELECT codigo, serial, cedula, movimiento, cantidad, observaciones, edit, fecha_edit  FROM `control_inventario_base_seriales`  WHERE $add_bus");
                $sql_cod->execute();
                $res_cod = $sql_cod->fetchAll(PDO::FETCH_ASSOC);

                $sql_cod_n = $conn_inv->prepare("SELECT fecha, codigo, cedula, movimiento, serial, cantidad, observaciones, edit, fecha_edit  FROM `control_inventario_movimientos_baja`  WHERE $add_bus");
                $sql_cod_n->execute();
                $res_cod_n = $sql_cod_n->fetchAll(PDO::FETCH_ASSOC);

                $sql_cod_dev = $conn_inv->prepare("SELECT fecha, codigo, cedula, movimiento, serial, cantidad, observaciones, edit, fecha_edit  FROM `control_inventario_movimientos_devolucion`  WHERE $add_bus");
                $sql_cod_dev->execute();
                $res_cod_dev = $sql_cod_dev->fetchAll(PDO::FETCH_ASSOC);

                $sql_cod_entra = $conn_inv->prepare("SELECT fecha, codigo, cedula, movimiento, serial, cantidad, observaciones, edit, fecha_edit  FROM `control_inventario_movimientos_entrada`  WHERE $add_bus");
                $sql_cod_entra->execute();
                $res_cod_entra = $sql_cod_entra->fetchAll(PDO::FETCH_ASSOC);

                $sql_cod_entre = $conn_inv->prepare("SELECT fecha, codigo, cedula, movimiento, serial, cantidad, observaciones, edit, fecha_edit  FROM `control_inventario_movimientos_entrega`  WHERE $add_bus");
                $sql_cod_entre->execute();
                $res_cod_entre = $sql_cod_entre->fetchAll(PDO::FETCH_ASSOC);

                if ($res_cod != null) {
                    $res_total[] = $res_cod;
                }
                if ($res_cod_n != null) {
                    $res_total[] = $res_cod_n;
                }
                if ($res_cod_dev != null) {
                    $res_total[] = $res_cod_dev;
                }
                if ($res_cod_entra != null) {
                    $res_total[] = $res_cod_entra;
                }
                if ($res_cod_entre != null) {
                    $res_total[] = $res_cod_entre;
                }
            }
        }
        else if($datos->action == 'saldo'){
            if($cedula != ''){ $add_bus .= " AND cedula = '$cedula' ";}
            if($serial != ''){
                $add_bus .= " AND serial = '$serial' ";

                $sql_cod = $conn_inv->prepare("SELECT codigo, serial, movimiento, cantidad FROM `control_inventario_base_seriales`  WHERE $add_bus ");
                $sql_cod->execute();
                $res_cod = $sql_cod->fetchAll(PDO::FETCH_ASSOC);
                if($res_cod != null){
                    $res_total[] = $res_cod;
                }
            }
            else{
                if($codigo != ''){ $add_bus .= " AND codigo = '$codigo' ";}
                
                $sql_cod_n = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_entrega`  WHERE $add_bus ");
                $sql_cod_n->execute();
                $res_cod_n = $sql_cod_n->fetchAll(PDO::FETCH_ASSOC);

                $sql_cod_dev = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_devolucion`  WHERE $add_bus");
                $sql_cod_dev->execute();
                $res_cod_dev = $sql_cod_dev->fetchAll(PDO::FETCH_ASSOC);

                $sql_cod_entra = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_entrada`  WHERE categoria ='$categoria' AND sede ='$sede'");
                $sql_cod_entra->execute();
                $res_cod_entra = $sql_cod_entra->fetchAll(PDO::FETCH_ASSOC);

                $sql_cod_ser = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_base_seriales`  WHERE $add_bus ");
                $sql_cod_ser->execute();
                $res_cod_ser = $sql_cod_ser->fetchAll(PDO::FETCH_ASSOC);

                $sql_cod_baja = $conn_inv->prepare("SELECT codigo, movimiento, serial, cantidad FROM `control_inventario_movimientos_baja`  WHERE $add_bus");
                $sql_cod_baja->execute();
                $res_cod_baja = $sql_cod_baja->fetchAll(PDO::FETCH_ASSOC);

                if($res_cod_baja != null){
                    $res_total[] = $res_cod_baja;
                }
                if($res_cod_ser != null){
                    $res_total[] = $res_cod_ser;
                }
                if($res_cod_entra != null){
                    $res_total[] = $res_cod_entra;
                }
                if($res_cod_n != null){
                    $res_total[] = $res_cod_n;
                }
                if($res_cod_dev != null){
                    $res_total[] = $res_cod_dev;
                }
            }
        }
        echo json_encode($res_total);
    }
?>