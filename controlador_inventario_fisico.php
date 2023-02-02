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
if(isset($_POST['getTecnico'])){
    $dni = $_POST['getTecnico'];
    $res_total = array();
    $sql = $conn_inv->prepare("SELECT id, codigo, descripcion FROM `control_inventario_base_codigos`");
    $sql->execute();
    $sql_error = $sql->errorInfo();
    $sql_count = $sql->rowCount();
    $arr_resul_cod = $sql->fetchAll(PDO::FETCH_ASSOC);
    $arr_cod = array();
    $arr_cod_n = array();
    $sql_cod_n = $conn_inv->prepare("SELECT codigo,  serial, cantidad, categoria FROM `control_inventario_movimientos_entrega`  WHERE cedula = '$dni'");
    $sql_cod_n->execute();
    $res_cod_n = $sql_cod_n->fetchAll(PDO::FETCH_ASSOC);
    $sql_cod = $conn_inv->prepare("SELECT codigo, serial, cantidad, categoria FROM `control_inventario_base_seriales`  WHERE cedula = '$dni' AND movimiento = 'ENTREGA'");
    $sql_cod->execute();
    $res_cod = $sql_cod->fetchAll(PDO::FETCH_ASSOC);
    if($res_cod != null){
        foreach($res_cod as $value){
            $pos_cod = array_search($value['codigo'], array_column($arr_resul_cod, 'codigo'));
            $arr_cod[] = array(
                'codigo' => $value['codigo'],
                'descripcion' => $arr_resul_cod[$pos_cod]['descripcion'],
                'serial' => $value['serial'],
                'cantidad' => $value['cantidad'],
                'categoria' => $value['categoria']
            );
        }
        $res_total[] = $arr_cod;
    }
    if($res_cod_n != null){
        foreach($res_cod_n as $value){
            $pos_cod = array_search($value['codigo'], array_column($arr_resul_cod, 'codigo'));
            $arr_cod_n[] = array(
                'codigo' => $value['codigo'],
                'descripcion' => $arr_resul_cod[$pos_cod]['descripcion'],
                'serial' => $value['serial'],
                'cantidad' => $value['cantidad'],
                'categoria' => $value['categoria']
            );
        }
        $res_total[] = $arr_cod_n;
    }
    echo json_encode($res_total);
}
else if(isset($_POST['getFecha'])){
    $dni = $_POST['getFecha'];
    $sql = $conn_inv->prepare("SELECT fecha FROM `control_inventario_fisico` WHERE cedula = '$dni'");
    $sql->execute();
    $res = $sql->fetchAll(PDO::FETCH_ASSOC);
    if($res != null){
        echo json_encode($res);
    }
    else{
        echo json_encode([]);
    }
}
else if(isset($_POST['getTecnicoInvent'])){
    $datos = json_decode($_POST['getTecnicoInvent']);
    $dni = $datos->cedula;
    $fecha = $datos->fecha;
    $add_bus = '';
    if($fecha != ''){$add_bus .= " fecha = '$fecha' AND ";}
    $total = array();
    $sql = $conn_inv->prepare("SELECT inventario FROM `control_inventario_fisico`  WHERE $add_bus  cedula = '$dni' ORDER BY id DESC LIMIT 1");
    $sql->execute();
    $res = $sql->fetch(PDO::FETCH_ASSOC);
    if($res != null){
        echo $res['inventario'];
    }
    else{
        echo json_encode([]);
    }
}
else if(isset($_POST['save_inventario'])){
    $datos = json_decode($_POST['save_inventario']);
    $sql = $conn_inv->prepare("INSERT INTO control_inventario_fisico (cedula, fecha, inventario, edit, fecha_edit) VALUES (?, ?, ?, ?, ?)");
    $dataSave = array(
        $datos->cedula,
        $datos->hoy,
        json_encode($datos->data_inven),
        $cedula,
        $fechahoy
    );
    $exe = $sql->execute($dataSave);
    $error = $sql->errorInfo();
    if($exe){
        $output = array(
            'success' => true, 
            'msg' => 'Se guardo correctamente.', 
            'info' => $exe
        );
    }
    else{
        $output = array(
            'success' => false, 
            'msg' => 'No Se Pudo Guardar La Informacion', 
            'info' => $error
        );
    }
echo json_encode($output);
}
?>