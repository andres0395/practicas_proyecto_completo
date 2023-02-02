<?php
error_reporting(0);
session_start();

$cedula = $_SESSION['cedula'];
$sede = $_SESSION['sede']; 
$fechahoy = date('Y-m-d G:i:s', strtotime('-5 hour, -00 minutes, -00 seconds,', strtotime(date('Y-m-d G:i:s'))));
$fecha_str = strtotime($fechahoy);
$fecha_num = date('YmdHis', $fecha_str); //20210121210000
$fecha_print = date('d/m/Y H:i:s', $fecha_str);
$ticket = "{$cedula}{$fecha_num}";
include "../../configure.php";
require_once('../controller_cinco.php');
$acentos = $abc4n2x34n->query("SET NAMES 'utf8'");

header("Content-Type: text/html; charset=utf-8");
$conn_azul = connect_PDO('azul');

    require 'header.php';
    ?>
    
        <style>

            .div_dia:hover, .div_dia_active {
                box-shadow: rgb(50 50 93 / 48%) 0px 30px 60px -12px inset, rgb(0 0 0 / 65%) 0px 18px 36px -18px inset;
            }
            
            .color-palette{
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                max-width: 300px;
                margin-top: 5px;
                margin-bottom: 10px;
            }

            .color_container{
                width: calc(70% / 7);
                box-shadow: rgba(14, 30, 37, 0.12) 0px 2px 4px 0px, rgba(14, 30, 37, 0.32) 0px 2px 16px 0px;
                border-top-left-radius: 3px; 
                border-bottom-left-radius: 3px; 
                border-top-right-radius: 3px; 
                border-bottom-right-radius: 3px; 
            }

            .js-palette-color{
                width: 100%; height: 27px;
                cursor: pointer;
                border-top-left-radius: 3px; 
                border-bottom-left-radius: 3px; 
                border-top-right-radius: 3px; 
                border-bottom-right-radius: 3px; 
            }

            .js-palette-color:hover{
                opacity: .7;
            }
            #sty{
                color: white;
                cursor: pointer;
            }
        
            .nav-tabs .active {
                color: #0054cf !important;
            }
            
            a {
                color: inherit;
                text-decoration: none;
            }
            .clase{
                font-size:small;
            }
            button {
                cursor: pointer;
                background: transparent;
                border: none;
                outline: none;
                font-size: inherit;
            }

            .deshabilitar {
                cursor: not-allowed;
                pointer-events: none;
            }
            .tarjeta{
                
            }
            .tamano{
                font-size: 10px;
                padding: 5px;
            }
            .correr{
                max-width:400px !important;
            }
        </style>
    </head>
    <div class="contain_resizer h-90">

        <div id="panel_left" class="bg-primary container__left flex-column info-tables correr" data-max-panel="330px" >
            <div class="ocultar_horizontal_left" style="">
                <div class="ocultar_panel float-right" title="Ocultar panel">←</div>
            </div>
            <div>
            
                <!--Left-->
                <div class="col-auto px-0">
                    <div id="sidebar" class="collapse collapse-horizontal show border-end">
                        <div id="sidebar-nav" class="list-group border-0 rounded-0 text-sm-start">
                            <div id="option_movi1">
                                <div class="p-1">
                                    <input type="text" class="bg-transparent form-control form-control-sm fs-4 p-0 text-center text-warning disabled trl_digital" name="ticketPerson" id="ticketPerson" value="<?php echo trim($ticket); ?>" placeholder="Ticekt movimiento" readonly style="font-size: 1.4rem !important;">
                                </div>
                                <div class="px-2 py-1">
                                    <input type="date" class="form-control form-control-sm text-center" name="fecha_movimiento" id="fecha_movimiento" value="<?php echo explode(' ',$fechahoy)[0]; ?>">
                                </div>
                            </div>
                            <div  class=" d-flex justify-content-between list-group-item bg-gradient-primary dropdown text-white">
                                <a id="div_cate_data" class="pe-none d-flex justify-content-start dropdown-toggle w-100"  data-bs-toggle="dropdown" data-parent="#sidebar" aria-expanded="false">
                                    <div>
                                        <span class="text-uppercase" id="mat_mat_selected">Categorias</span>  
                                    </div>
                                </a>
                                <ul id="sel_fam"></ul>
                                <div id="modal_add_cate"></div>
                            </div>
                            <div class="pe-none list-group-item bg-primary dropdown border-2">
                                <a id="btn_sede" class=" d-flex justify-content-between text-white border-end-0 text-truncate dropdown-toggle w-100" data-bs-toggle="dropdown" data-parent="#sidebar" aria-expanded="false">
                                    <div>
                                        <i class="fas fa-warehouse"></i> <span>Sede</span> 
                                    </div>
                                </a>
                                <ul class="dropdown-menu" id="sel_sede"></ul>
                                <div>
                                <small class="mb-1 text-white bottom-0 end-0 mb-n1 pe-4 position-absolute text-xs" id="work_sede"></small>
                                </div>
                            </div>
                            <div id="div_mov" class="pe-none list-group-item bg-primary dropdown text-white">
                                <small class="bottom-0 end-0 mb-n1 pe-4 position-absolute text-xs" id="movim_selected"></small>
                                <a class=" d-flex justify-content-between border-end-0 text-truncate dropdown-toggle w-100" data-bs-toggle="dropdown" data-parent="#sidebar" aria-expanded="false">
                                    <div >
                                        <i class="fas fa-dolly"></i> <span>Movimiento</span>  
                                    </div>
                                </a>
                                <ul class="dropdown-menu menu_mov" id="sede_mov"></ul>
                                <div>
                                    <small class="mb-1 text-white bottom-0 end-0 mb-n1 pe-4 position-absolute text-xs" id="work_movi"></small>
                                </div>
                            </div>
                            <form id="form_kardex">
                                <div id="div_mov_kardex" class="d-none border btn-group-toggle btn-group-xs col-12 justify-content-evenly mb-1 p-1 rounded-3">
                                    <label class="active ml-1 btn btn-sm btn-outline-primary mb-1 color_container btn_mov_kar col-5 p-2 mr-4 col-xl-5">
                                        <input type="radio" name="group_movi" id="entrada_label" class="rad_mat_mov" value="entrada" autocomplete="off"> Entrada
                                    </label>
                                    <label class="btn btn-sm btn-outline-primary mb-1 color_container btn_mov_kar col-5 p-2 col-xl-5">
                                        <input type="radio" name="group_movi" id="" class="rad_mat_mov" value="entrega" autocomplete="off"> Entrega
                                    </label>
                                    <label class="ml-1 btn btn-sm btn-outline-primary color_container mb-1 btn_mov_kar col-5 p-2 mr-4  col-xl-5">
                                        <input type="radio" name="group_movi" id="" class="rad_mat_mov" value="devolucion" autocomplete="off"> Devolucion
                                    </label>
                                    <label  class="btn btn-sm btn-outline-primary color_container mb-1 btn_mov_kar col-5 p-2 col-xl-5">
                                        <input type="radio" name="group_movi" id="" class="rad_mat_mov" value="baja" autocomplete="off"> Baja
                                    </label>
                                </div>
                                <div id="" class="d-none mb-3 mt-3 text-center">
                                    <div id="">
                                    </div>
                                </div>
                                <div id="option_movi2">
                                    <div id="" class="d-none btn-group btn-group-toggle mt-1 d-flex p-2 btn_kar_group" data-toggle="buttons">
                                        <label id="tecnico_bode" class="btn btn-sm btn-outline-primary">
                                            <input type="radio" name=""  class="rad_mat" value="Saldos" autocomplete="off" checked="true">Tecnico
                                        </label>
                                        <label id="bodega_tec" class="btn btn-sm btn-outline-primary active">
                                            <input type="radio" class="rad_mat" value="Buscar" autocomplete="off">Bodega
                                        </label>
                                    </div>
                                    <div id="cod_ser" class="d-none btn-group btn-group-toggle mt-1 d-flex p-2 btn_kar_group" data-toggle="buttons">
                                        <label id="btn_codigo" class="btn btn-sm btn-outline-primary">
                                            <input type="radio" name=""  class="rad_mat" value="Saldos" autocomplete="off" checked="true">Codigo
                                        </label>
                                        <label id="btn_serial" class="btn btn-sm btn-outline-primary active">
                                            <input type="radio" class="rad_mat" value="Buscar" autocomplete="off">Serial
                                        </label>
                                    </div>
                                    <div id="mostrar_tecnico" class="d-none col bg-transparent border-end-0 d-inline-block text-truncate px-2 py-1 reset_mov_mod" style="border-bottom: 1px solid rgba(0,0,0,.125) !important;">
                                        <div class="mb-n4 mr-n2" style="">
                                        <!-- <div class="mt-n1 position-absolute" style="rigth:0;"> -->
                                            <?php if( $permi_manual || $permi_programacion ){ ?>
                                                <div class="align-items-center d-flex justify-content-end mr-2 mt-1" style="">
                                                    <label class="toggle-switchy" id="toggle-estado" for="sw_reg_add" data-size="sm">
                                                        <input type="checkbox" id="sw_reg_add" onclick="camIconSwitch('switch', this)" name="sw_reg_add" data-nonchecked="bg-danger">
                                                        <span class="toggle fw-bold default-size" data-before="Activos" data-after="Todos" data-nonchecked="--non-checked-red" style="font-size.7rem; width: 95px !important;">
                                                            <span class="switch d-flex align-items-center justify-content-center">
                                                                <i class="fas text-gray-500 fa-arrow-right" id=""></i>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <label class="m-0 ps-2 text-white"><i class="fas fa-user-alt"></i><span> Técnico </span></label>
                                        <input type="search" class="d-none form-control form-control-sm tecnicos search_per " name="tecnico_movimiento_inv" id="tecnico_movimiento_inv" style="font-size:.7rem" tab-index="0" autocomplete="off" placeholder="Buscar Tecnico">
                                        <input type="hidden" class="" name="cc_tecnico_movimiento_inv" id="cc_tecnico_movimiento_inv" value="">
                                        <div id="resultado_tecnico_movimiento_inv" class="cont_table_search">
                                            <table class="table table-bordered table-hover bg-body m-0"></table>
                                        </div>
                                        <div id="tarjeta_tecnico_movimiento_inv">
                                            
                                        </div>
                                        <i class="d-block f-1_5 fa-angle-double-down fas position-static text-center text-warning d-none arrow mt-n3" id="arrow_trasl"></i>
                                        <div id="tarjeta_tecnico_recibe">
                                        </div> 
                                    </div>
                                    <div class="col bg-transparent border-end-0 d-inline-block text-truncate px-2 py-1 d-none reset_mov_mod" style="border-bottom: 1px solid rgba(0,0,0,.125) !important;">
                                        <label for="tecnico_recibe" class="m-0 ps-2 text-white"><i class="fas fa-user-alt"></i><span> Técnico Recibe</span></label>
                                        <input type="search" class="form-control form-control-sm tecnicos search_per " name="tecnico_recibe" id="tecnico_recibe" style="font-size:.7rem" tab-index="0" autocomplete="off">
                                        <input type="hidden" class="" name="cc_tecnico_recibe" id="cc_tecnico_recibe" value="">
                                        <div id="resultado_tecnico_recibe" class="cont_table_search">
                                            <table class="table table-bordered table-hover bg-body m-0"></table>
                                        </div>
                                    </div>
                                    <div class="col list-group-item bg-transparent border-top-0 text-white c_pointer d-none reset_mov_mod" id="import_entrada" >
                                        <a id="entradaCod" class="tab b text-white d-flex" data-bs-toggle="modal" data-bs-target="#entradaCodigoModal">
                                            <img src="./img/import-csv.png" style="width: 25px; height: 25px;" title="Importar" alt="Importar" class="icon mr-1"> Importar Entrada
                                        </a>
                                    </div>
                                    <div class="col list-group-item bg-transparent border-top-0 text-white c_pointer d-none reset_mov_mod" id="import_entrega" >
                                        <a id="entregaCod" class="tab b text-white d-flex" data-bs-toggle="modal" data-bs-target="#entregaCodigoModal">
                                            <img src="./img/import-csv.png" style="width: 25px; height: 25px;" title="Importar" alt="Importar" class="icon mr-1"> Importar Entrega
                                        </a>
                                    </div>

                                    <div class="col list-group-item bg-transparent border-top-0 text-white c_pointer d-none reset_mov_mod" id="import_query_inver" >
                                        <a id="" class="tab b text-white d-flex" >
                                            <img src="./img/import-csv.png" style="width: 25px; height: 25px;" title="Plantilla Para Importar" alt="Plantilla Importe" class="icon mr-1"> Query Inversa
                                        </a>
                                    </div>
                                    <div class="col list-group-item bg-transparent border-top-0 text-white c_pointer d-none reset_mov_mod" id="import_query_asoc" >
                                        <a id="" class="tab b text-white d-flex" ></a>
                                        </div>
                                    <!-- fin 12-06  -->
                                    <div class="col list-group-item bg-transparent border-top-0 text-white c_pointer d-none reset_mov_mod" id="subir_query_retiros" >
                                        <a id="" class="tab b text-white d-flex" >
                                            <img src="./img/import-csv.png" style="width: 25px; height: 25px;" title="Importar" alt="Exportar" class="icon mr-1"> Subir Retiros
                                        </a>
                                    </div>
                                    <div id="div_aliado" class="col bg-transparent border-end-0 d-inline-block px-2 py-1 d-none reset_mov_mod" style="border-bottom: 1px solid rgba(0,0,0,.125) !important;">                                        
                                        <label  class="d-flex align-items-center justify-content-between m-0 ps-2 text-white">
                                            <div>
                                                <i class="fas fa-users"></i><span> Aliado:</span>
                                            </div>
                                            <i class="fas fa-plus-circle mx-2 c_pointer" id="icon_add_aliado" data-bs-toggle="modal" data-bs-target="#add_aliado"></i>
                                        </label>
                                        <select class="form-control form-control-sm form-select form-select-sm" name="select_aliado" id="select_aliado">
                                        </select>
                                    </div>
                                    <div id="div_aliado_imp" class="col list-group-item bg-transparent border-top-0 text-white-50 d-none" id="subir_query_traslados" >
                                        <a id="abrir_mod_ali" class="c_pointer tab b text-white d-flex" data-bs-toggle="modal" data-bs-target="#tras_aliado_mod">
                                            <img src="./img/import-csv.png" style="width: 25px; height: 25px;" title="Importar" alt="Exportar" class="icon mr-1"> Subir Traslados
                                        </a>
                                    </div>
                                </div>
                                <div id="mostrar_bodega_all" class="mt-1 d-none">
                                    <div class="m-2 form-group mb-1">
                                        <input id="inp_kar" type="search" name="codigo" class=" form-control form-control-sm " placeholder="Código">
                                        <input id="inp_kar_ser" type="search" name="serial" class="mt-2 form-control form-control-sm " placeholder="Serial">
                                    </div>
                                    <div id="" class="btn-group-toggle mt-3 d-flex p-2 justify-content-around" data-toggle="buttons">
                                        <label id="sal_buscar" class="col-3 color_container btn btn-sm btn-outline-primary">
                                            <input type="radio" name="" class="rad_mat" value="Saldos" autocomplete="off" checked="true">Saldos
                                        </label>
                                        <label id="codigo_buscar" class="col-3 color_container btn btn-sm btn-outline-primary">
                                            <input type="radio"   class="rad_mat" value="Buscar" autocomplete="off">Buscar
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="align-items-center d-flex justify-content-center resizer" data-direction="horizontal">
            <div class="draggable_vertical"></div>
        </div>
        <div class="overflow-auto tab-content w-100">                       
            <div class="nav nav-tabs w-100" id="nav-tab" role="tablist"  >
                <div class="f-1_5 mr-3 mt-2 text-primary" id="title_work"></div>
            </div>
                    <div class=" tab-pane fade show active " id="nav-admin" role="tabpanel" aria-labelledby="btn-card-admin">
                        <div class="d-none mostrar_horizontal_left">
                            <div class="ocultar_panel"  title="Mostrar panel">→</div>
                        </div>
                        <div class="container__top  ">
                            <div class="info-tables m-1 ">
                                <div id="div_carga_mat" class="p-2 px-lg-4 row">
                                    <div class="col-12 border border-lg rounded-lg">
                                        <form class="rounded-lg mt-1 mb-2 w-100" name="formInventario" id="formInventario">
                                            <div class="form-row justify-content-between">
                                                <div class="form-row ml-0 w-100">
                                                    <div class="col-12 col-2 col-sm-auto col-xl-2 form-group mb-3 mb-lg-1 ml-1 mr-4 position-relative" id="div_codigo">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="text-secondary mb-0">Código</label>
                                                            <div class="d-flex align-items-center justify-content-end">
                                                                <i class="fas fa-plus-circle c_pointer me-1 f-5 agregar_codigo pe-none text-gray-600 " id="agregar-codigo_inventario" data-bs-toggle="modal" data-bs-target="#codigoModal"></i>
                                                                <i class="c_pointer fas ti ti-search-off text-primary text-black-50  pe-none"  id="iconBuscar"></i>
                                                            </div>
                                                        </div>
                                                        <input type="search" class="pe-none bg-gray-200 form-control form-control-sm full-data" name="select_codigo" id="select_codigo" autocomplete="off">
                
                                                        <div id="sel_cod_msg" class="lh-1 m-0 position-absolute text-center text-danger" style="font-size: .7rem;" hidden="">Codigo no existe</div>
                                                        <div id="div_tabla_cod_buscar" class="d-none position-fixed" style="display: block; z-index:99;">
                                                            <table id="tabla_cod_buscar" class="table-striped table table-bordered table-hover bg-body m-0">
                                                                <tbody></tbody>
                                                            </table>
                                                            <span id="countBuscar"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-2 col-sm-auto form-group mb-3 mb-lg-1 mr-4 position-relative">
                                                        <label class="text-black-50 mb-0">Serial<span style="color:red;">*</span></label>
                                                        
                                                        <input id="serialCodigo" class="pe-none bg-gray-200 form-control form-control-sm full-data" type="search">
                                                        <div id="sel_serial_msg" class="lh-1 m-0 position-absolute text-center text-danger" style="font-size: .7rem;" hidden="">Serial No Registrado o No Disponible</div>
                                                        <div id="" class=" cantidad_msg3 lh-1 m-0 position-absolute text-center text-danger" style="font-size: .7rem;" hidden="">Campo Requerido</div>
                                                        <div id="sel_serial_msg1" class="  lh-1 m-0 position-absolute text-center text-danger" style="font-size: .7rem;" hidden="">debe ser mayor a 3 caracteres</div>
                                                    </div>
                                                    <div class="col-2 col-5 col-lg-1 col-sm-auto col-xl-1 form-group mb-3 mb-lg-1 mr-4 position-relative">
                                                        <label class="text-black-50 mb-0">Cantidad<span style="color:red;">*</span></label>
                                                        <input id="cantCodigo" class="pe-none bg-gray-200 form-control form-control-sm full-data" type="number" step="any" pattern="[0-9,.]+">
                                                        <div id="" class="cantidad_msg lh-1 m-0 position-absolute text-center text-danger" style="font-size: .7rem;" hidden="">Campo Requerido</div>
                                                    </div>
                                                    <div id="precioCont" class="col-2 col-5 col-sm-auto col-xl-2 form-group mb-3 mb-lg-1 ml-1 mr-4 position-relative">
                                                        <label class="text-black-50 mb-0">Precio<span style="color:red;">*</span></label>
                                                        <input id="precioCodigo" class="pe-none bg-gray-200 form-control form-control-sm full-data" type="number" step="any" pattern="[0-9,.]+">
                                                    </div>
                                                    <div class="col-2 col-5 col-sm-auto col-xl-2 form-group mb-3 mb-lg-1 mr-4 odc_cod position-relative d-none">
                                                        <label class="text-black-50 mb-0">ODC<span style="color:red;">*</span></label>
                                                        <input id="odcCodigo" class="pe-none bg-gray-200 form-control form-control-sm full-data" type="text">
                                                        <div id="odc_msg" class="cantidad_msg lh-1 m-0 position-absolute text-center text-danger" style="font-size: .7rem;" hidden="">Campo Requerido</div>
                                                    </div>
                                                    <div id="divInfoForm" class="bg-gray-200 mr-4 border-1 border-primary col-2 col-sm-auto col-xl-2 mb-4 ml-2 mt-2 mt-n5 rounded-3 tarjeta w-auto" style="min-width: 150px; height: 50px;">
                                                        <span class="tamano" id="select_descripcion"></span>
                                                        <br>
                                                        <span class="tamano" id="medidaCod"></span>
                                                        <br>
                                                        <span class="tamano" id="precioTarjet"></span>
                                                    </div>
                                                    <div class="col-6 col-md-3 col-sm-auto form-group mb-3 mb-lg-1 mr-5 mt-0 position-relative" id="div_obs_mat">
                                                        <label class="text-secondary mb-0">Observaciones</label>
                                                        <textarea class="pe-none bg-gray-200 form-control form-control-sm" name="obs_mat" rows="1" id="obsCodigo"></textarea>
                                                        <div id="odc_msg" class="cantidad_msg1 lh-1 m-0 position-absolute text-center text-danger" style="font-size: .7rem;" hidden="">Campo Requerido</div>
                                                        <div id="obs_mat-msg" class="d-block invalid-feedback lh-1 m-0 position-absolute text-center text-danger" style="font-size: .7rem;"></div>
                                                    </div>
                                                    <div class=" col-6 col-sm-6 d-flex form-group justify-content-lg-between mt-3">
                                                        <div>
                                                            <button type="submit" class="pe-none btn btn-primary" id="agregar_item">
                                                            Agregar</button>
                                                        </div>
                                                        <div class="ml-4 ml-lg-0">
                                                            <input type="button" class="btn-outline-primary form-control form-control-sm pe-none" id="InvetarioCount" value="0" style="font-weight: bold;">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>   
                            
                            <div class="ocultar_vertical_top" style="">
                                <div class="ocultar_panel " title="Ocultar panel">↑</div>
                            </div>
                        </div>
                        <div class="align-items-center d-flex flex-column justify-content-center resizer" data-direction="vertical">
                            <div class="draggable_horizontal"></div>
                        </div>
                        <div class="container__bottom">
                            <div class="d-none mostrar_vertical_top">
                                <div class="ocultar_panel" title="Mostrar panel">↓</div>
                            </div>
                            <div class="info-tables w-100">
                                <div class="row">
                                    <div id="div_pdf" class="col-12">
                                        <table class="table table-bordered table-sm text-center tb_materiales_mov" id="material_tb_materiales_mov">
                                            <thead class="bg-primary text-white">
                                                <tr>
                                                    <th>CÓDIGO</th>
                                                    <th>DESCRIPCIÓN</th>
                                                    <th>MEDIDA</th>
                                                    <th>CANTIDAD</th>
                                                    <th class="thDes">PRECIO</th>
                                                    <th>SERIAL</th>
                                                    <th class="thDes">ODC</th>
                                                    <th>OBSERVACIONES</th>
                                                    <th>ELIMINAR</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaPrinci">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="elementH"></div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex ">
                                        <div class="col-10 d-flex justify-content-center">
                                            <input type="button" class="pe-none btn-primary form-control form-control-sm text-uppercase mx-1 w-auto" id="movInvent" value="Entrega">
                                        </div>
                                        <div class="col-2 d-flex justify-content-center">
                                            <input type="button" class="pe-none btn-outline-secondary form-control form-control-sm text-uppercase w-auto mx-2 lim_mov" id="limpiarIvent" value="Limpiar">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- container__right -->
                    <div class="tab-pane fade " id="nav-Inventario" role="tabpanel" aria-labelledby="btn-card-invent">
                        <div id="saldos_tecnicos">
                            <table id="tabla_saldos"  class="datatable_div table_kardex table-hover table-sm w-100 table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Codigo</th>
                                        <th>Descripcion</th>
                                        <th>serial</th>
                                        <th>Cantidad</th>
                                        <th>Dev - Baja - Cobro</th>
                                        <th>Accion</th>
                                        <th>Guardar</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th class="tdh-l text-center" >Filter...</th> <!--  tdh-l -->
                                        <th class="tdh-s text-center" >Filter...</th> <!--  tdh-m -->
                                        <th class="tdh-s text-center" >Filter...</th> <!--  tdh-m -->
                                        <th class="tdh-s text-center" >Filter...</th> <!--  tdh-m -->
                                        <th class="tdh-s text-center" >Filter...</th> <!--  tdh-m -->
                                        <th class="tdh-s text-center" >Filter...</th> <!--  tdh-m -->
                                        <th class="tdh-s text-center" >Filter...</th> <!--  tdh-m -->
                                    </tr>
                                </tfoot>
                                <tbody></tbody>
                            </table>
                            <div class="mt-3 row">
                                <div class="d-flex justify-content-evenly">
                                    <div class="">
                                        <input type="button" class="pe-none c_pointer btn-primary form-control form-control-sm text-uppercase mx-1 w-auto" id="guar_reporte" value="Guardar">
                                    </div>
                                    <div class="">
                                        <input type="button" class="pe-none c_pointer btn-outline-secondary form-control form-control-sm text-uppercase w-auto mx-2 lim_mov" id="eli_reporte" value="Eliminar">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- inicio tab kardex -->
                    <div class="tab-pane fade " id="nav-kardex" role="tabpanel" aria-labelledby="btn-card-kardex">
                        <?php 
                                include "control_inventario_kardex.php";
                        ?>
                    </div>
                    <!-- fin tab kardex -->
                    <!-- inicio tab codigos -->
                    <div class="tab-pane fade " id="nav-codigos" role="tabpanel" aria-labelledby="btn-card-codigos">
                        <div id="">
                            <!-- table-responsive -->
                            <table id="tabla_codigos_bodega"  class=" datatable_div table_kardex table-hover table-sm w-100 table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <!-- <th>categoria</th> -->
                                        <th class="tdh-s">codigo</th>
                                        <th class="tdh-l">descripcion</th>
                                        <th class="tdh-s">subcategoria</th>
                                        <th class="tdh-s">tipo</th>
                                        <th class="tdh-s">medida</th>
                                        <th class="tdh-s">precio</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th class="tdh-s text-center" >Filter...</th> <!--  tdh-l -->
                                        <th class="tdh-l text-center" >Filter...</th> <!--  tdh-m -->
                                        <th class="tdh-s text-center" >Filter...</th> <!--  tdh-m -->
                                        <th class="tdh-s text-center" >Filter...</th> <!--  tdh-m -->
                                        <th class="tdh-s text-center" >Filter...</th>
                                        <th class="tdh-s text-center" >Filter...</th> 
                                    </tr>
                                </tfoot>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- fin tab codigos -->
                </div>
            </div>
        </div>
        <!-- modal permisos -->
        <div class="modal fade" id="modal_permi" tabindex="-1" aria-labelledby="modal_permi" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_permiLabel">Asignar Permisos</h5>
                        <button type="button" class="btn-close close_permi" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex mb-3">
                            <div class="col-12 p-0" id="inven_permisos" data-search-label="Asignar Permisos a:"></div>
                        </div>
                        <div id="perm_div" class="mb-3 mt-3 text-center">
                            <div id="">
                                <div id="msg_sede_perm" class="btn-group btn-group-toggle mt-1 " data-toggle="buttons">
                                    <label class="btn btn-sm btn-outline-secondary l_general_perm active">
                                        <input type="radio" name="general_perm" id="general_perm" class="rad_mat" value="Permisos Generales" autocomplete="off" checked="true">Permisos Generales
                                    </label>
                                    <label  class="btn btn-sm btn-outline-secondary l_sede_perm l_rad_mat">
                                        <input type="radio" name="sede_perm" id="sede_perm" class="rad_mat" value="Permisos de Sede" autocomplete="off" >Permisos de Sede
                                    </label>
                                </div>
                            </div>
                        </div>
                        <table id="tabla_asignar" class=" table_kardex   table d-none table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>Cédula</th>
                                    <th>Admin</th>
                                    <th>Gestor</th>
                                    <th>Lectura</th>
                                    <th>Guardar</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>'
                        <div class=" accordion my-2" id="perm_acordeon">
                            <div id="presonal_asignado" class="  accordion-item">
                                <h2 class="accordion-header" id="h2_personal">
                                    <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#div_personal" aria-expanded="true" aria-controls="div_personal">
                                        <div class=" d-flex justify-content-between w-100">
                                            <span id="person_gene_span">Personal Asignado General: </span>
                                            <div class="me-2">
                                                <span class="badge badge-success border" id="countPermi_gen">0</span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="div_personal" class="accordion-collapse collapse" data-parent="#perm_acordeon" aria-labelledby="h2_personal"> 
                                    <div class="accordion-body info-tables" style="max-height: 40vh;">
                                        <table id="tabla_personal_general" class="w-100 table table-bordered table-hover table-sm table-striped text-break text-center">
                                            <thead class="bg-success text-white">
                                                <tr>
                                                    <th>Cedula</th>
                                                    <th>Admin</th>
                                                    <th>Gestor</th>
                                                    <th>Lectura</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>  
                        <div class=" accordion my-2 d-none" id="perm_acordeon_sed">
                            <div id="presonal_asignado_sed" class="  accordion-item">
                                <h2 class="accordion-header" id="h2_personal_sed">
                                    <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#div_personal_sed" aria-expanded="true" aria-controls="div_personal_sed">
                                        <div class=" d-flex justify-content-between w-100">
                                            <span id="span_asig_sede">Personal Asignado por Sede: </span>
                                            <div class="me-2">
                                                <span class="badge badge-success border" id="count_sed">0</span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="div_personal_sed" class="accordion-collapse collapse" data-parent="#perm_acordeon_sed" aria-labelledby="h2_personal_sed"> 
                                    <div class="accordion-body info-tables" style="max-height: 40vh;">
                                        <table id="tabla_personal_sede" class="w-100 table table-bordered table-sm text-center table-hover table-striped ">
                                            <thead class="bg-success text-white">
                                                <tr>
                                                    <th>Cedula</th>
                                                    <th>Admin</th>
                                                    <th>Gestor</th>
                                                    <th>Lectura</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>             
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="cerrar_permi btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="d-none guardar_permi btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin modal permisos -->
        <!-- inicio modal aliado -->
        <div class="modal fade" id="add_aliado" data-bs-backdrop="static" aria-hidden="true" data-bs-keyboard="false" tabindex="-1" aria-labelledby="add_aliado" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary py-2 text-white">
                        <h5 class="modal-title" id="h2_add_aliado">Agregar aliado</h5>
                        <i id="close_aliado" class="fas fa-times fa-lg" data-bs-dismiss="modal" aria-label="Close"></i>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 border border-lg rounded-lg">
                            <form class="rounded-lg my-1 w-100" id="add_aliado_form">
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-12 col-sm-4">
                                        <label class="text-secondary mb-0">NIT:
                                        </label>
                                        <input class="aliado_class form-control full-data" type="text" id="add_aliado_nit" name="add_aliado_nit" placeholder="NIT aliado" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12 col-sm-8">
                                        <label class="text-secondary mb-0">Nombre Aliado:
                                        </label>
                                        <input class="aliado_class form-control full-data" type="text" id="add_aliado_name" name="add_aliado_name" placeholder="Nombre ó Razón social aliado" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12">
                                        <label class="text-secondary mb-0" >Dirección:
                                        </label>
                                        <input class="aliado_class form-control pe-4 full-data" type="address" id="add_aliado_addres" name="addressItem" placeholder="Dirección Aliado" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12 col-sm-4">
                                        <label class="text-secondary mb-0">Teléfono Contacto:
                                        </label>
                                        <input class="aliado_class form-control full-data" type="number" id="add_aliado_celu" name="add_aliado_celu" placeholder="Fijo / Móvil" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12 col-sm-8">
                                        <label class="text-secondary mb-0">Nombre Contacto:
                                        </label>
                                        <input class="aliado_class form-control full-data" type="text" id="add_aliado_contact" name="add_aliado_contact" placeholder="Nombre contacto" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12">
                                        <label class="text-secondary mb-0">Correo electrónico:
                                        </label>
                                        <input class="aliado_class form-control full-data" type="email" id="add_aliado_email" name="add_aliado_email" placeholder="Correo aliado ó contacto" autocomplete="off">
                                        <div id="add_aliado_email_msg" class="d-none error-msg fz-06 lh-1 m-0 pl-2 position-absolute text-danger">Correo Incorrecto</div>
                                    </div>
                                    <div class="col-12 my-3 d-flex align-items-end justify-content-center form-group">
                                        <div><input type="submit" class="btn-outline-primary form-control" id="btnAddNewItem" name="btnAddNewItem" aria-label="aliado" value="Agregar">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin modal aliado -->
        <!-- inicio modal saldos -->
        <div class="modal fade" id="modal_saldos" tabindex="-1" aria-labelledby="modal_saldos" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="mb-n2 mt-n2">
                            <h5 class="text-black-500 m-0" id="h5_mod_title"></h5>
                            <span class="text-black-500 m-0" id="span_mod_title"></span>
                        </div>
                        <button type="button" class="btn-close close_permi" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h3 id="cc_mod_h3" class="mt-n2"></h3>
                        <div class="col-12 border border-lg rounded-lg">
                            <form class="rounded-lg my-1 w-100" name="" id="form_sal_mod">
                                <div class="form-row justify-content-center border-5 border-bottom pb-2">
                                    <div class="form-group col-12 m-0">
                                        <span class="fw-bold mb-0" style="font-size: 0.9rem;">CÓDIGO:</span>
                                        <span id="span_mod_cod"></span>
                                    </div>
                                    <div class="form-group col-12 m-0">
                                        <span class="fw-bold mb-0" style="font-size: 0.8rem;">DESCRIPCIÓN:</span>
                                        <span id="span_mod_des"></span>
                                    </div>
                                    <div class="form-group col-12 m-0">
                                        <span class="fw-bold mb-0" style="font-size: 0.8rem;">SERIAL:</span>
                                        <span id="span_mod_ser"></span>
                                    </div>
                                </div>
                                <div class="d-flex form-row justify-content-center mt-3">
                                    <div class="form-group w-25">
                                        <label class="d-flex justify-content-between mb-0 text-secondary" for="">
                                            <span class=" mr-3" >Devolucion</span>
                                        </label>
                                        <input type="number" name="optradio" id="devolucion_sal" class="rad_opc form-control text-center form-control-sm" autocomplete="off" aria-label="">
                                    </div>
                                    <div class="form-group ml-5 mr-5 w-25">
                                        <label class="d-flex justify-content-between mb-0 text-secondary" for="">
                                            <span class=" mr-3" >Baja</span>
                                        </label>
                                        <input type="number" name="optradio" id="baja_sal" class="rad_opc form-control text-center form-control-sm" autocomplete="off" aria-label="">
                                    </div>
                                    <div class="form-group w-25">
                                        <label class="d-flex justify-content-between mb-0  text-secondary" for="utpConsumo">
                                            <span >Cobro:</span>
                                        </label>
                                        <input id="cobro_sal" name="optradio" type="number" class="rad_opc form-control text-center form-control-sm" autocomplete="off" aria-label="">
                                    </div>
                                </div>
                                <div class="form-row justify-content-center mb-3">
                                    <div class="form-group m-0 mb-3 mb-lg-1 mr-5 position-relative w-100" id="">
                                        <label class="text-secondary mb-0">Observaciones</label>
                                        <textarea class="form-control form-control-sm" name="" rows="1" id="obs_sal"></textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <input type="submit" class="btn-primary form-control form-control-sm text-uppercase w-auto m-1" id="guardar_sal_mod" value="Guardar">
                                    <input type="button" class="btn-outline-secondary form-control form-control-sm text-uppercase w-auto m-1" id="cancelar_saldo" data-bs-dismiss="modal" value="Cancelar">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin modal saldos -->
        <!-- inicio modal entrada -->
        <div class="modal fade" id="entradaCodigoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary py-2 text-white">
                        <h5 class="m-0">Importar Entrada</h5>
                        <i class="fas fa-times fa-lg c_pointer" data-bs-dismiss="modal" aria-label="Cerrar"></i>
                    </div>
                    <div class="modal-body"> 
                            
                        <div class="col-12 align-items-center  justify-content-between mb-0 p-0" id="">
                            
                            <form  id="entradaCodigo" class="m-0" >
                                <div class="border p-3 rounded-lg">
                                    <label for="entrada_csv_codigos">Selecionar archivo:</label>
                                    <input  name="entrada_csv_codigos" id="entrada_csv_codigos" type="file" class="form-control-file">
                                    
                                </div>
                                <input type="submit" class="btn btn-primary" hidden value="Guardar" id="guardarEntradaOculta">
                            </form>
                            <!-- ------------------INICIO Acordeón lectura archivo---------------- -->
                                <div class="d-none accordion my-2" id="entradaInventario">
                                    <div id="codigosNormales" class="d-none acordeonEntra accordion-item">
                                        <h2 class="accordion-header" id="h2CodigoOk">
                                            <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#divCodigoOk" aria-expanded="true" aria-controls="divCodigoOk">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="">Codigos Normales Correctos: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-success border" id="countCodigoOk">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divCodigoOk" class="accordion-collapse collapse" data-parent="#entradaInventario" aria-labelledby="h2CodigoOk"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tabla_t_ok" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-success text-white">
                                                        <tr>
                                                            <th>CÓDIGO</th>
                                                            <th>DESCRIPCIÓN</th>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="codigosSerializados" class="d-none acordeonEntra accordion-item">
                                        <h2 class="accordion-header" id="h2CodigoOkSerial">
                                            <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#divCodigoOkSerial" aria-expanded="true" aria-controls="divCodigoOkSerial">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="guardarEntraSerial">Codigos Serializados Correctos: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-success border" id="countCodigoOkSerial">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divCodigoOkSerial" class="accordion-collapse collapse" data-parent="#entradaInventario" aria-labelledby="h2CodigoOkSerial"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tabla_t_ok_serial" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-success text-white">
                                                        <tr>
                                                            <th>CÓDIGO</th>
                                                            <th>DESCRIPCIÓN</th>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="errorEscritura" class="d-none acordeonEntra accordion-item">
                                        <h2 class="accordion-header" id="h2Escritura">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divEscrituraErr" aria-expanded="true" aria-controls="divEscrituraErr">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Error de Escritura: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countEscritura">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divEscrituraErr" class="accordion-collapse collapse info-tables" data-parent="#entradaInventario" aria-labelledby="h2Escritura" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tablaEscrituraErr" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="celdasVacias" class="d-none acordeonEntra accordion-item">
                                        <h2 class="accordion-header" id="h2CeldaVac">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divCeldaVac" aria-expanded="true" aria-controls="divCeldaVac">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Celdas Vacias:</span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countCelVac">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divCeldaVac" class="accordion-collapse collapse info-tables" data-parent="#entradaInventario" aria-labelledby="h2CeldaVac" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tablaCelVac" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="codigosNoExi" class="d-none acordeonEntra accordion-item">
                                        <h2 class="accordion-header" id="h2CodigoExi">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divCodigoExi" aria-expanded="true" aria-controls="divCodigoExi">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Codigos no Existen:</span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countCodigoExi">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divCodigoExi" class="accordion-collapse collapse info-tables" data-parent="#entradaInventario" aria-labelledby="h2CodigoExi" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tablaCodigoExi" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CODIGO</th>
                                                            <th>CELDA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="serialExiste" class="d-none acordeonEntra accordion-item">
                                        <h2 class="accordion-header" id="h2SerialExis">
                                            <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#divSerialExis" aria-expanded="true" aria-controls="divSerialExis">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="spanSerialExis">Seriales Ya Existen En La Base: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-success border" id="countSerialExis">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divSerialExis" class="accordion-collapse collapse" data-parent="#entradaInventario" aria-labelledby="h2SerialExis"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tablaSerialExis" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-success text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="serialDupli" class="d-none acordeonEntra accordion-item">
                                        <h2 class="accordion-header" id="h2SerialesDupli">
                                            <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#divSerialesDupli" aria-expanded="true" aria-controls="divSerialesDupli">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="">Seriales Duplicados: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-success border" id="countSerialDupli">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divSerialesDupli" class="accordion-collapse collapse" data-parent="#entradaInventario" aria-labelledby="h2SerialesDupli"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tablaSerialesDupli" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-success text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="cantidadSerial" class="d-none acordeonEntra accordion-item">
                                        <h2 class="accordion-header" id="h2cantidadSerial">
                                            <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#divcantidadSerial" aria-expanded="true" aria-controls="divcantidadSerial">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="">Error En cantidad De Serializados: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-success border" id="countCantidaSer">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divcantidadSerial" class="accordion-collapse collapse" data-parent="#entradaInventario" aria-labelledby="h2cantidadSerial"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tablacantidadSerial" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-success text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>CODIGO</th>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- -----------------FIN Acordeón lectura archivo----------------- -->
                        </div> 
                    </div>
                    
                    <div class="modal-footer">
                        <div class="d-flex align-items-center import_mod me-auto w-25" style="width: fir-content;" id="">
                            <small class="d-flex lh-sm ms-1 order-1 w-50" style="font-size: 0.7em;">Descargar Plantilla</small>
                            <a class="d-flex justify-content-center text-center" style="cursor: pointer;" href="plantillas/entrada_inventario_codigos.csv" download="" id="" title="Descargar Plantilla">
                                <img onerror="this.src='img/export-csv.png'" src="img/export-csv.png" id="" class="border-success border-bottom-success img-thumbnail" alt="Plantilla" height="30px" width="30px">
                            </a>
                        </div>
                        <div class="w-auto">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin modal entrada -->
        <!-- inicio modal entrega -->
        <div class="modal fade" id="entregaCodigoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary py-2 text-white">
                        <h5 class="m-0">Importar Entrega</h5>
                        <i class="fas fa-times fa-lg c_pointer" data-bs-dismiss="modal" aria-label="Cerrar"></i>
                    </div>
                    <div class="modal-body"> 
                            
                        <div class="col-12 align-items-center  justify-content-between mb-0 p-0" id="">
                            
                            <form  id="entregaCodigo" class="m-0" >
                                <div class="border p-3 rounded-lg">
                                    <label for="">Selecionar archivo:</label>
                                    <input  name="entrega_csv_codigos" id="entrega_csv_codigos" type="file" class="form-control-file">
                                    
                                </div>
                                <input type="submit" class="btn btn-primary" hidden value="Guardar" id="guardarEntregaOculta">
                            </form>
                            <!-- ------------------INICIO Acordeón lectura archivo---------------- -->
                                <div class="d-none accordion my-2" id="entregaInventario">
                                    <div id="codigosNormalesEntre" class="d-none acordeonEntrega accordion-item">
                                        <h2 class="accordion-header" id="h2entregaOk">
                                            <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#divEntregaOk" aria-expanded="true" aria-controls="divEntregaOk">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="">Codigos Correctos: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-success border" id="countentregaOk">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divEntregaOk" class="accordion-collapse collapse" data-parent="#entregaInventario" aria-labelledby="h2entregaOk"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tabla_entre_ok" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-success text-white">
                                                        <tr>
                                                            <th>CÓDIGO</th>
                                                            <th>CEDULA</th>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="entreSerializados" class="d-none acordeonEntrega accordion-item">
                                        <h2 class="accordion-header" id="h2EntregaOkSerial">
                                            <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#divEntregaOkSerial" aria-expanded="true" aria-controls="divEntregaOkSerial">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="guardarEntraSerial">Serialiales Correctos: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-success border" id="countEntregaOkSerial">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divEntregaOkSerial" class="accordion-collapse collapse" data-parent="#entregaInventario" aria-labelledby="h2EntregaOkSerial"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tabla_entre_ok_serial" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-success text-white">
                                                        <tr>
                                                            <th>SERIAL</th>
                                                            <th>CEDULA</th>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="errorEscrituraEntre" class="d-none acordeonEntrega accordion-item">
                                        <h2 class="accordion-header" id="h2EscrituraEntre">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divEscrituraErrEntre" aria-expanded="true" aria-controls="divEscrituraErrEntre">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Error de Escritura: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countEscrituraente">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divEscrituraErrEntre" class="accordion-collapse collapse info-tables" data-parent="#entregaInventario" aria-labelledby="h2EscrituraEntre" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tablaEscrituraErrEntre" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="celdasVaciasEntre" class="d-none acordeonEntrega accordion-item">
                                        <h2 class="accordion-header" id="h2CeldaVacEntre">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divCeldaVacEntre" aria-expanded="true" aria-controls="divCeldaVacEntre">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Celdas Vacias:</span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countCelVacEntre">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divCeldaVacEntre" class="accordion-collapse collapse info-tables" data-parent="#entregaInventario" aria-labelledby="h2CeldaVacEntre" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tablaCelVacEntre" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="codigosNoExiEntre" class="d-none acordeonEntrega accordion-item">
                                        <h2 class="accordion-header" id="h2CodigoExientre">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divCodigoExiEntre" aria-expanded="true" aria-controls="divCodigoExiEntre">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Codigos no Existen:</span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countCodigoNoExi">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divCodigoExiEntre" class="accordion-collapse collapse info-tables" data-parent="#entregaInventario" aria-labelledby="h2CodigoExientre" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tablaCodigoNoExi" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CODIGO</th>
                                                            <th>CELDA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="cedulaNoExiEntre" class="d-none acordeonEntrega accordion-item">
                                        <h2 class="accordion-header" id="h2CedulaExientre">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divCedulaExiEntre" aria-expanded="true" aria-controls="divCedulaExiEntre">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Cedulas no Existen:</span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countCedulaNoExi">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divCedulaExiEntre" class="accordion-collapse collapse info-tables" data-parent="#entregaInventario" aria-labelledby="h2CedulaExientre" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tablaCedulaNoExi" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CEDULA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="serialNoExiste" class="d-none acordeonEntrega accordion-item">
                                        <h2 class="accordion-header" id="h2SerialNoExis">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divSerialNoExis" aria-expanded="true" aria-controls="divSerialNoExis">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="spanSerialExis">Seriales No Existen o No Disponibles: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countSerialNoExis">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divSerialNoExis" class="accordion-collapse collapse" data-parent="#entregaInventario" aria-labelledby="h2SerialNoExis"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tablaSerialNoExis" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="serialDupliEntre" class="d-none acordeonEntrega accordion-item">
                                        <h2 class="accordion-header" id="h2SerialesDupliEntre">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divSerialesDupliEntre" aria-expanded="true" aria-controls="divSerialesDupliEntre">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="">Seriales Duplicados: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countSerialDupliEntre">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divSerialesDupliEntre" class="accordion-collapse collapse" data-parent="#entregaInventario" aria-labelledby="h2SerialesDupliEntre"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tablaSerialesDupliEntre" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="cantidadSerialEntre" class="d-none acordeonEntrega accordion-item">
                                        <h2 class="accordion-header" id="h2cantidadSerialEntre">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divcantidadSerialEntre" aria-expanded="true" aria-controls="divcantidadSerialEntre">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="">Error En cantidad De Serializados: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countCantidaSerEntre">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divcantidadSerialEntre" class="accordion-collapse collapse" data-parent="#entregaInventario" aria-labelledby="h2cantidadSerialEntre"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tablacantidadSerialEntre" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>CODIGO</th>
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- -----------------FIN Acordeón lectura archivo----------------- -->
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center import_mod me-auto w-25" style="width: fir-content;" id="">
                            <small class="d-flex lh-sm ms-1 order-1 w-50" style="font-size: 0.7em;">Descargar Plantilla</small>
                            <a class="d-flex justify-content-center text-center" style="cursor: pointer;" href="plantillas/entrega_inventario_codigos.csv" download="" id="" title="Descargar Plantilla">
                                <img onerror="this.src='img/export-csv.png'" src="img/export-csv.png" id="" class="border-success border-bottom-success img-thumbnail" alt="Plantilla" height="30px" width="30px">
                            </a>
                        </div>
                        <div class="w-auto">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin modal entrega -->
        <!-- inicio modal import aliado -->
        <div class="modal fade" id="tras_aliado_mod" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary py-2 text-white">
                        <h5 class="m-0">Importar Traslado</h5>
                        <i class="fas fa-times fa-lg c_pointer" data-bs-dismiss="modal" aria-label="Cerrar"></i>
                    </div>
                    <div class="modal-body"> 
                            
                        <div class="col-12 align-items-center  justify-content-between mb-0 p-0" id="">
                            <form id="" class="m-0">
                                <div class="border p-3 rounded-lg">
                                    <label for="">Selecionar archivo:</label>
                                    <input name="traslado_csv_aliado" id="traslado_csv_aliado" type="file" class="form-control-file">
                                    
                                </div>
                            </form>
                            <!-- ------------------INICIO Acordeón lectura archivo---------------- -->
                                <div class="d-none accordion my-2" id="tras_otro_aliado">
                                    <div id="errorEscritura_tras" class="d-none  accordion-item">
                                        <h2 class="accordion-header" id="h2Escritura_tras">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divEscrituraErr_tras" aria-expanded="true" aria-controls="divEscrituraErr_tras">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Error de Escritura: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countEscritura_tras">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divEscrituraErr_tras" class="accordion-collapse collapse info-tables" data-parent="#tras_otro_aliado" aria-labelledby="h2Escritura_tras" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tablaEscritura_tras" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="celdasVacias_tras" class="d-none  accordion-item">
                                        <h2 class="accordion-header" id="h2CeldaVac_tras">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divCeldaVac_tras" aria-expanded="true" aria-controls="divCeldaVac_tras">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Celdas Vacias:</span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="countCelVac_tras">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divCeldaVac_tras" class="accordion-collapse collapse info-tables" data-parent="#tras_otro_aliado" aria-labelledby="h2CeldaVac_tras" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tablaCelVac_tras" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="serial_nexi_tras" class="d-none  accordion-item">
                                        <h2 class="accordion-header" id="h2_nexi">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#div_ser_nexi" aria-expanded="true" aria-controls="div_ser_nexi">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Seriales no Existen:</span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="count_ser_nexi">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="div_ser_nexi" class="accordion-collapse collapse info-tables" data-parent="#tras_otro_aliado" aria-labelledby="h2_nexi" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tabla_ser_nexi" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="serial_dupli_tras" class="d-none  accordion-item">
                                        <h2 class="accordion-header" id="h2_dupli">
                                            <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#div_ser_dupli" aria-expanded="true" aria-controls="div_ser_dupli">
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>Seriales Duplicados:</span>
                                                    <div class="me-2">
                                                        <span class="badge badge-danger border" id="count_ser_dupli">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="div_ser_dupli" class="accordion-collapse collapse info-tables" data-parent="#tras_otro_aliado" aria-labelledby="h2_dupli" style="max-height: 40vh;"> 
                                            <div class="accordion-body">
                                                <table id="tabla_ser_dupli" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-danger text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>ERROR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tras_ser_div" class="d-none  accordion-item">
                                        <h2 class="accordion-header" id="h2tras_ser">
                                            <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#divtras_ser" aria-expanded="true" aria-controls="divtras_ser">
                                                <div class=" d-flex justify-content-between w-100">
                                                    <span id="">Seriales a Trasladar: </span>
                                                    <div class="me-2">
                                                        <span class="badge badge-success border" id="countCant_tras_ser">0</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="divtras_ser" class="accordion-collapse collapse" data-parent="#tras_otro_aliado" aria-labelledby="h2tras_ser"> 
                                            <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                <table id="tabla_tras_ser_guar" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                    <thead class="bg-success text-white">
                                                        <tr>
                                                            <th>CELDA</th>
                                                            <th>SERIAL</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- -----------------FIN Acordeón lectura archivo----------------- -->
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center import_mod me-auto w-25" style="width: fir-content;" id="">
                            <small class="d-flex lh-sm ms-1 order-1 w-50" style="font-size: 0.7em;">Descargar Plantilla</small>
                            <a class="d-flex justify-content-center text-center" style="cursor: pointer;" href="plantillas/plantilla_traslado_seriales.csv" download="" id="" title="Descargar Plantilla">
                                <img onerror="this.src='img/export-csv.png'" src="img/export-csv.png" id="" class="border-success border-bottom-success img-thumbnail" alt="Plantilla" height="30px" width="30px">
                            </a>
                        </div>
                        <div>
                            <button type="button" class="d-none btn btn-primary" data-bs-dismiss="modal" id="agregar_tras">
                            Agregar</button>
                        </div>
                        <div class="w-auto">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin modal import aliado -->
    <!-- inicio del modal de codigo -->
        <div class="modal fade" id="codigoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary py-2 text-white ">
                        <h5 id="h5_cod_mod" class="mb-0">Agregar codigo</h5>
                        <i id="i_close" class="cerrr_Modcod fas fa-times fa-lg c_pointer" data-bs-dismiss="modal" aria-label="Close"></i>
                    </div>
                    <div class="modal-body"> 
                            <!-- <div class="col-12 align-items-center d-inline-flex justify-content-end mb-0 p-0" id="ocultarImport"> -->
                            <div class="col-12 align-items-center d-inline-flex justify-content-between mb-0 p-0" id="ocultarImport">
                                <div class="border border-2 d-flex justify-content-center my-1 ms-auto p-1 position-relative rounded-3 cambio_activ" style="width: fit-content; margin-top: -29px;">
                                    <small class="pt-4 px-1 text-muted rounded">Importar archivo</small>
                                    <i class="fa-upload fas fs-4 text-primary position-absolute c_pointer" id="addImport"></i>
                                </div>
                            </div> 
                        <div class="col-12 border border-lg rounded-lg" id="modalCodigo">
                            <form class="rounded-lg my-1 w-100" name="addNewItem_form" id="form_mod_cod">    
                                    <div class="form-row justify-content-between">
                                        <div class="form-group col-12 col-sm-4 col-lg-3">
                                            <label class="text-secondary mb-0" for="codItem">Código:</label>
                                            <input type="search" class="vaciar_form form-control validate-form" id="codItem" name="codItem" placeholder="Cód. material">
                                            <div id="codItem_material-msg" class="d-block invalid-feedback lh-1 m-0 position-absolute text-center text-danger" style="font-size: 0.7rem;">
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-8 col-lg-6">
                                            <label class="text-secondary mb-0" for="descripItem_material">Descripción:</label>
                                            <input type="search" class="vaciar_form form-control validate-form" id="descripItem_material" name="descripItem_material" placeholder="Descripción material">
                                            <div id="descripItem_material-msg" class="d-block invalid-feedback lh-1 m-0 position-absolute text-center text-danger" style="font-size: 0.7rem;">
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-4 col-lg-3">
                                            <label class="text-secondary mb-0" for="precioItem">Precio:</label>
                                            <input type="number" class="vaciar_form form-control validate-form" id="precioItem" name="precioItem" placeholder="Precio material">
                                            <div id="codItem_material-msg" class="d-block invalid-feedback lh-1 m-0 position-absolute text-center text-danger" style="font-size: 0.7rem;">
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-lg-3">
                                            <label class="text-secondary mb-0" for="medida_material">Medida:</label>
                                            <select id="medida_material" name="medItem_material" class="form-control form-select validate-form">
                                                <option value="-1" disabled="">Seleccionar...</option>
                                                <option  value="UND">UNIDADES</option>
                                                <option  value="MTR">METROS</option>
                                                <option  value="CM">CENTÍMETROS</option>
                                                <option  value="PAQ">PAQUETES</option>
                                            </select>
                                            <div id="medida_material-msg" class="d-block invalid-feedback lh-1 m-0 position-absolute text-center text-danger" style="font-size: 0.7rem;">
                                            </div>
                                        </div>
                                        <!-- <div class="d-flex">
                                            <div class="mr-5">
                                                <div class="align-items-center ml-2 d-flex flex-column mr-5">
                                                    <div class="d-grid">
                                                        <label class="form-check-label text-secondary mb-0" for="item_cod_serial">Tiene Serial?</label>
                                                        <label class="toggle-switchy" for="item_cod_serial" data-size="lg">
                                                                <input type="checkbox" id="item_cod_serial" name="item_cod_serial" data-nonchecked="bg-danger">
                                                            <span class="toggle fw-bold default-size" data-before="Si" data-after="No" data-nonchecked="--non-checked-red" style="box-shadow: rgba(0, 119, 200, 0.5) 0px 0px 5px 3px;">
                                                                <span class="switch d-flex align-items-center justify-content-center">
                                                                    <i class="fas text-gray-500 fa-arrow-right " id="ir_serial"></i>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-1 ml-5 d-none">
                                                <label for="" class="b w-auto text-success">Areas:</label>
                                                <div id="ope_admin" class="btn-group-toggle btn-group-xs  mb-1 p-1 rounded-3 w-50 d-flex">
                                                    <label id="mostrar_area" style="width:max-content;" class="ml-1 btn btn-sm btn-outline-success mb-1 color_container p-2">Operativa</label>
                                                    <label id="admin_area" style="width:max-content;" class="ml-1 btn btn-sm btn-outline-success mb-1 color_container p-2 ">Administrativa</label>
                                                </div>
                                                <div id="input_area" style="height:100px;" class="border btn-group-toggle btn-group-xs col-12 justify-content-evenly mb-1 p-1 rounded-3 overflow-auto d-none">
                                                </div>
                                                <div id="input_area_admin" style="height:100px;" class="border btn-group-toggle btn-group-xs col-12 justify-content-evenly mb-1 p-1 rounded-3 overflow-auto w-50 d-none">
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="d-flex">
                                            <div class="mr-5">
                                                <div class="align-items-center ml-2 d-flex flex-column mr-5">
                                                    <div class="d-grid">
                                                        <label class="form-check-label text-secondary mb-0" for="item_cod_serial">Tiene Serial?</label>
                                                        <label class="toggle-switchy" for="item_cod_serial" data-size="lg">
                                                                <input type="checkbox" id="item_cod_serial" name="item_cod_serial" data-nonchecked="bg-danger">
                                                            <span class="toggle fw-bold default-size" data-before="Si" data-after="No" data-nonchecked="--non-checked-red" style="box-shadow: rgba(0, 119, 200, 0.5) 0px 0px 5px 3px;">
                                                                <span class="switch d-flex align-items-center justify-content-center">
                                                                    <i class="fas text-gray-500 fa-arrow-right " id="ir_serial"></i>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-1 ml-5">
                                                <label for="" class="b w-auto text-success ml-5">Areas: <span id="sp_gen" class="text-primary">[General]</span></label>
                                                <div id="ope_admin" class="btn-group-toggle btn-group-xs  mb-1 p-1 rounded-3 w-50 d-flex">
                                                    <label id="mostrar_area" style="width:max-content;" class="ml-1 btn btn-sm btn-outline-success mb-1 color_container p-2 active">Operativa</label>
                                                    <label id="admin_area" style="width:max-content;" class=" mr-2 ml-1 btn btn-sm btn-outline-success mb-1 color_container p-2">Administrativa</label>
                                                    <i id="add_delete" class="c_pointer fa-sharp fa-solid fa-circle-check text-success"></i>
                                                </div>
                                                <div id="input_area" style="height:100px;" class="border btn-group-toggle btn-group-xs col-12 justify-content-evenly mb-1 p-1 rounded-3 overflow-auto w-50">
                                                </div>
                                                <div id="input_area_admin" style="height:100px;" class="border btn-group-toggle btn-group-xs col-12 justify-content-evenly mb-1 p-1 rounded-3 overflow-auto w-50 d-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Guardar" hidden id="guadarCodigo">
                                </div>
                            </form>
                        </div>
                        <div class="d-none" id="modalImp">
                            <div class="register">
                                <div class="col-12 align-items-center d-inline-flex justify-content-between mb-0 p-0">
                                    <h5 class="m-2 mb-0 text-primary">Importar CSV</h5>
                                    <div class="border border-2 d-flex justify-content-center my-1 ms-auto p-1 position-relative rounded-3 cambio_activ">
                                        <small class="pt-4 px-1 text-muted rounded">Agregar código</small>
                                        <i class="fa-plus-circle fas fs-4 blueHover text-primary position-absolute c_pointer" id="addModalC"></i>
                                    </div>
                                </div>
                                <div id="">
                                    <form  id="importar_codigo" class="m-0" name="importar_codigo">
                                        <div class="border p-3 rounded-lg">
                                            <label for="import_csv_codigos">Selecionar archivo:</label>
                                            <input name="import_csv_codigos" id="import_csv_codigos" type="file" class="form-control-file">
                                            <input type="submit" style ="" class="submit-green" hidden name="envImport" id="envImport" value="Cargar archivo">
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary" value="Importar" hidden id="imporCodigo">
                                        </div>
                                    </form>
                                    <!-- ------------------INICIO Acordeón lectura archivo---------------- -->
                                    <div class="d-none accordion my-2" id="importInventario">
                                        <div id="imp_cod_guar" class="d-none accordion-item">
                                            <h2 class="accordion-header" id="h2CodigoImportOk">
                                                <button class="accordion-button fw-bold text-success collapsed" type="button" data-toggle="collapse" data-target="#divCodigoImportOk" aria-expanded="true" aria-controls="divCodigoImportOk">
                                                    <div class="d-flex justify-content-between w-100">
                                                        <span id="codGuardados">Codigos Correctos: </span>
                                                        <div class="me-2">
                                                            <span class="badge badge-success border" id="countCodigoimportOk">0</span>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="divCodigoImportOk" class="accordion-collapse collapse" data-parent="#importInventario" aria-labelledby="h2CodigoImportOk"> 
                                                <div class="accordion-body info-tables" style="max-height: 40vh;">
                                                    <table id="tabla_import_ok" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                        <thead class="bg-success text-white">
                                                            <tr>
                                                                <th>CÓDIGO</th>
                                                                <th>DESCRIPCIÓN</th>
                                                            </tr>
                                                            
                                                        </thead>
                                                        <tbody>
                                                            
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="imp_es" class="d-none accordion-item">
                                            <h2 class="accordion-header" id="h2EscrituraImp">
                                                <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divEscrituraErrImp" aria-expanded="true" aria-controls="divEscrituraErrImp">
                                                    <div class="d-flex justify-content-between w-100">
                                                        <span>Error de Escritura: </span>
                                                        <div class="me-2">
                                                            <span class="badge badge-danger border" id="countEscrituraImp">0</span>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="divEscrituraErrImp" class="accordion-collapse collapse info-tables" data-parent="#importInventario" aria-labelledby="h2EscrituraImp" style="max-height: 40vh;"> 
                                                <div class="accordion-body">
                                                    <table id="tablaEscrituraErrImp" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                        <thead class="bg-danger text-white">
                                                            <tr>
                                                                <th>CELDA</th>
                                                                <th>ERROR</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="imp_cel" class="d-none accordion-item">
                                            <h2 class="accordion-header" id="h2CeldaVacImp">
                                                <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divCeldaVacImp" aria-expanded="true" aria-controls="divCeldaVacImp">
                                                    <div class="d-flex justify-content-between w-100">
                                                        <span>Celdas Vacias:</span>
                                                        <div class="me-2">
                                                            <span class="badge badge-danger border" id="countCelVacImp">0</span>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="divCeldaVacImp" class="accordion-collapse collapse info-tables" data-parent="#importInventario" aria-labelledby="h2CeldaVacImp" style="max-height: 40vh;"> 
                                                <div  class="accordion-body">
                                                    <table id="tablaCelVacImp" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                        <thead class="bg-danger text-white">
                                                            <tr>
                                                                <th>CELDA</th>
                                                                <th>ERROR</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="cod_imp" class="d-none accordion-item">
                                            <h2 class="accordion-header" id="h2CodigoExiImp">
                                                <button class="accordion-button fw-bold text-danger collapsed" type="button" data-toggle="collapse" data-target="#divCodigoExiImp" aria-expanded="true" aria-controls="divCodigoExiImp">
                                                    <div class="d-flex justify-content-between w-100">
                                                        <span>Codigos ya Existen:</span>
                                                        <div class="me-2">
                                                            <span class="badge badge-danger border" id="countCodigoExiImp">0</span>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="divCodigoExiImp" class="accordion-collapse collapse info-tables" data-parent="#importInventario" aria-labelledby="h2CodigoExiImp" style="max-height: 40vh;"> 
                                                <div class="accordion-body">
                                                    <table id="tablaCodigoExiImp" class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5">
                                                        <thead class="bg-danger text-white">
                                                            <tr>
                                                                <th>CODIGO</th>
                                                                <th>CELDA</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- -----------------FIN Acordeón lectura archivo----------------- -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center import_mod me-auto w-25 d-none" style="width: fir-content;" id="plan_imp_cod">
                            <small class="d-flex lh-sm ms-1 order-1 w-50" style="font-size: 0.7em;">Descargar Plantilla</small>
                            <a class="d-flex justify-content-center text-center" style="cursor: pointer;" href="plantillas/importar_codigos_inventario.csv" download="" id="desc_plan_cod" title="Descargar Plantilla">
                                <img onerror="this.src='img/export-csv.png'" src="img/export-csv.png" id="" class="border-success border-bottom-success img-thumbnail" alt="Plantilla" height="30px" width="30px">
                            </a>
                        </div>
                        <div class="w-auto">
                            <button type="button" class="btn btn-primary import_mod" id="guardar_cod">Guardar</button>
                            <button type="button" class="btn btn-primary import_mod pe-none d-none" id="import_cod" disabled>Importar</button>
                            <button type="button" class="cerrr_Modcod btn btn-secondary" data-bs-dismiss="modal" id="">Cerrar</button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    <!-- fin del modal de codigo -->

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="h4_cate text-white">Crear o Editar Categoria</h4>
                    <button type="button" class="btn-close close-edit" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="familia">
                    <div class="modal-body" >
                            <div class="d-flex justify-content-between">
                                <a  data-bs-toggle="dropdown"  data-parent="#sidebar" aria-expanded="false">
                                    <div class="div_cate">
                                        <i class="fas fa-cubes"></i> <span class="text-uppercase" id="">Categorias</span>  
                                    </div>
                                </a>
                                <ul class="dropdown-menu " id="sel_category">
                                </ul>
                                
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="familia_elemento" >Categoria:</label>
                                <input id="familia_elemento" class="form-control form-control-sm" type="search" name="familia_elemento" autofocus>
                                <div id="categoria_msg" class="lh-1 m-0 position-absolute text-center text-danger" style="font-size: .7rem;" hidden>Campo Requerido</div>
                            </div>
                        <div class="row ">                                           
                                <label for="sub_familia">Sub Categoria:</label>
                            <div class=" col d-flex justify-content-between">
                                <div class="form-group ">
                                    <input id="sub_familia" class="form-control form-control-sm txt_sub_familia" type="search" name="sub_familia">                    
                                </div>
                                <div class="form-group col ">
                                    <i id="btn_add_sub_cat" class="fas fa-check-circle ml-3 text-success"></i>
                                </div>
                            </div>
                        </div>
                            <div class="bg-gray-300 d-flex flex-wrap gap-1 p-2 rounded-3" id="fila_subcate">
                            </div>
                        <div class="row" style="margin-top:20px;">
                                <label for="inp_add_sede" >Sedes:</label>
                            <div class="col d-flex justify-content-between">
                                <div class="form-group ">
                                    <input id="inp_add_sede" class="form-control form-control-sm" type="search" name="inp_add_sede">
                                    <!-- <div id="sede_msg" class="lh-1 m-0 position-absolute text-center text-danger" style="font-size: .7rem;" hidden>Campo Requerido, Para Agregar una Sede Presione el Boton Verde</div> -->
                                </div>  
                                <div class="form-group col ">
                                    <i id="btn_add_sedes" class="fas fa-check-circle ml-3 text-success"></i>
                                </div>
                            </div>
                        </div>   
                                <div class="bg-gray-300 d-flex flex-wrap gap-1  p-2 rounded-3" id="fila_sedes">
                                </div>          
                        <!-- <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Agregar" hidden id = "btn_enviar">
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btn_env_revaluar">Guardar</button>
                        <button type="button" class="btn btn-secondary" id="btn_cerrar_modal" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- inicio del modal de busqueda -->
    
    <div class="modal fade" id="buscarModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Descripción</h5>
                    <button type="button" class="cerrarB btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group col ">
                        <label>Buscar:</label>
                        <input id="buscarDes" class="form-control form-control-sm" type="search" name="buscarDes">
                        
                    </div>
                    <table class="table table-bordered table-sm text-center table-hover table-striped w-75 ml-5" id="tablaBuscar">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>CÓDIGO</th>
                                <th>DESCRIPCIÓN</th>
                                <th>TIPO</th>
                                <th>PRECIO</th>
                            </tr>
                        </thead>
                        <tbody id="bodyBuscar"></tbody>
                    </table>
                    
                </div>
                <div class="modal-footer">
                    <button type="button"  class="cerrarB btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- fin del modal de busqueda -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.24/b-1.7.0/b-html5-1.7.0/b-print-1.7.0/fc-3.3.2/fh-3.1.8/sl-1.3.3/datatables.min.css" />
    <script src="js/datatables.min.js"></script>
    <!-- fin offcanvas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="js/popper.min.js" defer></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.24/b-1.7.0/b-html5-1.7.0/b-print-1.7.0/fc-3.3.2/fh-3.1.8/sl-1.3.3/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
    <script src="js/convertir.js"></script>
    <script>
        let dataCate = [];
        
        let data_sal_arr = [];
        let codigoIcon = '';
        let data_sal_mod = {
            cedula:'',
            codigo:'',
            serial:'',
            devolucion:'',
            baja:'',
            cobro:'',
            observaciones:''
        };
        let dataSaldos = [];
        let data_bodega = [];
        let data_bodega_all = [];
        let bodega_kardex = [];
        let dataSalTotal = [];
        let cc_saldo = '';
        const data_perm = [];
        new searchPersonal('inven_permisos');
        let cate_event = null;  
        let exis_btn = false;
        let info = '';
        let objCodExi = {};
        let objTabla = {};
        // let objCate = {};
        let saveCodigo ={
            codigo:"",
            descripcion:"",
            precio:"",
            medida:"",
            tipo:"NORMAL",
            categoria : "",
            subcategoria : "",
            sede:"",
            area:[]
        }
        let result_aliado = [];
        let dat_ser1 = '';
        cod_ser_kar = {codigo:'',serial:''};
        let data_cod_cate = [];
        // let datosCate = [];
        let today = new Date();
        let workData ={
            categoria:"",
            sub_cate:"",
            sede:"",
            movimiento:"",
            cedula:"",
            fecha:document.getElementById('fecha_movimiento').value,
            ticket:document.getElementById('ticketPerson').value
        }
        let dat_nor1 = '';
        let valData = {};
        let valSerial ={};
        let valNormal = {};
        let codTabla = {
            cedula:"",
            categoria:"",
            sub_cate:"",
            sede:"",
            movimiento:"",
            datosTabla:[],
            fecha:document.getElementById('fecha_movimiento').value,
            ticket:document.getElementById('ticketPerson').value
        };
        
        let saveDataCate = {
            categoria : "", 
            sub_cate : [],
            sedes : [],
            movimientos : [
                "entrega", 
                "entrada",
                "devolucion",
                "baja",
                "mantenimiento",
                "traslado_otro_aliado"
            ] 
        };
        let con1 = false;
        let msg_fam = '';
        let mod_cod_inven = document.getElementById('agregar-codigo_inventario');
        
        let dataAliado = [];
        let dataNormal = [];
        let dataSerial = [];
        let dataSerialCsv = [];
        let dataEntra = [];
        let dataImport = [];
        let ser1 = '';
        let cod1 = '';
        function getFamilias(){
            document.getElementById('loader').style.display = 'block';
            pro_ajax = $.ajax({
                url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                method:'POST',
                data: {
                    get_familia: ''
                },
                dataType : "json",
                success: function(data){
                    dataCate = data;//
                    if(!!dataCate.length){
                        document.getElementById('div_cate_data').classList.remove('pe-none');
                        createSelFam('sel_fam');
                    }
                    else{

                    }
                    document.getElementById('loader').style.display = 'none';
                },
                error: function( jqXHR, textStatus, errorThrown ) {
                    document.getElementById('loader').style.display = 'none';
                    error_ajax( jqXHR, textStatus, errorThrown );
                }
            });
        };
        getFamilias();
        function crear_tarjeta_mov(cedula, idElem, isTrasl = false){
            let contenedor_tarjeta = document.getElementById(`tarjeta_${idElem}`);
            contenedor_tarjeta.innerHTML = '';
            if(idElem == 'tecnico_recibe'){
                document.getElementById('arrow_trasl').classList.remove('d-none');
            }

            let { nombre, apellido, cargo, movil, area, foto } = jsonAllPer.find(per => per.cedula == cedula);
            let nombreC = `${nombre} ${apellido}`;	

            let div_tarjeta = document.createElement('div');
            div_tarjeta.className = `d-flex justify-content-center ${isTrasl ? 'p-2' : 'row'} ${idElem == 'tecnico_recibe' ? 'flex-row-reverse' : ''} mt-1`;

            let img = document.createElement('img');
            img.className = 'border img-profile p-1 rounded-circle m-auto';
            img.src = foto;
            img.style.width = isTrasl ? '60px' : '90px';
            img.style.height = isTrasl ? '60px' : '90px';
            img.alt = 'Foto tecnico';
            img.onerror = "this.src = 'photos/Defaul.png'";
            img.onclick = ( () => zoom_img_ruta(foto, nombreC) );
            img.style.cursor = 'pointer';

            let div_textos = document.createElement('div');
            div_textos.className = 'b col-8 col-sm-9 p-0 text-white';
            if(!!document.getElementById('btn-card-kardex').classList.contains('active')){
                div_textos.className = 'b col-8 col-sm-9 p-0 text-primary';
            }
            else{
                div_textos.className = 'b col-8 col-sm-9 p-0 text-white';
            }
            // div_textos.style.fontSize = (movimiento == 'traslados_tecnicos') ? '.6rem' : '.7rem';

            let text_cedula = document.createElement('p');
            text_cedula.className =  'm-0 text-center';
            text_cedula.textContent = cedula;

            let text_cargo = document.createElement('p');
            text_cargo.className =  'm-0 text-center';
            text_cargo.textContent = `${cargo} ${movil ? `- ${movil}` : ''}`;

            let text_area = document.createElement('p');
            text_area.className =  'm-0 text-center';
            text_area.textContent = area;

            div_tarjeta.appendChild(img);
            div_textos.appendChild(text_cedula);
            div_textos.appendChild(text_cargo);
            div_textos.appendChild(text_area);
            div_tarjeta.appendChild(div_textos);
            contenedor_tarjeta.appendChild(div_tarjeta);
        }
        function iconMostrar(){
            document.getElementById('iconBuscar').classList.remove('ti');
            document.getElementById('iconBuscar').classList.add('fa-search');
            document.getElementById('iconBuscar').classList.remove('text-black-50');
            document.getElementById('iconBuscar').classList.add('text-primary');
            document.getElementById('agregar-codigo_inventario').classList.remove('pe-none');
            document.getElementById('agregar-codigo_inventario').classList.remove('text-gray-600');
            document.getElementById('agregar-codigo_inventario').classList.add('text-primary');
        }
        function dbTarjeta(id){
            document.getElementById(`tarjeta_${id}`).innerHTML = '';
            workData.cedula = "";
            codTabla.cedula = "";
            setTimeout(() => {
                let ced = document.getElementById(`cc_${id}`).value;
                if(ced != ''){
                    crear_tarjeta_mov(ced, id, true);
                    workData.cedula = ced;

                    codTabla.cedula = ced;
                    document.getElementById('select_codigo').classList.remove('pe-none');
                    document.getElementById('select_codigo').classList.remove('bg-gray-200');
                    iconMostrar();
                    if(workData.movimiento != 'entrada'){
                        document.getElementById('serialCodigo').classList.remove('pe-none');
                        document.getElementById('serialCodigo').classList.remove('bg-gray-200');
                        setTimeout(() => {
                            document.getElementById('serialCodigo').focus();
                        }, 100);
                        
                    }

                }else{
                    document.getElementById('arrow_trasl').classList.add('d-none');
                    redload();
                }
            }, 1000);
        };
        function addPermiMod(){
            document.querySelector('#tabla_asignar tbody').innerHTML = '';
            document.querySelector('.l_sede_perm').addEventListener('click', e =>{
                if(workData.sede == ''){
                    new createDivMsg('msg_sede_perm', `Para Asignar Permisos de Sede Debe Elegir una Sede`);
                    setTimeout(() => document.querySelector('.l_general_perm').click(),100)
                    
                    document.querySelector('.guardar_permi').setAttribute('disabled','')
                }
                else{
                    !!document.getElementById('msg_sede_perm-msg') && document.getElementById('msg_sede_perm-msg').remove();
                    document.querySelector('#tabla_asignar tbody').innerHTML = '';
                    addPermiCat('sede')
                }
            })
            document.querySelector('.l_general_perm').addEventListener('click', e =>{ 
                document.querySelector('#tabla_asignar tbody').innerHTML = '';
                addPermiCat('general');
            });
        }
        function createSelFam(id){
            let contedor = document.querySelector(`#sel_category`);            
            let lista = '';
            contedor.innerHTML = "";
            let new_li = '';
            let new_button = '';
            let new_li_sub = '';
            let new_a = '';
            let new_ul = '';
            let new_ul_cate = document.getElementById('sel_fam');
            new_ul_cate.innerHTML ='';
            [...dataCate].map( fam => {
                let dat = fam.parametros;
                const p_man_sel = "<?php echo $permi_manual ?>"  
                let cedula_admin = "<?php echo $_SESSION['cedula'] ?>"
                let perm_admin = dat.permisos.admin.includes(cedula_admin);
                let perm_edit = dat.permisos.editor.includes(cedula_admin);
                let perm_leer = dat.permisos.lector.includes(cedula_admin);
                let perm_admin_sede = dat.permisos.admin.includes(cedula_admin);
                let perm_edit_sede = dat.permisos.editor.includes(cedula_admin);
                let perm_leer_sede = dat.permisos.lector.includes(cedula_admin);
                if(!!perm_admin || !!perm_edit || !!perm_leer){
                    if (dat.sub_cate == "" || id == "sel_category") {
                        new_li = document.createElement('li');
                        new_button = document.createElement('button');
                        new_button.setAttribute('type', 'button');
                        new_button.setAttribute('aria-label','fa-cubes');
                        new_button.classList.add('dropdown-item', 'sel_param');
                        new_button.name = 'familia_tab';
                        new_button.textContent = `${dat.categoria.toCapitalizeCase()}`;
                        new_button.setAttribute('data-category', `${dat.categoria}`);
                        new_li.appendChild(new_button);
                        if(id == "sel_category"){
                            contedor.appendChild(new_li);
                            // new_ul_cate = '';
                        }
                        else{
                            new_ul_cate.appendChild(new_li);
                        }
                    }
                    else if(id == "sel_fam"){
                        new_ul_cate.classList.add('dropdown-menu');
                        new_li = document.createElement('li');
                        new_li.classList.add( 'submenu_bod', 'men_su_b');
                        new_a = document.createElement('a');
                        new_a.classList.add('sel_param', 'd-flex', 'justify-content-between', 'dropdown-item');
                        new_a.setAttribute('data-bs-auto-close', 'outside');
                        new_a.setAttribute('aria-expanded', 'false') ;
                        new_a.setAttribute('data-category', `${dat.categoria}`);
                        new_a.textContent = `${dat.categoria.toCapitalizeCase()}`;  
                        new_li.appendChild(new_a);
                        new_ul_cate.appendChild(new_li);
                        if(dat.sub_cate.length > 0){ 
                            new_a.classList.add('dropdown-toggle');
                            new_a.setAttribute('data-bs-toggle', 'dropdown');
                            new_ul = document.createElement('ul');
                            new_ul.classList.add('sede_sub', 'submenu', 'submenuParent', 'dropdown-menu')        
                            dat.sub_cate.forEach(sub => {
                                new_li_sub = document.createElement('li');
                                new_button = document.createElement('button');
                                new_button.setAttribute('type', 'button');
                                new_button.classList.add('dropdown-item', 'sel_param');
                                new_button.setAttribute('data-category', `${dat.categoria}`);
                                new_button.textContent = `${sub.toCapitalizeCase()}`;
                                new_li_sub.appendChild(new_button);
                                new_ul.appendChild(new_li_sub);
                            });
                            new_li.appendChild(new_ul);
                        }
                    }
                    
                }
                else{
                    return  false;
                }
            });
            for (let el of document.querySelector("#sel_fam").querySelectorAll('.sel_param')) {
                el.addEventListener('click', (e) =>{
                    document.getElementById('work_sede').innerHTML = '';
                    document.getElementById('work_movi').innerHTML = '';
                    redload();
                    createSelsede();
                    if(!objCodExi[workData.categoria]){
                        getCod_Cate();
                    }
                    else{
                        data_cod_cate = objCodExi[workData.categoria];
                        dataTablesCodigos();
                    }
                    getSeriales();
                })
            }
            for (let el of document.getElementById(`sel_category`).querySelectorAll(`.sel_param`) ) {
                el.addEventListener('click', () => { 
                   // valCateExist();
                    createEditcat();
                })
            };
        };
        function DivAPdf() {
            var doc = new jsPDF({orientation: 'landscape'});
            var elementHTML = $('#div_pdf').html();
            var specialElementHandlers = {
                '#elementH': function (element, renderer) {
                return true;
                }
            };
            doc.fromHTML(elementHTML, 35, 35, {
                'width': 300,
                'elementHandlers': specialElementHandlers
            });
            doc.save(`${workData.movimiento.toUpperCase()}.pdf`);
        }
        function reloadTab(){
            document.getElementById('tecnico_movimiento_inv').value = '';
            document.querySelector('#sel_sede').parentElement.classList.add('pe-none');
            if(!!document.querySelector('#btn-card-admin').classList.contains('active') && !!workData.sede){
                document.querySelector('#sede_mov').parentElement.classList.remove('pe-none');
            }
            else{
                document.querySelector('#sede_mov').parentElement.classList.add('pe-none');
            }
            document.getElementById('work_sede').innerHTML = '';
            document.getElementById('work_movi').innerHTML = '';
        }
        function createEditcat() {
            document.getElementById('btn_env_revaluar').removeAttribute('disabled');
            document.getElementById('btn_env_revaluar').classList.remove('pe-none');
            !!document.getElementById('inp_add_sede-msg') && document.getElementById('inp_add_sede-msg').remove();
            !!document.getElementById('sub_familia-msg') && document.getElementById('sub_familia-msg').remove();       
            !!document.getElementById('familia_elemento-msg') && document.getElementById('familia_elemento-msg').remove();
            let fam_selected = event.target.textContent ? event.target.textContent.toCapitalizeCase().trim() : document.getElementById('familia_elemento').value.toCapitalizeCase().trim();
            document.getElementById('sub_familia').value = '';
            document.getElementById('inp_add_sede').value = ''; 
            let datos_fam = [...dataCate].find(el => el.parametros.categoria == fam_selected);
            if(!datos_fam) return;
            saveDataCate = {...datos_fam.parametros};
            document.getElementById('familia_elemento').value = saveDataCate.categoria;
            crearSubcate();
            crearSedes();
        }
        function createSelsede(){
            let catSede = event.target.dataset?.category;
            workData.categoria = catSede;
            workData.sub_cate = event.target.textContent;
            saveCodigo.categoria = workData.categoria;
            saveCodigo.subcategoria = workData.sub_cate;
            codTabla.categoria = workData.categoria;
            codTabla.sub_cate = workData.sub_cate;
            let datos_sede = [...dataCate].find(el => el.parametros.categoria == catSede);
            dataSaldos = [];
            dataSalTotal = [];
            dataTables();
            document.querySelector('#sel_sede').parentElement.classList.remove('pe-none');
            let contenedorsede = document.querySelector('#sel_sede');
            contenedorsede.innerHTML = '';
            let sedes = datos_sede.parametros.sedes;
            if(sedes.length == 1){
                document.getElementById('work_sede').textContent = sedes[0].sede;
                document.querySelector('#sede_mov').parentElement.classList.remove('pe-none');
                workData.sede = sedes[0].sede;
                saveCodigo.sede = workData.sede;
                codTabla.sede = workData.sede;
                workData.movimiento = '';
                controlSede(datos_sede);
            }
            sedes.forEach(sed => {
                let new_i = document.createElement('i');
                let new_btn = document.createElement('button');
                new_btn.classList.add('dropdown-item', 'sede_sel');
                new_btn.setAttribute('id', `sede_${sed.sede}`);
                new_btn.setAttribute('data-info-sede', `${sed.sede}`);
                new_btn.setAttribute('name', 'sede_tab');
                new_btn.setAttribute('aria-label', 'fa-cubes');
                new_btn.textContent = `${sed.sede.toCapitalizeCase()}`;
                new_i.appendChild(new_btn)
                contenedorsede.appendChild(new_i)  
            });
            for (const el of document.querySelectorAll('.sede_sel')) {
                el.addEventListener('click', (e) => {
                    redload();
                    workData.sede = event.target?.dataset?.infoSede;
                    saveCodigo.sede = workData.sede;
                    codTabla.sede = workData.sede;
                    workData.movimiento = '';
                    controlSede(datos_sede);
                });
            };
            let contenedorTilte = document.getElementById('title_work');
            contenedorTilte.innerHTML = '';
            const p_man_t = "<?php echo $permi_manual ?>"
            let permi_cate = [...dataCate].find(e=> e.parametros.categoria == workData.categoria).parametros.permisos;
            let permi_admin = permi_cate.admin;
            let cedula_admin = '<?php echo $_SESSION['cedula'] ?>'
            if(permi_admin.includes(cedula_admin) || p_man_t){
                let div = `
                <div class="d-flex">
                    <span class="btn fa fa-user-gear text-primary" id="btn_permi_cate" data-bs-toggle="modal" data-bs-target="#modal_permi"></span>
                    <div>${workData.categoria[0].toUpperCase() + workData.categoria.substring(1)} - ${workData.sub_cate[0].toUpperCase() + workData.sub_cate.substring(1)}</div>
                </div>`
                contenedorTilte.innerHTML += div;
                !!document.getElementById('msg_sede_perm-msg') && document.getElementById('msg_sede_perm-msg').remove();
                personalAsignado();
            }
            actModaAddlCate();
        }
        function controlSede(datos_sede){
            if(!!document.getElementById('btn-card-kardex').classList.contains('active')){
                workData.movimiento = 'entrada';
                document.getElementById('mostrar_bodega_all').classList.remove('d-none');
                document.getElementById('div_mov_kardex').classList.remove('d-none');
                document.getElementById('div_mov').classList.add('d-none');
                document.getElementById('mostrar_tecnico').classList.remove('d-none');
                document.getElementById('tecnico_movimiento_inv').classList.remove('d-none');
            }
            if(!!document.getElementById('btn-card-invent').classList.contains('active')){
                document.getElementById('mostrar_tecnico').classList.remove('d-none');
                document.getElementById('tecnico_movimiento_inv').classList.remove('d-none');
                document.getElementById('div_mov_kardex').classList.add('d-none');
                document.getElementById('div_mov').classList.add('d-none');
            }
            document.getElementById('work_movi').innerHTML = '';
            let movimientos = datos_sede.parametros.movimientos;
            document.querySelector('#sede_mov').parentElement.classList.remove('pe-none');
            let contenedormovi = document.querySelector('#sede_mov'); 
            contenedormovi.innerHTML = '';
            let sedemov ="";
            let new_i_1 = '';
            let new_btn_1 = '';
            movimientos.forEach(mov => {
                new_i_1 = document.createElement('i');
                new_btn_1 = document.createElement('button');
                new_btn_1.classList.add('dropdown-item', 'movi_sel');
                new_btn_1.setAttribute('id', `mov_${mov}`);
                new_btn_1.setAttribute('name', 'sede_tab');
                new_btn_1.setAttribute('aria-label', 'fa-cubes');
                new_btn_1.textContent = `${mov.toCapitalizeCase()}`;
                new_i_1.appendChild(new_btn_1);
                contenedormovi.appendChild(new_i_1);
            });
            let conten_work_sede = document.getElementById('work_sede');
            conten_work_sede.textContent = `${workData.sede.toCapitalizeCase()}`; //esto pone la primeraletra en mayuscula
            personalSede();
            !!document.getElementById('msg_sede_perm-msg') && document.getElementById('msg_sede_perm-msg').remove();
            for (const a of document.querySelectorAll('.movi_sel')){
                a.addEventListener('click', (e) => {
                    workData.movimiento = e.target.textContent.toLowerCase();
                    codTabla.movimiento = workData.movimiento
                    redload();
                    document.getElementById('tecnico_movimiento_inv').value = "";
                    if(!!document.getElementById('btn-card-kardex').classList.contains('active')){
                        document.getElementById('mostrar_tecnico').classList.remove('d-none');
                        document.getElementById('mostrar_bodega_all').classList.remove('d-none');
                        document.getElementById('import_entrada').classList.add('d-none');
                        document.getElementById('import_entrega').classList.add('d-none');
                        document.getElementById('div_mov_kardex').classList.remove('d-none');
                        document.getElementById('div_mov').classList.add('d-none');
                    }
                    else{
                        iconMostrar();
                        if(workData.movimiento == 'traslado_otro_aliado'){
                            document.getElementById('div_aliado').classList.remove('d-none');
                            document.getElementById('div_aliado_imp').classList.remove('d-none');
                            document.getElementById('mostrar_tecnico').classList.add('d-none');
                            selectAliado();
                        }
                        else if(workData.movimiento == "entrada" || workData.movimiento == "baja"){
                            document.getElementById('div_aliado').classList.add('d-none');
                            document.getElementById('div_aliado_imp').classList.add('d-none');
                            document.getElementById('import_entrada').classList.remove('d-none');
                            document.getElementById('import_entrega').classList.add('d-none');
                            document.getElementById('tarjeta_tecnico_movimiento_inv').classList.add('d-none');
                            document.getElementById('tecnico_movimiento_inv').value = "CINCO";
                            document.getElementById('tecnico_movimiento_inv').classList.add('pe-none');
                            document.getElementById('tecnico_movimiento_inv').classList.remove('d-none');
                            document.getElementById('mostrar_tecnico').classList.remove('d-none');
                            workData.cedula = "CINCO";
                            codTabla.cedula = "CINCO";
                            document.getElementById('select_codigo').classList.remove('pe-none');
                            document.getElementById('select_codigo').classList.remove('bg-gray-200');
                            document.getElementById('select_codigo').focus();
                            document.getElementById('serialCodigo').classList.remove('pe-none');
                            document.getElementById('serialCodigo').classList.remove('bg-gray-200');
                            

                        }
                        else{  
                            document.getElementById('div_aliado').classList.add('d-none'); 
                            document.getElementById('div_aliado_imp').classList.add('d-none'); 
                            if(workData.movimiento == "entrega"){
                                document.getElementById('import_entrega').classList.remove('d-none');
                            }
                            else{
                                document.getElementById('import_entrega').classList.add('d-none');
                            };
                            document.getElementById('import_entrada').classList.add('d-none')
                            document.getElementById('mostrar_tecnico').classList.remove('d-none');
                            document.getElementById('tecnico_movimiento_inv').classList.remove('pe-none');
                            document.getElementById('tecnico_movimiento_inv').classList.remove('d-none');
                        };
                        textBtnMov();
                    };
                    let conten_work_movi = document.getElementById('work_movi');
                    conten_work_movi.textContent = `${workData.movimiento[0].toUpperCase() + workData.movimiento.substring(1)}`;
                    if(workData.movimiento != "entrada"){
                        document.querySelector('.odc_cod').classList.add('d-none');
                        document.getElementById('precioCont').classList.add('d-none');
                        for(let el of document.querySelectorAll('.thDes')){
                            el.classList.add('d-none');
                        };
                    }
                    else{
                        document.querySelector('.odc_cod').classList.remove('d-none');
                        document.getElementById('precioCont').classList.remove('d-none');
                        for(let el of document.querySelectorAll('.thDes')){
                            el.classList.remove('d-none');
                        };
                    }
                });
            };
        }
        function tabInico(){
            let contenedorTilte = document.querySelector('#nav-tab');
            const cardEdiBody = document.createElement('div');
            cardEdiBody.classList.add('card-body', 'mb-n2', 'mt-lg-n3');
            const cardEdiBodyButtons = document.createElement('div');
            cardEdiBodyButtons.classList.add('card-body-buttons', 'parent_card_btn4');
            const cardEdiBtnParent = document.createElement('div');
            cardEdiBtnParent.classList.add('card-btn-parent');
            const btn_card_admin = document.createElement('button');
            btn_card_admin.id = 'btn-card-admin';
            btn_card_admin.textContent = 'Movimientos';
            btn_card_admin.classList.add('buton', 'active', 'nav-link')
            btn_card_admin.setAttribute('data-bs-toggle', 'tab');
            btn_card_admin.setAttribute('data-bs-target', '#nav-admin');
            btn_card_admin.setAttribute('role', 'tab');
            btn_card_admin.setAttribute('aria-selected', 'true');
            const btn_card_ivent = document.createElement('button');
            btn_card_ivent.id = 'btn-card-invent';
            btn_card_ivent.textContent = 'Devolucion / Baja';
            btn_card_ivent.classList.add('buton', 'nav-link');
            btn_card_ivent.setAttribute('data-bs-toggle', 'tab');
            btn_card_ivent.setAttribute('data-bs-target', '#nav-Inventario');
            btn_card_ivent.setAttribute('role', 'tab');
            btn_card_ivent.setAttribute('aria-selected', 'false');
            const btn_card_kardex = document.createElement('button');
            btn_card_kardex.id = 'btn-card-kardex';
            btn_card_kardex.textContent = 'Kardex';
            btn_card_kardex.classList.add('buton', 'nav-link')
            btn_card_kardex.setAttribute('data-bs-toggle', 'tab');
            btn_card_kardex.setAttribute('data-bs-target', '#nav-kardex');
            btn_card_kardex.setAttribute('role', 'tab');
            btn_card_kardex.setAttribute('aria-selected', 'false');
            const btn_card_codigos = document.createElement('button');
            btn_card_codigos.id = 'btn-card-codigos';
            btn_card_codigos.textContent = 'Codigos';
            btn_card_codigos.classList.add('buton', 'nav-link')
            btn_card_codigos.setAttribute('data-bs-toggle', 'tab');
            btn_card_codigos.setAttribute('data-bs-target', '#nav-codigos');
            btn_card_codigos.setAttribute('role', 'tab');
            btn_card_codigos.setAttribute('aria-selected', 'false');
            const overlay = document.createElement('div');
            overlay.classList.add('overlay');
            const cardEdiBodyContenedor = document.createElement('div');
            cardEdiBodyContenedor.id = 'card-body-contenedor';
            cardEdiBtnParent.appendChild(btn_card_admin);
            cardEdiBtnParent.appendChild(btn_card_ivent);
            cardEdiBtnParent.appendChild(btn_card_kardex);
            cardEdiBtnParent.appendChild(btn_card_codigos);
            cardEdiBtnParent.appendChild(overlay);
            cardEdiBodyButtons.appendChild(cardEdiBtnParent);
            cardEdiBody.appendChild(cardEdiBodyButtons);
            cardEdiBody.appendChild(cardEdiBodyContenedor);
            const planBtns = document.querySelectorAll(".buton");
            const plans = document.querySelectorAll(".card-body div");
            const contenedor = document.querySelector('#card-body-contenedor');
            contenedorTilte.insertAdjacentHTML('beforeend', cardEdiBody.outerHTML);
            [...document.querySelectorAll(".buton")].forEach(planBtn => {
                planBtn.addEventListener("click", (e) => {
                    removeClass();
                    event.target.classList.add("active");
                    let btnVal = event.target.getAttribute("id");
                    document.querySelector('#card-body-contenedor').innerHTML = '';
                });
            });
        }
        function removeClass() {
            [...document.querySelectorAll(".buton")].forEach(planBtn => { if(planBtn.classList.contains("active")) planBtn.classList.remove("active") });
            [...document.querySelectorAll(".card-body div")].forEach(plan => { if(plan.classList.contains("active")) plan.classList.remove("active") });
        }
        function textBtnMov(){
            let text_btn = {
                entrada : "Guardar",    
                entrega : "Asignar",
                devolucion : "Recibir",
                mantenimiento: "Mantenimiento",
                baja : "Retirar",
                traslado_otro_aliado:"Trasladar"
            };
            let val_btn = text_btn[workData.movimiento];
            document.getElementById('movInvent').value = val_btn;
        }
        function getCod_Cate(){
            if(validarSesion(location.pathname+location.search)){
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                    method:'POST',
                    data: {
                        getCodCate: workData.categoria
                    },
                    dataType : "json",
                    success: function(data){
                            objCodExi[workData.categoria] = data;
                            objCodExi[workData.categoria].forEach(e=>e.area = JSON.parse(e.area));
                            data_cod_cate = objCodExi[workData.categoria];
                            // let data_cod = data_cod_cate.filter(e=>e.categoria == workData.categoria);
                            setTimeout(()=>dataTablesCodigos(),100);
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                }); 
            };
        };
        function getSeriales(){
            if(validarSesion(location.pathname+location.search)) {
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                    method:'POST',
                    data: {
                        getSerial: workData.categoria
                    },
                    dataType : "json",
                    success: function(data){
                        dataSerial = data;
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                }); 
            };
        };
        function getSaldos(){
            if(validarSesion(location.pathname+location.search)) {
                dataSalTotal = [];
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                    method:'POST',
                    data: {
                        get_saldos: cc_saldo,
                        get_data_sal:JSON.stringify(workData)
                    },
                    dataType : "json",
                    success: function(data){
                        dataSaldos = data;
                        dataSaldos = dataSaldos.flatMap(e => e);
                        dataSaldos.forEach(e=>{
                            e.desc = e.codigo 
                            e.movi = 0
                            e.ico = `<i class="icon_abrir text-primary c_pointer mr-4 fa-solid fa-pen-to-square" data-cod="${e.codigo}" data-ser="${e.serial}" id="${e.codigo}_${e.serial}"  data-bs-toggle="modal" data-bs-target="#modal_saldos"></i>`
                            e.guar = `<i data-codg="${e.codigo}" data-serg="${e.serial}" class="text-success c_pointer icon_guar fa-solid fa-floppy-disk"></i>`
                        });
                        let dat1 = [];
                        dataSaldos.forEach(e =>{ 
                            if(!dat1.includes(e.codigo) && e.serial == ''){
                                dataSalTotal.push(e);
                                dat1.push(e.codigo);
                            }
                            else if(!!dat1.includes(e.codigo) && e.serial == ''){
                                let total = dataSalTotal.find(a=>a.codigo == e.codigo);
                                let tot = 0
                                if(e.movimiento!= 'ENTREGA'){
                                    tot = +total.cantidad - +e.cantidad;
                                }
                                else{
                                    tot = +total.cantidad + +e.cantidad;
                                }
                                total.cantidad = tot;
                            }
                            else if(e.serial != ''){
                                dataSalTotal.push(e);
                            }
                        });
                        if(cc_saldo == '811042087'){
                            document.getElementById('cobro_sal').parentNode.classList.add('d-none');
                            document.getElementById('devolucion_sal').parentNode.classList.add('d-none');
                            
                        }
                        else{
                            document.getElementById('cobro_sal').parentNode.classList.remove('d-none');
                            document.getElementById('devolucion_sal').parentNode.classList.remove('d-none');
                        }
                        dataSalTotal = dataSalTotal.filter(e => e.cantidad > 0);
                        dataTables();
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                });
            };
        };
        function getSaldos_kardex(formDat, name_tab, action){
            if(validarSesion(location.pathname+location.search)) {
                bodega_kardex = [];
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/controlador_control_inventario_kardex.php",
                    method:'POST',
                    data: {
                        get_data_kar_bodega:JSON.stringify(workData),
                        get_cod_kardex: JSON.stringify(formDat)
                    },
                    dataType : "json",
                    success: function(data){
                        bodega_kardex = data.flatMap(e=> e);
                        if(action == 'buscar'){
                            if(formDat.serial != ''){
                                bodega_kardex.forEach(e=>{
                                    e.cantidad = 1;
                                });
                            }
                            ejAjax();
                            bodega_kardex.forEach(e=>{
                                e.descri = data_cod_cate.find(a=> a.codigo == e.codigo)?.descripcion ?? '';
                            });
                        }else{
                            bodega_kardex = bodega_kardex.reduce((acc, obj)=>{
                                if(!acc.find(e=>e.codigo == obj.codigo)){
                                    acc.push({
                                        codigo: obj.codigo,
                                        descri: obj.codigo,
                                        baja: 0,
                                        entrada: 0,
                                        entrega: 0,
                                        devolucion: 0,
                                        moviles:0,
                                        stock:0
                                    });
                                }
                                let cant_total = acc.find(e=> e.codigo == obj.codigo);
                                
                                if(obj.movimiento == 'ENTRADA'){
                                    cant_total.entrada = cant_total.entrada + +obj.cantidad;
                                    cant_total.stock = cant_total.stock + +obj.cantidad;
                                }
                                if(obj.movimiento == 'ENTREGA'){
                                    cant_total.entrega = cant_total.entrega + +obj.cantidad;
                                    cant_total.stock = cant_total.stock - +obj.cantidad;
                                    cant_total.moviles = cant_total.moviles + +obj.cantidad;
                                }
                                if(obj.movimiento == 'DEVOLUCION'){
                                    cant_total.devolucion = cant_total.devolucion + +obj.cantidad;
                                    cant_total.moviles = cant_total.moviles - +obj.cantidad;
                                }
                                if(obj.movimiento == 'BAJA' || obj.movimiento == 'COBRO'){
                                    cant_total.baja = cant_total.baja + +obj.cantidad;
                                    cant_total.stock = cant_total.stock - +obj.cantidad;
                                }
                                return acc;
                            },[]);
                        }
                        dataTablesBusSal(name_tab);
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                });
            };
        };
        function createSubCaSede(act){
            let sub_cate_fam = document.getElementById('sub_familia').value.toLowerCase().toCapitalizeCase().trim();
            let sede_cate = document.getElementById('inp_add_sede').value.toLowerCase().toCapitalizeCase().trim();
            let cat_sel = document.getElementById('familia_elemento').value.toCapitalizeCase().trim();
            let new_span = "";
            let contenedorSubCate = document.querySelector("#fila_subcate");
            let contenedorSede = document.querySelector("#fila_sedes");
            let new_i = "";
            let exi_sub_car = [...dataCate].find(a => a.parametros.categoria == cat_sel);
            if((act == 'sub_cat' || act == 'cat_sel')){
                contenedorSubCate.innerHTML = '';
                if(exi_sub_car != undefined){
                    let param_exi = exi_sub_car.parametros;
                    param_exi.sub_cate.forEach(e => {                        
                        (!saveDataCate.sub_cate.includes(e)) && saveDataCate.sub_cate.push(e);
                    });
                }
                (sub_cate_fam != '' && !saveDataCate.sub_cate.includes(sub_cate_fam)) && saveDataCate.sub_cate.push(sub_cate_fam);
                if(saveDataCate.sub_cate.length > 0){
                    crearSubcate(saveDataCate.sub_cate)
                }
            }
            if(act == 'sede' || act == 'cat_sel'){
                contenedorSede.innerHTML = '';
                if(exi_sub_car != undefined){    
                    let param_exi = exi_sub_car.parametros; 
                    param_exi.sedes.forEach(e => {
                    (!saveDataCate.sedes.includes(e)) && saveDataCate.sedes.push(e);
                    });
                }
                (sede_cate != '' && !saveDataCate.sedes.includes(sede_cate)) && saveDataCate.sedes.push(sede_cate);
                if(saveDataCate.sedes.length > 0){
                    crearSedes(saveDataCate.sedes); //revisar
                };
            }
        }
        function crearSubcate(){
            let contenedorSubCate = document.querySelector("#fila_subcate");
            contenedorSubCate.innerHTML = '';
            saveDataCate.sub_cate.forEach(c => {
                new_i = document.createElement('i')
                new_i.classList.add('fa', 'fa-trash', 'mt-n2', 'position-absolute', 'sub_cate_class', 'text-danger');
                new_i.setAttribute('style', 'cursor:pointer');
                new_span = document.createElement('span');
                new_span.classList.add('badge', 'badge-light', 'badge-pill', 'span_ot', 'add_inp_ot', 'bg-primary', 'text-white');
                new_span.setAttribute('style', 'margin-right:3px');
                new_span.style.color = 'rgb(2, 117, 216)';
                new_span.textContent =`${c}`;
                new_span.appendChild(new_i)
                contenedorSubCate.appendChild(new_span); saveDataCate
            });
            for (let ele of document.querySelectorAll('.sub_cate_class')) {
                ele.addEventListener('click', e => {
                    saveDataCate.sub_cate = saveDataCate.sub_cate.filter(el => el != ele.parentNode.textContent);
                    crearSubcate() 
                });
            };
        };
        async function crearSedes() {
            document.getElementById('inp_add_sede').removeAttribute('required');
            let contenedorSede = document.querySelector("#fila_sedes");
            contenedorSede.innerHTML = '';
            await saveDataCate.sedes.forEach(d => {
                new_i_sede = document.createElement('i')
                new_i_sede.classList.add('fa', 'fa-trash', 'mt-n2', 'position-absolute', 'text-danger', 'sede_class')
                new_i_sede.setAttribute('style', 'cursor:pointer');
                const new_span_sed = document.createElement('span');
                new_span_sed.classList.add('badge', 'badge-light', 'badge-pill', 'span_ot', 'add_inp_ot', 'bg-primary', 'text-white');
                new_span_sed.setAttribute('style', 'margin-right:3px');
                new_span_sed.style.color = 'rgb(2, 117, 216)';
                new_span_sed.textContent =`${d.sede}`;
                new_span_sed.appendChild(new_i_sede)
                contenedorSede.appendChild(new_span_sed);
            });
            for (let ele of document.querySelectorAll('.sede_class')) {
                ele.addEventListener('click', e => {
                    saveDataCate.sedes = saveDataCate.sedes.filter(el => el.sede != ele.parentNode.textContent.toCapitalizeCase().trim());
                    crearSedes()
                });
            }
        }
        function reset_ticket() {
            let fec = new Date();
            let fec_num = formatDate(fec, 'yyyymmdd') + minTwoDigits(fec.getHours()) + minTwoDigits(fec.getMinutes()) + minTwoDigits(fec.getSeconds());
            let cc_ses = "<?php echo $_SESSION['cedula']; ?>";
            let new_tick = cc_ses+fec_num;
            workData.ticket = new_tick;
            codTabla.ticket = new_tick;
            return new_tick;
        };
        function redload(){
            document.getElementById('div_tabla_cod_buscar').classList.add('d-none');
            document.getElementById('mostrar_tecnico').classList.add('d-none');
            document.querySelector('#perm_acordeon_sed').classList.add('d-none');
            document.getElementById('div_aliado').classList.add('d-none');
            document.getElementById('div_aliado_imp').classList.add('d-none');
            dataSaldos = [];
            dataSalTotal = [];
            dataTables();
            if(!!document.getElementById('btn-card-kardex').classList.contains('active') && !!workData.sede){
                document.getElementById('mostrar_tecnico').classList.remove('d-none');
                document.getElementById('mostrar_bodega_all').classList.remove('d-none');
                document.getElementById('div_mov_kardex').classList.remove('d-none');
                document.getElementById('div_mov').classList.add('d-none');
            }
            else{
                document.getElementById('mostrar_bodega_all').classList.add('d-none');
            }
            !!document.getElementById('precioCodigo-msg') && document.getElementById('precioCodigo-msg').remove();
            !!document.getElementById('cantCodigo-msg') && document.getElementById('cantCodigo-msg').remove();
            document.getElementById('agregar-codigo_inventario').classList.add('pe-none');
            document.getElementById('agregar-codigo_inventario').classList.add('text-gray-600');
            document.getElementById('agregar-codigo_inventario').classList.remove('text-primary');
            document.getElementById('ticketPerson').value = reset_ticket();
            document.getElementById('tecnico_movimiento_inv').value = "";
            document.getElementById('tarjeta_tecnico_movimiento_inv').classList.add('d-none');
            document.getElementById('formInventario').reset();
            document.getElementById('sel_cod_msg').setAttribute('hidden', '');
            document.getElementById("sel_serial_msg").setAttribute('hidden','');
            document.getElementById("sel_serial_msg1").setAttribute('hidden','');
            document.getElementById('obsCodigo').classList.add('pe-none');
            document.getElementById('obsCodigo').classList.add('bg-gray-200');
            document.getElementById('serialCodigo').classList.add('pe-none');
            document.getElementById('serialCodigo').classList.add('bg-gray-200');
            document.getElementById('odcCodigo').classList.add('pe-none');
            document.getElementById('odcCodigo').classList.add('bg-gray-200');
            document.getElementById('precioCodigo').classList.add('pe-none');
            document.getElementById('precioCodigo').classList.add('bg-gray-200');
            document.getElementById('cantCodigo').classList.add('pe-none');
            document.getElementById('cantCodigo').classList.add('bg-gray-200');
            document.getElementById('agregar_item').classList.add('pe-none');
            document.getElementById('select_codigo').classList.add('pe-none');
            document.getElementById('select_codigo').classList.add('bg-gray-200');
            document.getElementById('tablaPrinci').innerHTML = "";
            codTabla.datosTabla = [];
            [...document.querySelectorAll('.cantidad_msg3')].forEach(e=> e.setAttribute('hidden',''));
            [...document.querySelectorAll('.cantidad_msg1')].forEach(e=> e.setAttribute('hidden',''));
            for (el of document.querySelectorAll('.cantidad_msg')){
                el.setAttribute('hidden','');
            }
            for (el of document.querySelectorAll('.tamano')){
                el.textContent = '';
            }
            document.getElementById('odc_msg').setAttribute('hidden','');
            document.getElementById('odcCodigo').classList.remove('is-invalid');
            document.getElementById('serialCodigo').classList.remove('is-invalid');
            document.getElementById('precioCodigo').classList.remove('is-invalid');
            document.getElementById('cantCodigo').classList.remove('is-invalid');
            document.getElementById('InvetarioCount').value = "0";
            document.getElementById('movInvent').classList.add('pe-none');
            document.getElementById('limpiarIvent').classList.add('pe-none');
            document.getElementById('import_entrada').classList.add('d-none');
            document.getElementById('import_entrega').classList.add('d-none');
        };
        function createTabMov(){
            let tabla = document.getElementById('tablaPrinci');
            tabla.innerHTML = '';
            codTabla.datosTabla.forEach(e=>{
                let new_tr = document.createElement('tr');
                let new_td = document.createElement('td');
                let new_td1 = document.createElement('td');
                let new_td2 = document.createElement('td');
                let new_td3 = document.createElement('td');
                let new_td4 = document.createElement('td');
                let new_td5 = document.createElement('td');
                let new_td6 = document.createElement('td');
                let new_td7 = document.createElement('td');
                let new_td8 = document.createElement('td');
                let new_i = document.createElement('i');
                new_td.textContent = e.codigo;
                new_td1.textContent = e.descripcion;
                new_td2.textContent = e.medida;
                new_td3.textContent = e.cantidad;
                new_td4.textContent = e.precio;
                new_td5.textContent = e.serial;
                new_td6.textContent = e.odc;
                new_td7.textContent = e.observaciones;
                new_td4.classList.add('columnDes');
                new_td6.classList.add('columnDes');
                new_td8.classList.add('c_pointer', 'eliminarFil');
                new_i.classList.add('c_pointer', 'eliminarFil', 'fas', 'text-danger', 'fa-trash-alt');
                new_td8.setAttribute(`data-cod`,`${e.codigo}`);
                new_i.setAttribute(`data-cod`,`${e.codigo}`);
                new_td8.appendChild(new_i);
                new_tr.appendChild(new_td);
                new_tr.appendChild(new_td1);
                new_tr.appendChild(new_td2);
                new_tr.appendChild(new_td3);
                new_tr.appendChild(new_td4);
                new_tr.appendChild(new_td5);
                new_tr.appendChild(new_td6);
                new_tr.appendChild(new_td7);
                new_tr.appendChild(new_td8);
                tabla.appendChild(new_tr);
            });
            
            if(workData.movimiento != 'entrada'){
                for(let el of document.querySelectorAll('.columnDes')){
                    el.classList.add('d-none');
                };
            }
            else{
                for(let el of document.querySelectorAll('.columnDes')){
                    el.classList.remove('d-none');
                };
            }
            for (let el of document.querySelectorAll('.eliminarFil')){
                el.addEventListener('click', e => {
                    let codEli = e.target?.dataset?.cod;
                    codTabla.datosTabla = codTabla.datosTabla.filter(e => e.codigo != codEli);
                    createTabMov();
                });
            };
            document.getElementById('InvetarioCount').value = codTabla.datosTabla.length;
            if (codTabla.datosTabla.length == 0){
                document.getElementById('movInvent').classList.add('pe-none');
            };
        };
        function addItemMov(){
            let valNumero = /^\d*\.?\,?\d*$/;
            let cantidad = document.getElementById('cantCodigo').value;
            let pre = document.getElementById('precioCodigo').value;
            cantidad.replaceAll(',','.');
            pre.replaceAll(',','.');
            let codtipo = document.getElementById('select_codigo').value.toUpperCase();
            let valTipo = data_cod_cate.find(e=> e.codigo == codtipo)
            if ((document.getElementById('cantCodigo').value == ''  || document.getElementById('odcCodigo').value == '') && workData.movimiento == 'entrada'){
                for(el of document.querySelectorAll('.cantidad_msg')){
                    el.removeAttribute('hidden');
                }
                document.getElementById('sel_serial_msg').setAttribute('hidden', '');
                document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                return false;
            }
            else if (workData.movimiento == 'baja' && (document.getElementById('obsCodigo').value == '' || document.getElementById('cantCodigo').value == '')) {
                [...document.querySelectorAll('.cantidad_msg')].forEach(e=> e.removeAttribute('hidden'));
                for(el of document.querySelectorAll('.cantidad_msg1')){
                    el.removeAttribute('hidden');
                }
                document.getElementById('sel_serial_msg').setAttribute('hidden', '');
                document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                
                return false;
            }
            else if ((workData.movimiento == 'entrega' || workData.movimiento == 'devolucion') && document.getElementById('cantCodigo').value == '') {
                for(el of document.querySelectorAll('.cantidad_msg')){
                    el.removeAttribute('hidden');
                }
                document.getElementById('sel_serial_msg').setAttribute('hidden', '');
                document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                
                return false;
            }
            else if (valTipo.tipo == "SERIALIZADO" && document.getElementById("serialCodigo").value == ''){
                for(el of document.querySelectorAll('.cantidad_msg3')){
                    el.removeAttribute('hidden');
                }
                document.getElementById('sel_serial_msg').setAttribute('hidden', '');
                document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                return false;
            }
            else if(!valNumero.test(cantidad) || !valNumero.test(pre)){
                new createDivMsg('cantCodigo', `Debe Ingresar Solo Numeros`);
                new createDivMsg('precioCodigo', `Debe Ingresar Solo Numeros`);
                return false;
            }
            else{
                [...document.querySelectorAll('.cantidad_msg3')].forEach(e=> e.setAttribute('hidden',''));
                [...document.querySelectorAll('.cantidad_msg1')].forEach(e=> e.setAttribute('hidden',''));
                for(el of document.querySelectorAll('.cantidad_msg')){
                    el.setAttribute('hidden','');
                
                }
                !!document.getElementById('precioCodigo-msg') && document.getElementById('precioCodigo-msg').remove();
                !!document.getElementById('cantCodigo-msg') && document.getElementById('cantCodigo-msg').remove();
                document.getElementById('movInvent').classList.remove('pe-none');
                document.getElementById('limpiarIvent').classList.remove('pe-none');
                let observaciones = document.getElementById('obsCodigo').value;
                let cant = document.getElementById('cantCodigo').value;
                objTabla.codigo = document.getElementById('select_codigo').value.toUpperCase();
                objTabla.serial = document.getElementById('serialCodigo').value.toUpperCase();
                if(document.getElementById("serialCodigo").value == ''){
                    codTabla.datosTabla = codTabla.datosTabla.filter(e => e.codigo != objTabla.codigo);
                }
                else{
                    codTabla.datosTabla = codTabla.datosTabla.filter(e => e.serial != objTabla.serial);
                };
                objTabla.descripcion = document.getElementById('select_descripcion').textContent;
                objTabla.medida = document.getElementById('medidaCod').textContent;
                objTabla.cantidad = cant;
                objTabla.precio = document.getElementById('precioCodigo').value;
                objTabla.odc = document.getElementById('odcCodigo').value.toUpperCase();
                objTabla.observaciones = observaciones.toUpperCase();
                codTabla.datosTabla.push(objTabla);
                objTabla ={};
                document.getElementById('formInventario').reset();
                document.getElementById('medidaCod').textContent = '';
                document.getElementById('select_descripcion').textContent = '';
                document.getElementById('cantCodigo').value = '';
                cant = '';
                document.getElementById('precioTarjet').textContent = ''; 
                document.getElementById('obsCodigo').classList.add('pe-none');
                document.getElementById('obsCodigo').classList.add('bg-gray-200');
                document.getElementById('serialCodigo').classList.add('pe-none');
                document.getElementById('serialCodigo').classList.add('bg-gray-200');
                document.getElementById('precioCodigo').classList.add('pe-none');
                document.getElementById('precioCodigo').classList.add('bg-gray-200');
                document.getElementById('odcCodigo').classList.add('pe-none');
                document.getElementById('odcCodigo').classList.add('bg-gray-200');
                document.getElementById('cantCodigo').classList.add('pe-none');
                document.getElementById('cantCodigo').classList.add('bg-gray-200');
                document.getElementById('agregar_item').classList.add('pe-none');
                document.getElementById("sel_serial_msg").setAttribute('hidden','');
                document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                !!document.getElementById('serialCodigo-msg') && document.getElementById('serialCodigo-msg').remove();
                if(workData.movimiento != "entrada"){
                    document.getElementById('serialCodigo').classList.remove('pe-none');
                    document.getElementById('serialCodigo').classList.remove('bg-gray-200');
                    document.getElementById('serialCodigo').focus();
                }
                else{
                    document.getElementById('select_codigo').focus();
                    document.getElementById('serialCodigo').classList.add('pe-none');
                    document.getElementById('serialCodigo').classList.add('bg-gray-200');
                }
                createTabMov();
            };
        }
        function checkCodSerial(){
            let dat = document.querySelector('#item_cod_serial').checked
            if(dat == false) {
                ir_serial.classList.add('fa-arrow-right');
                ir_serial.classList.remove('fa-arrow-left');
                ir_serial.classList.remove('text-primary');                           
                saveCodigo.tipo = 'NORMAL';
            } else {
                ir_serial.classList.add('fa-arrow-left');
                ir_serial.classList.remove('fa-arrow-right');
                ir_serial.classList.add('text-primary');  
                saveCodigo.tipo = 'SERIALIZADO';
            }
        };
        function codExisCate(){
            document.getElementById('codItem').addEventListener('input', e =>{
                let val = e.target.value;
                
                let dat = data_cod_cate.find(e=> e.codigo == val);
                
                if(!!dat){
                    new createDivMsg('codItem', `Este Codigo ya existe Para Esta Categoria`);
                    document.getElementById('guardar_cod').setAttribute('disabled','');
                    document.getElementById('descripItem_material').value = dat.descripcion;
                    document.getElementById('descripItem_material').classList.add('pe-none');
                    document.getElementById('precioItem').classList.add('pe-none');
                } 
                else{
                    !!document.getElementById('codItem-msg') && document.getElementById('codItem-msg').remove();
                    document.getElementById('guardar_cod').removeAttribute('disabled');
                    document.getElementById('precioItem').classList.remove('pe-none');
                    document.getElementById('descripItem_material').classList.remove('pe-none');
                    
                }
            });
        };
        function limpiarTab(){
            document.getElementById('tablaPrinci').innerHTML = "";
            document.getElementById('InvetarioCount').value = 0;
            codTabla.datosTabla = [];
            document.getElementById('movInvent').classList.add('pe-none');
            document.getElementById('limpiarIvent').classList.add('pe-none');
        };
        async function saveNewCate(){
            document.getElementById('categoria_msg').setAttribute('hidden','');
            document.getElementById('familia_elemento').classList.remove('is-invalid');
            !!document.getElementById('inp_add_sede-msg') && document.getElementById('inp_add_sede-msg').remove();
            document.getElementById('inp_add_sede').classList.remove('is-invalid');
            saveDataCate.categoria = document.getElementById('familia_elemento').value.toCapitalizeCase().trim();
            let existe = false;
            await [...dataCate].forEach((item, index) => {
                if (item.parametros.categoria == saveDataCate.categoria){
                    existe = true;
                    return true;
                }
            });
            const val_exis = await existe;
            if (!!val_exis) {
                // validar si se adicionó o eliminó subcategoria
                let sub_cate = [...dataCate].find(e => e.parametros.categoria == saveDataCate.categoria).parametros.sub_cate;
                let sedes = [...dataCate].find(e => e.parametros.categoria == saveDataCate.categoria).parametros.sedes;
                let sub_cate_new = [...saveDataCate.sub_cate];
                let sedes_new = [...saveDataCate.sedes];
                let sub_cate_add = [];
                let sub_cate_del = [];
                let sedes_add = [];
                let sedes_del = [];
                sub_cate_new.forEach((item, index) => {
                    if (!sub_cate.includes(item)){
                        sub_cate_add.push(item);
                    }
                });
                sub_cate.forEach((item, index) => {
                    if (!sub_cate_new.includes(item)){
                        sub_cate_del.push(item);
                    }
                });
                sedes_new.forEach((item, index) => {
                    if (!sedes.includes(item)){
                        sedes_add.push(item);
                    }
                });
                sedes.forEach((item, index) => {
                    if (!sedes_new.includes(item)){
                        sedes_del.push(item);
                    }
                });
                if (sub_cate_add.length > 0 || sub_cate_del.length > 0 || sedes_add.length > 0 || sedes_del.length > 0){
                    Swal.fire({
                        title: `¿Desea actualizar la categoria: ${saveDataCate.categoria}?`,
                        html: `<div class="text-center">
                                    <table class="datatable_div  table table-bordered table-sm text-center tb_materiales_mov" id="">
                                        <thead>
                                            <tr><th class="text-center b text-primary" colspan="2">Subcategorias</th> </tr>
                                            <tr class="bg-primary text-white">
                                                <th class="tdh-m">Actualizar</th>
                                                <th class="tdh-m">Eliminar</th>
                                            </tr>
                                        </thead>
                                        <tbody id="">
                                            <tr>
                                                <td>
                                                    <div class="0 d-flex flex-wrap gap-1  p-2 rounded-3 ">
                                                        ${sub_cate_add.map(e => `<span class="badge badge-light badge-pill span_ot add_inp_ot bg-primary text-white" style="margin-right: 3px; color: rgb(2, 117, 216);">${e}</span>`).join('')}
                                                    </div>
                                                </td>
                                            
                                                <td>
                                                    <div class="0 d-flex flex-wrap gap-1  p-2 rounded-3 ">
                                                        ${sub_cate_del.map(e => `<span class="badge badge-light badge-pill span_ot add_inp_ot bg-danger text-white" style="margin-right: 3px; color: rgb(2, 117, 216);">${e}</span>`).join('')}
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="datatable_div table table-bordered table-sm text-center tb_materiales_mov" id="">
                                        <thead>
                                            <tr><th colspan="2" class="text-center b text-primary">Sedes</th></tr>
                                            <tr class="bg-primary text-white">
                                                <th class="tdh-m">Actualizar</th>
                                                <th class="tdh-m">Eliminar</th>
                                            </tr>
                                        </thead>
                                        <tbody id="">
                                            <tr>
                                                <td>
                                                    <div class="0 d-flex flex-wrap gap-1  p-2 rounded-3 ">
                                                        ${sedes_add.map(e => `<span class="badge badge-light badge-pill span_ot add_inp_ot bg-primary text-white" style="margin-right: 3px; color: rgb(2, 117, 216);">${e.sede}</span>`).join('')}
                                                    </div>
                                                </td>
                                            
                                                <td>
                                                    <div class="0 d-flex flex-wrap gap-1  p-2 rounded-3 ">
                                                        ${sedes_del.map(e => `<span class="badge badge-light badge-pill span_ot add_inp_ot bg-danger text-white" style="margin-right: 3px; color: rgb(2, 117, 216);">${e.sede}</span>`).join('')}
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // update categoria
                            saveCate(!!val_exis);
                        }
                    });
                }
                else{
                    Swal.fire({
                        title: `La categoria: ${saveDataCate.categoria} ya existe`,
                        html: `<div class="text-left">
                                    <p class="text-danger text-center">No Hay Cambios Para Actualizar</p>
                                </div>`,
                        icon: 'warning',
                        confirmButtonText: 'Ok',
                        timer: 3000
                    });
                }
            }
            else{
                Swal.fire({
                    title: `¿Desea guardar la categoria: ${saveDataCate.categoria}?`,
                    html: `<div class="text-center">
                                <table class="datatable_div  table table-bordered table-sm text-center tb_materiales_mov" id="">
                                    <thead>
                                        <tr><th class="text-center b text-primary" colspan="2">${saveDataCate.categoria}</th> </tr>
                                        <tr class="bg-primary text-white">
                                            <th class="tdh-m">Subcategorias</th>
                                            <th class="tdh-m">Sedes</th>
                                        </tr>
                                    </thead>
                                    <tbody id="">
                                        <tr>
                                            <td>
                                                <div class="0 d-flex flex-wrap gap-1  p-2 rounded-3 ">
                                                    ${saveDataCate.sub_cate.map(e => `<span class="badge badge-light badge-pill span_ot add_inp_ot bg-primary text-white" style="margin-right: 3px; color: rgb(2, 117, 216);">${e}</span>`).join('')}
                                                </div>
                                            </td>
                                        
                                            <td>
                                                <div class="0 d-flex flex-wrap gap-1  p-2 rounded-3 ">
                                                    ${saveDataCate.sedes.map(e => `<span class="badge badge-light badge-pill span_ot add_inp_ot bg-primary text-white" style="margin-right: 3px; color: rgb(2, 117, 216);">${e.sede}</span>`).join('')}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // save categoria
                        saveCate(!!val_exis);
                    }
                });
            }
            function saveCate(val_exis){
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                    method:'POST',
                    data: {
                        save_familia: JSON.stringify(saveDataCate)
                    },
                    dataType : "json",
                    async: false,
                    success:  function(data){
                        if(data.success){
                            Swal.fire({
                                title: data.msg,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            });
                            let cedula_admin_cate = '<?php echo $_SESSION['cedula'] ?>'
                            
                            if (!!val_exis){
                                // update categoria
                                let idx_data_cate = dataCate.findIndex(e => e.parametros.categoria == saveDataCate.categoria);
                                dataCate[idx_data_cate].parametros.sub_cate = [...saveDataCate.sub_cate];
                                dataCate[idx_data_cate].parametros.sedes = [...saveDataCate.sedes];
                            }
                            else{
                                // save categoria
                                const permisos = {
                                    admin: [cedula_admin_cate],
                                    editor: [],
                                    lector: []
                                };
                                dataCate.push({
                                    parametros: {
                                        categoria: saveDataCate.categoria,
                                        sub_cate: [...saveDataCate.sub_cate],
                                        sedes: [...saveDataCate.sedes],
                                        movimientos:["entrega", "entrada","devolucion","baja","mantenimiento","traslado_otro_aliado"],
                                        permisos:permisos
                                }
                                });
                                let dat = dataCate.find(e=>e.parametros.categoria == saveDataCate.categoria).parametros.sedes;
                                dat.map(e=>{
                                    e['permisos'] = permisos;
                                })
                                createSelFam('sel_fam');
                                createSelFam('sel_category');
                            };
                            document.getElementById('div_cate_data').classList.remove('pe-none');
                            createSelFam('sel_fam');
                            !!document.getElementById('familia_elemento-msg') && new createDivMsg('familia_elemento-msg').remove();
                            setTimeout(() => document.getElementById('btn_cerrar_modal').click(),200);
                        }
                        else{
                            Swal.fire({
                            title: data.msg,
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                        }
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                });
            }
        }
        async function valCateExist(){
            !!document.querySelector('inp_add_sede-msg') && document.getElementById('inp_add_sede-msg').remove()
            document.getElementById('sub_familia').value = '';
            document.getElementById('inp_add_sede').value = ''; 
            !!document.getElementById('sub_familia-msg') && document.getElementById('sub_familia-msg').remove();       
            !!document.getElementById('familia_elemento-msg') && document.getElementById('familia_elemento-msg').remove();
            document.getElementById('fila_subcate').innerHTML = '';
            document.getElementById('fila_sedes').innerHTML = ''; 
            saveDataCate.categoria = '';
            saveDataCate.sub_cate = [];
            saveDataCate.sedes = [];
            if(event.target.value == '') return;
            let nom_fam = await [...dataCate].find(e => e.parametros.categoria == event.target.value.toCapitalizeCase().trim());
            if(!nom_fam && event.target.value.length >= 3){
                saveDataCate.categoria = event.target.value;               
                document.getElementById(`${event.target.id}-msg`) ? document.getElementById(`${event.target.id}-msg`).innerHTML="" :"";
                document.getElementById('fila_subcate').innerHTML="";
                document.getElementById('fila_sedes').innerHTML="";
                document.getElementById('btn_env_revaluar').removeAttribute('disabled');
                document.getElementById('btn_env_revaluar').classList.remove('pe-none');
            }
            else if(!!nom_fam ){
                document.getElementById('btn_env_revaluar').classList.remove('pe-none');
                document.getElementById('btn_env_revaluar').removeAttribute('disabled');
            }
            else{
                new createDivMsg('familia_elemento', 'Es menor a 3 caracteres');
                document.getElementById('btn_env_revaluar').setAttribute('disabled','');
                document.getElementById('familia_elemento').classList.remove('is-invalid')
                document.getElementById('btn_env_revaluar').classList.add('pe-none');
                let cedula_admin = '<?php echo $_SESSION['cedula'] ?>'
                if(!!workData.categoria){
                    let permi_cate = [...dataCate].find(e=> e.parametros.categoria == workData.categoria).parametros.permisos;
                    let permi_admin = permi_cate.admin;
                    let permi_edit = permi_cate.editor;
                    if(permi_admin.includes(cedula_admin) || permi_edit.includes(cedula_admin)){
                        document.getElementById('btn_env_revaluar').removeAttribute('disabled');
                        document.getElementById('btn_env_revaluar').classList.remove('pe-none');
                        !!document.getElementById('familia_elemento-msg') && document.getElementById('familia_elemento-msg').remove();
                    }
                }
            }
        }
        function abrirModalCate(){
            if(exis_btn == true){
                document.getElementById('btn_modal_edit_cate').addEventListener('click',()=> {
                    cate_event = new Event('input');
                    document.getElementById('familia_elemento').value = workData.categoria;
                    document.querySelector('#familia_elemento').dispatchEvent(cate_event);
                    !!document.getElementById('familia_elemento-msg') && document.getElementById('familia_elemento-msg').remove();
                    !!document.getElementById('inp_add_sede-msg') && document.getElementById('inp_add_sede-msg').remove();
                    document.getElementById('categoria_msg').setAttribute('hidden','');
                    document.getElementById('familia_elemento').classList.remove('is-invalid');
                    !!document.getElementById('inp_add_sede-msg') && document.getElementById('inp_add_sede-msg').remove();
                    document.getElementById('inp_add_sede').classList.remove('is-invalid');
                    createSelFam('sel_category');
                });
            }
        }
        function actModaAddlCate(){
            let cedula_admin = '<?php echo $_SESSION['cedula'] ?>'
            const p_man = "<?php echo $permi_manual ?>"
            const bnt_mod =`
            <a data-parent="#sidebar" aria-expanded="false">
                <div> 
                    <i id ="btn_modal_edit_cate" class="" data-bs-toggle="modal" data-bs-target="#exampleModal" >
                    <span id="sty"></span>
                    </i>
                </div>
            </a>` 
            if(p_man){
                document.getElementById('modal_add_cate').innerHTML = bnt_mod;
                exis_btn = true;
                document.getElementById('btn_modal_edit_cate').classList.add('fas', 'fa-plus-circle');
                document.getElementById('familia_elemento').removeAttribute('disabled','');
                document.getElementById('btn_env_revaluar').removeAttribute('disabled');
                document.querySelector('.h4_cate').innerHTML = 'Crear o Editar Categoria';
                document.querySelector('.div_cate').classList.remove('d-none');
                document.getElementById('familia_elemento').value = workData.categoria;
            }
            else if (!!workData.categoria){
                let permi_cate = [...dataCate].find(e=> e.parametros.categoria == workData.categoria).parametros.permisos;
                let permi_admin = permi_cate.admin;
                let permi_edit = permi_cate.editor;
                let permi_leer = permi_cate.lector;
                if(permi_admin.includes(cedula_admin) || permi_edit.includes(cedula_admin)){
                    document.getElementById('modal_add_cate').innerHTML = bnt_mod;
                    exis_btn = true;
                    document.getElementById('btn_modal_edit_cate').classList.add('fa-regular', 'fa-pen-to-square');
                    document.getElementById('familia_elemento').setAttribute('disabled','');
                    document.getElementById('btn_env_revaluar').removeAttribute('disabled');
                    document.getElementById('btn_env_revaluar').classList.remove('pe-none');
                    document.querySelector('.h4_cate').innerHTML = 'Editar categoria';
                    document.querySelector('.div_cate').classList.add('d-none');
                }
                else if(permi_leer.includes(cedula_admin)){
                    document.getElementById('modal_add_cate').innerHTML = bnt_mod;
                    exis_btn = true;
                    document.getElementById('btn_modal_edit_cate').classList.add('fa-regular', 'fa-pen-to-square');
                    document.querySelector('.div_cate').classList.add('d-none');
                    document.getElementById('familia_elemento').setAttribute('disabled','');
                    document.getElementById('btn_env_revaluar').setAttribute('disabled','');
                    document.querySelector('.h4_cate').innerHTML = 'Ver categoria';
                }
            }
            !!document.getElementById('familia_elemento-msg') && document.getElementById('familia_elemento-msg').remove();
            abrirModalCate()
        };
        function addPermiCat(id){
            let cedula_admin = '<?php echo $_SESSION['cedula'] ?>'
            const permi_gen_cat = [...dataCate].find(perm => perm.parametros.categoria == workData.categoria).parametros.permisos;
            const cc_add_perm = document.getElementById('cc_inven_permisos_search').value;
            let tabla_asignar = document.querySelector('#tabla_asignar tbody');
            let input = `<input type="checkbox" id="admin_t" value="">`;
            let inputEdit = `<input type="checkbox" id="edit_t" value="">`;
            let inputLector = `<input type="checkbox" id="lector_t" value="">`;
            let icono = `<i class="iconoI text-primary fa-solid fa-floppy-disk c_pointer"></i>`
            tabla_asignar.innerHTML = '';
            if(!!cc_add_perm && id == 'general'){
                let new_tr = document.createElement('tr');
                let new_td = document.createElement('td');
                let new_td1 = document.createElement('td');
                let new_td2 = document.createElement('td');
                let new_td3 = document.createElement('td');
                let new_td4 = document.createElement('td');
                new_td.textContent = cc_add_perm;
                new_td1.innerHTML = input;
                new_td2.innerHTML = inputEdit;
                new_td3.innerHTML = inputLector;
                new_td4.innerHTML = icono;
                new_tr.appendChild(new_td);
                new_tr.appendChild(new_td1);
                new_tr.appendChild(new_td2);
                new_tr.appendChild(new_td3);
                new_tr.appendChild(new_td4);
                tabla_asignar.appendChild(new_tr);
                setTimeout(()=> {
                    if(!!document.getElementById('admin_t') && !!permi_gen_cat.admin.includes(cc_add_perm)){
                        document.getElementById('admin_t').checked = true;
                    }
                    if(!!document.getElementById('edit_t') && !!permi_gen_cat.editor.includes(cc_add_perm)){
                        document.getElementById('edit_t').checked = true;
                    }
                    if(!!document.getElementById('lector_t') && !!permi_gen_cat.lector.includes(cc_add_perm)){
                        document.getElementById('lector_t').checked = true;
                    }
                },200)
                for (el of document.querySelectorAll('.iconoI')){
                    el.addEventListener('click',e =>{
                        let conta = false;
                        if(!!document.getElementById('admin_t')?.checked && !permi_gen_cat.admin.includes(cc_add_perm)){
                            permi_gen_cat.admin.push(cc_add_perm);
                            conta = true;
                        };
                        if(!!document.getElementById('edit_t')?.checked && !permi_gen_cat.editor.includes(cc_add_perm)){
                            permi_gen_cat.editor.push(cc_add_perm);
                            conta = true;
                        };
                        if(!!document.getElementById('lector_t')?.checked  && !permi_gen_cat.lector.includes(cc_add_perm)){
                            permi_gen_cat.lector.push(cc_add_perm);
                            conta = true;
                        };
                        if(!document.getElementById('admin_t')?.checked && !!permi_gen_cat.admin.includes(cc_add_perm)){
                            permi_gen_cat.admin = permi_gen_cat.admin.filter(e => e != cc_add_perm);
                            conta = true;
                        };
                        if(!document.getElementById('edit_t')?.checked && !!permi_gen_cat.editor.includes(cc_add_perm)){
                            permi_gen_cat.editor = permi_gen_cat.editor.filter(e => e != cc_add_perm);
                            conta = true;
                        };
                        if(!document.getElementById('lector_t')?.checked  && !!permi_gen_cat.lector.includes(cc_add_perm)){
                            permi_gen_cat.lector = permi_gen_cat.lector.filter(e => e != cc_add_perm);
                            conta = true;
                        };
                        if(conta == true){
                            limpiarPermi();
                            personalAsignado();
                        };
                        document.querySelector('#tabla_asignar').classList.add('d-none');
                        !!document.getElementById('msg_sede_perm-msg') && document.getElementById('msg_sede_perm-msg').remove();
                    })
                }
            }
            else if(!!cc_add_perm && id == 'sede'){
                const permi_sede = [...dataCate].filter(perm => perm.parametros.categoria == workData.categoria).flatMap(perm => perm.parametros.sedes).find(e=> e.sede == workData.sede);
                let new_tr = document.createElement('tr');
                let new_td = document.createElement('td');
                let new_td1 = document.createElement('td');
                let new_td2 = document.createElement('td');
                let new_td3 = document.createElement('td');
                let new_td4 = document.createElement('td');
                new_td.textContent = cc_add_perm;
                new_td1.innerHTML = input;
                new_td2.innerHTML = inputEdit;
                new_td3.innerHTML = inputLector;
                new_td4.innerHTML = icono;
                new_tr.appendChild(new_td);
                new_tr.appendChild(new_td1);
                new_tr.appendChild(new_td2);
                new_tr.appendChild(new_td3);
                new_tr.appendChild(new_td4);
                tabla_asignar.appendChild(new_tr);
                setTimeout(()=> {
                    if(!!document.getElementById('admin_t') && !!permi_sede.permisos.admin.includes(cc_add_perm)){
                        document.getElementById('admin_t').checked = true;
                    }
                    if(!!document.getElementById('edit_t') && !!permi_sede.permisos.editor.includes(cc_add_perm)){
                        document.getElementById('edit_t').checked = true;
                    }
                    if(!!document.getElementById('lector_t') && !!permi_sede.permisos.lector.includes(cc_add_perm)){
                        document.getElementById('lector_t').checked = true;
                    }
                },200)
                for (el of document.querySelectorAll('.iconoI')){
                    el.addEventListener('click',e =>{
                        let conta1 = false;
                        if(!!document.getElementById('admin_t')?.checked && !permi_sede.permisos.admin.includes(cc_add_perm)){
                            permi_sede.permisos.admin.push(cc_add_perm);
                            conta1 = true;
                        }
                        if(!!document.getElementById('edit_t')?.checked && !permi_sede.permisos.editor.includes(cc_add_perm)){
                            permi_sede.permisos.editor.push(cc_add_perm);
                            conta1 = true;
                        }
                        if(!!document.getElementById('lector_t')?.checked && !permi_sede.permisos.lector.includes(cc_add_perm)){
                            permi_sede.permisos.lector.push(cc_add_perm);
                            conta1 = true;
                        }
                        if(!document.getElementById('admin_t')?.checked && !!permi_sede.permisos.admin.includes(cc_add_perm)){
                            permi_sede.permisos.admin = permi_sede.permisos.admin.filter(e => e != cc_add_perm);
                            conta1 = true;
                        }
                        if(!document.getElementById('edit_t')?.checked && !!permi_sede.permisos.editor.includes(cc_add_perm)){
                            permi_sede.permisos.editor = permi_sede.permisos.editor.filter(e => e != cc_add_perm);
                            conta1 = true;
                        }
                        if(!document.getElementById('lector_t')?.checked && !!permi_sede.permisos.lector.includes(cc_add_perm)){
                            permi_sede.permisos.lector = permi_sede.permisos.lector.filter(e => e != cc_add_perm);
                            conta1 = true;
                        }
                        if(conta1 == true){
                            limpiarPermi();
                            personalSede();
                        };
                        document.querySelector('#tabla_asignar').classList.add('d-none');
                        !!document.getElementById('msg_sede_perm-msg') && document.getElementById('msg_sede_perm-msg').remove();
                    })
                }
            }
        }
        function limpiarPermi(){
            document.querySelector('#tabla_asignar').classList.add('d-none');
            document.getElementById('inven_permisos_search').value = '';
            savePermi();
        }
        function savePermi(){
            let save_permi = dataCate.find(e => e.parametros.categoria == workData.categoria).parametros;
            if(validarSesion(location.pathname+location.search)) {
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                    method:'POST',
                    data: {
                        save_permi: JSON.stringify(save_permi)
                    },
                    dataType : "json",
                    success: function(data){
                        if(data.success){
                            Swal.fire({
                                title: data.msg,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            });
                        }
                        else{
                            Swal.fire({
                                title: data.msg,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                        }
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                });
            };
        };
        function personalAsignado(){
            document.querySelector('#person_gene_span').textContent =`Personal Asignado a la Categoria ${workData.categoria}`
            let data_permi = dataCate.find(e=> e.parametros.categoria == workData.categoria).parametros.permisos;
            let person_asig = Object.entries(data_permi).reduce((acc, [key, val])=>{
                if(val.length >  0){
                    val.forEach(e => {
                        
                        let val_cc = acc.find(a => a.cedula == e );
                        if (!!val_cc){
                            val_cc[key] = true;
                        }
                        else {
                            let dat = {
                                cedula: e
                            };
                            dat[key] = true;
                            acc.push(dat)
                        };
                    });
                };
                return acc;
            },[]);
            document.querySelector('#countPermi_gen').textContent = person_asig.length;
            person_asig.forEach(e=> {
                let dat = e.cedula;
                e.input = `<input type="checkbox" id="admin_${dat}" value="">`;
                e.inputEdit = `<input type="checkbox" id="edit_${dat}" value="">`;
                e.inputLector = `<input type="checkbox" id="lector_${dat}" value="">`;
                e.icono = `<i class="icono_ text-primary mr-3 fa-solid fa-floppy-disk c_pointer" data-cod="${dat}"></i>
                <i class="fa-solid icono_b text-danger fa-trash-can c_pointer" data-eli="${dat}"></i>`;
                if(!!e.admin){
                    e.input = `<input type="checkbox" id="admin_${dat}" checked="true">`;
                }
                if(!!e.editor){
                    e.inputEdit = `<input type="checkbox" id="edit_${dat}" checked="true">`;                    
                }
                if(!!e.lector){
                    e.inputLector = `<input type="checkbox" id="lector_${dat}" checked="true">`;                    
                }
            });
            $('#tabla_personal_general').DataTable({
                destroy:true,
                lengthChange:false,
                data: person_asig,
                columns: [
                    { title: "Cedula", data: "cedula"},
                    { title: "Admin", data: "input"},
                    { title: "Gestor", data: "inputEdit"},
                    { title: "Lectura", data: "inputLector"},
                    { title: "Acciones", data: "icono"}
                ],
                responsive: true,
                language: dataTable_espanol,
            });
            document.querySelector('#tabla_personal_general thead').classList.add('bg-primary','text-white','text-center');
            document.querySelector('#tabla_personal_general tbody').classList.add('text-center');
            for (el of document.querySelectorAll('.icono_')){
                el.addEventListener('click',e =>{
                    let dato = e.target?.dataset?.cod;
                    let conta = false;
                    if(!!document.getElementById(`admin_${dato}`)?.checked && !data_permi.admin.includes(dato)){
                        data_permi.admin.push(dato);
                        conta = true;
                    }
                    if(!!document.getElementById(`edit_${dato}`)?.checked && !data_permi.editor.includes(dato)){
                        data_permi.editor.push(dato);
                        conta = true;
                    }
                    if(!!document.getElementById(`lector_${dato}`)?.checked && !data_permi.lector.includes(dato)){
                        data_permi.lector.push(dato);
                        conta = true;
                    }
                    if(!document.getElementById(`admin_${dato}`)?.checked && !!data_permi.admin.includes(dato)){
                        data_permi.admin = data_permi.admin.filter(e => e != dato);
                        conta = true;
                    }
                    if(!document.getElementById(`edit_${dato}`)?.checked && !!data_permi.editor.includes(dato)){
                        data_permi.editor = data_permi.editor.filter(e => e != dato);
                        conta = true;
                    }
                    if(!document.getElementById(`lector_${dato}`)?.checked && !!data_permi.lector.includes(dato)){
                        data_permi.lector = data_permi.lector.filter(e => e != dato);
                        conta = true;
                    }
                    if(conta == true){
                        limpiarPermi();
                    }
                    !!document.getElementById('msg_sede_perm-msg') && document.getElementById('msg_sede_perm-msg').remove();
                })
            }
            [...document.querySelectorAll('.icono_b')].forEach(e => {
                e.addEventListener('click', el => {
                    let dato_ced = el.target?.dataset?.eli;
                    data_permi.admin = data_permi.admin.filter(e => e != dato_ced);
                    data_permi.editor = data_permi.editor.filter(e=> e != dato_ced);
                    data_permi.lector = data_permi.lector.filter(e=> e != dato_ced);
                    personalAsignado();
                    limpiarPermi();
                });
            });
        }
        function personalSede(){
            if(!!workData.sede){
                !!document.getElementById('msg_sede_perm-msg') && document.getElementById('msg_sede_perm-msg').remove();
                document.querySelector('#perm_acordeon_sed').classList.remove('d-none');
                document.querySelector('#span_asig_sede').textContent = `Personal Asignado a la Sede ${workData.sede}`;
                const permi_sede_exi = [...dataCate].filter(perm => perm.parametros.categoria == workData.categoria).flatMap(perm => perm.parametros.sedes).find(e=> e.sede == workData.sede).permisos;
                let person_asig = Object.entries(permi_sede_exi).reduce((acc, [key, val])=>{
                    if(val.length >  0){
                        val.forEach(e => {
                            let val_cc = acc.find(a => a.cedula == e );
                            if (!!val_cc){
                                val_cc[key] = true;
                            }
                            else {
                                let dat = {
                                    cedula: e
                                };
                                dat[key] = true;
                                acc.push(dat)
                            }
                        })
                    }
                    return acc
                },[]);
                document.querySelector('#count_sed').textContent = person_asig.length;
                person_asig.forEach(e=> {
                    let dat = e.cedula;
                    e.input = `<input type="checkbox" id="admin${dat}" value="">`;
                    e.inputEdit = `<input type="checkbox" id="edit${dat}" value="">`;
                    e.inputLector = `<input type="checkbox" id="lector${dat}" value="">`;
                    e.icono = `<i class="icono_g text-primary mr-3 fa-solid fa-floppy-disk c_pointer" data-code="${dat}"></i>
                    <i class="fa-solid icono_bs text-danger fa-trash-can c_pointer" data-elim="${dat}"></i>`
                    if(!!e.admin){
                        e.input = `<input type="checkbox" id="admin${dat}" checked="true">`;
                    }
                    if(!!e.editor){
                        e.inputEdit = `<input type="checkbox" id="edit${dat}" checked="true">`;                    
                    }
                    if(!!e.lector){
                        e.inputLector = `<input type="checkbox" id="lector${dat}" checked="true">`;                    
                    }
                });
                $('#tabla_personal_sede').DataTable({
                    destroy:true,
                    lengthChange:false,
                    data: person_asig,
                    columns: [
                        { title: "Cedula", data: "cedula"},
                        { title: "Admin", data: "input"},
                        { title: "Gestor", data: "inputEdit"},
                        { title: "Lectura", data: "inputLector"},
                        { title: "Acciones", data: "icono"}
                    ],
                    responsive: true,
                    language: dataTable_espanol,
                });
                for (el of document.querySelectorAll('.icono_g')){
                    el.addEventListener('click',e =>{
                        let dato = e.target?.dataset?.code;
                        let conta = false;
                        if(!!document.getElementById(`admin${dato}`)?.checked && !permi_sede_exi.admin.includes(dato)){
                            permi_sede_exi.admin.push(dato);
                            conta = true;
                        }
                        if(!!document.getElementById(`edit${dato}`)?.checked && !permi_sede_exi.editor.includes(dato)){
                            permi_sede_exi.editor.push(dato);
                            conta = true;
                        }
                        if(!!document.getElementById(`lector${dato}`)?.checked && !permi_sede_exi.lector.includes(dato)){
                            permi_sede_exi.lector.push(dato);
                            conta = true;
                        }
                        if(!document.getElementById(`admin${dato}`)?.checked && !!permi_sede_exi.admin.includes(dato)){
                            permi_sede_exi.admin = permi_sede_exi.admin.filter(e => e != dato);
                            conta = true;
                        }
                        if(!document.getElementById(`edit${dato}`)?.checked && !!permi_sede_exi.editor.includes(dato)){
                            permi_sede_exi.editor = permi_sede_exi.editor.filter(e => e != dato);
                            conta = true;
                        }
                        if(!document.getElementById(`lector${dato}`)?.checked && !!permi_sede_exi.lector.includes(dato)){
                            permi_sede_exi.lector = permi_sede_exi.lector.filter(e => e != dato);
                            conta = true;
                        }
                        if(conta == true){
                            limpiarPermi();
                        }
                    })
                }
                [...document.querySelectorAll('.icono_bs')].forEach(e => {
                    e.addEventListener('click', el => {
                        let dato_ced = el.target?.dataset?.elim;
                        permi_sede_exi.admin = permi_sede_exi.admin.filter(e => e != dato_ced);
                        permi_sede_exi.editor = permi_sede_exi.editor.filter(e=> e != dato_ced);
                        permi_sede_exi.lector = permi_sede_exi.lector.filter(e=> e != dato_ced);
                        personalSede();
                        limpiarPermi();
                    });
                });
            }
        }
        function dataTables(){
            $('#tabla_saldos').DataTable({
                destroy:true,
                dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                buttons: dataTable_all_btn,
                data: dataSalTotal,
                columns: [
                    { title: "Codigo", data: "codigo"},
                    { title: "Descripcion", data: "desc"},
                    { title: "serial", data: "serial"},
                    { title: "Cantidad", data: "cantidad"},
                    { title: "Dev - Baja - Cobro", data: "movi"},
                    { title: "Editar", data: "ico"},
                    { title: "Guardar", data: "guar"}
                ],
                columnDefs: [{
                    targets: 1, //descripcion
                    render: function(data, type, full, meta){
                        let descri = data_cod_cate.find(e=> e.codigo == data)?.descripcion ?? '';
                        return descri;
                    }
                }],
                responsive: true,
                language: dataTable_espanol,
                initComplete: function() {
                    $(`#tabla_saldos tfoot th`).each(function() {
                        let title = $(this).text();
                        $(this).html('<input class="w-75" type="search" placeholder=" ' + title + '" />');
                    });
                        // Apply the search
                    this.api().columns().every(function() {
                        let that = this;
                        $('#tabla_saldos', this.footer()).on('input', function() {
                            this.classList.remove('bg_rep_aus', 'border-left-info');
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                            if(this.value){
                                this.classList.add('bg_rep_aus', 'border-left-info');
                            }
                        });
                    });
                }
            });
            document.querySelector('#tabla_saldos thead').classList.add('bg-primary','text-white','text-center');
            document.querySelector('#tabla_saldos tbody').classList.add('text-center');
            if(!!document.querySelector('.icon_abrir')){
                !!document.getElementById('obs_sal-msg') && document.getElementById('obs_sal-msg').remove();
                    iconClick();
                }
            if(!!document.querySelector('.icon_guar')){
                !!document.getElementById('obs_sal-msg') && document.getElementById('obs_sal-msg').remove();
                iconGuarClick();
            }
        }
        function dataTablesCodigos(){
            
            let cod = '';
            jsonAreaCarp();
            let dat1 = [];
            let admi = [];
            let dat = json_area_carp.filter(e=>e.tipo_area == 'OPERATIVA').map(e=> dat1.push(e.area));
            let operativa = json_area_carp.filter(e=>e.tipo_area == 'ADMINISTRATIVA').map(e=> admi.push(e.area));
            dat1 = [...new Set(dat1)];
            admi = [...new Set(admi)];
            $('#tabla_codigos_bodega').DataTable({
                destroy:true,
                dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                buttons: dataTable_all_btn,
                data: data_cod_cate,
                columns: [
                    { title: "Codigo", data: "codigo"},
                    { title: "Descripcion", data: "descripcion"},
                    { title: "Subcategoria", data: "subcategoria"},
                    { title: "Tipo", data: "tipo"},
                    { title: "Area", data: "area"},
                    { title: "Edit", data: "codigo"},
                ],
                columnDefs: [{
                    targets: 5, //descripcion
                    render: function(data, type, full, meta){
                        return `<i id="${data}" class="abrir_mod fa-solid c_pointer text-success fa-pen-to-square" data-codigo=${data} data-bs-toggle="modal" data-bs-target="#codigoModal"></i>`;;
                    }
                }],
                language: dataTable_espanol,
                initComplete: function() {
                    $(`#tabla_codigos_bodega tfoot th`).each(function() {
                        let title = $(this).text();
                        $(this).html('<input class="w-75" type="search" placeholder=" ' + title + '" />');
                    });
                        // Apply the search
                    this.api().columns().every(function() {
                        let that = this;
                        $('#tabla_codigos_bodega', this.footer()).on('input', function() {
                            this.classList.remove('bg_rep_aus', 'border-left-info');
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                            if(this.value){
                                this.classList.add('bg_rep_aus', 'border-left-info');
                            }
                        });
                    });
                    
                },
                fnDrawCallback: function(){
                    document.querySelectorAll('#tabla_codigos_bodega .abrir_mod').forEach(e=>{
                        e.addEventListener('click', (a) =>{
                            cod = a.target.dataset.codigo;
                            let arr = data_cod_cate.find(a=>a.codigo == e.codigo);
                            let dat = data_cod_cate.find(e=> e.codigo == cod);
                            saveCodigo.area = [];
                            document.getElementById('input_area').classList.add('d-none');
                            document.getElementById('input_area_admin').classList.add('d-none');
                            document.getElementById('mostrar_area').classList.remove('active');
                            document.getElementById('admin_area').classList.remove('active');
                            document.getElementById('h5_cod_mod').parentNode.classList.remove('bg-primary');
                            document.getElementById('h5_cod_mod').parentNode.classList.add('bg-success');
                            document.getElementById('h5_cod_mod').textContent = 'Editar Codigo';
                            document.getElementById('guardar_cod').textContent = 'Actualizar';
                            document.getElementById('guardar_cod').classList.remove('bg-primary');
                            document.getElementById('guardar_cod').classList.add('bg-success');
                            // document.getElementById('ope_admin').parentNode.classList.remove('d-none');
                            document.getElementById('ocultarImport').classList.add('d-none');
                            document.getElementById('codItem').value = cod;
                            document.getElementById('descripItem_material').value = dat.descripcion;
                            document.getElementById('precioItem').value = 0;
                            if(dat.tipo == "SERIALIZADO" && !document.querySelector('#item_cod_serial').checked){
                                document.querySelector('#item_cod_serial').click();
                            }
                            else if(dat.tipo != "SERIALIZADO" && !!document.querySelector('#item_cod_serial').checked){
                                document.querySelector('#item_cod_serial').click();
                            }
                            funActive(cod,dat1,admi);
                        });
                    });
                    
                }
            });
            document.querySelector('#tabla_codigos_bodega thead').classList.add('bg-primary','text-white','text-center');
        }
        const funActive = (codi='',ope,adm) => {
            let matriz = [];
            if(!!workData.categoria && !!document.getElementById('btn-card-codigos').classList.contains('active')){
                matriz = data_cod_cate.find(e=>e.codigo == codi).area;
            }
            let lista = ''
            console.log(ope);
            console.log(saveCodigo.area);
            ope.forEach(e=>{
                if(!!matriz.includes(e)){
                    lista+=
                    `<label id="${e}" style="width:max-content; height:max-content;" class="area_l active btn btn-sm btn-outline-primary mb-1  mr-1 ">${e}</label>`;
                    saveCodigo.area.push(e);
                    document.getElementById("sp_gen").classList.add('d-none');
                }
                else{
                    lista+=
                    `<label id="${e}" style="width:max-content; height:max-content;" class="area_l btn btn-sm btn-outline-primary mb-1 mr-1 ">${e}</label>`;
                }
            });
            document.getElementById('input_area').innerHTML = lista;
            let lista_admin = ''
            adm.forEach(e=>{
                if(!!matriz.includes(e)){
                    lista_admin+=
                    `<label id="${e}" style="width:max-content; height:max-content;" class="area_l active btn btn-sm btn-outline-primary mb-1 mr-1 ">${e}</label>`;
                    saveCodigo.area.push(e);
                    document.getElementById("sp_gen").classList.add('d-none');
                }
                else{
                    lista_admin+=
                    `<label id="${e}" style="width:max-content; height:max-content;" class="area_l btn btn-sm btn-outline-primary mb-1 mr-1 ">${e}</label>`;
                }
            });
            document.getElementById('input_area_admin').innerHTML = lista_admin;
            document.querySelectorAll('.area_l').forEach(e=>{
                e.addEventListener('click', (a)=>{
                    if(!e.classList.contains('active')){
                        e.classList.add('active');
                        saveCodigo.area.push(e.textContent);
                        document.getElementById("sp_gen").textContent = `[${saveCodigo.area[0]}]`;
                        if(saveCodigo.area.length > 1){
                            document.getElementById("sp_gen").textContent = '[VARIAS]';
                        }
                    }
                    else{
                        e.classList.remove('active');
                        saveCodigo.area = saveCodigo.area.filter(a=> a != e.textContent);
                        if(!saveCodigo.area.length){
                            document.getElementById("sp_gen").textContent = '[GENERAL]';
                        }
                    }
                });
            });
            (()=>{
                document.getElementById('add_delete').addEventListener('click',e=>{
                    if(!document.getElementById('add_delete').classList.contains('fa-circle-xmark')){
                        saveCodigo.area = [...ope,...adm];
                        document.querySelectorAll('.area_l').forEach(e=>e.classList.add('active'));
                        document.getElementById('add_delete').classList.add('fa-circle-xmark','text-danger');
                        document.getElementById('sp_gen').textContent = '[TODAS]'
                    }
                    else{
                        saveCodigo.area = [];
                        document.querySelectorAll('.area_l').forEach(e=>e.classList.remove('active'));
                        document.getElementById('add_delete').classList.remove('fa-circle-xmark','text-danger');
                        document.getElementById('sp_gen').textContent = '[GENERAL]';
                    }
                });
            })()
        }
        function iconGuarClick(){
            [...document.querySelectorAll('.icon_guar')].forEach(a=> {
                a.addEventListener('click', e=>{
                    dat_ser1 = e.target?.dataset?.serg;
                    dat_nor1 = e.target?.dataset?.codg;
                    if(!!data_sal_arr.length && !!data_sal_arr.find(e=>e.codigo == dat_nor1 && e.serial== dat_ser1 && e.cobro!='')){
                        Swal.fire({
                            title: `Desea Procesar el Cobro?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#42ba96', 
                            cancelButtonColor: '#dd3333',
                            confirmButtonText: `procesar`,
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                reporteSave('report');
                            }
                        });
                    }
                    else{
                        reporteSave('report');
                    };
                });
            });
        }
        function iconClick(){
            !!document.getElementById('obs_sal-msg') && document.getElementById('obs_sal-msg').remove();
            [...document.querySelectorAll('.icon_abrir')].forEach(e=> {
                e.addEventListener('click', el =>{
                    !!document.getElementById('baja_sal-msg') && document.getElementById('baja_sal-msg').remove();
                    document.getElementById('form_sal_mod').reset();
                    cod1 = el.target?.dataset?.cod;
                    ser1 = el.target?.dataset?.ser;
                    if(!!ser1){
                        [...document.querySelectorAll('.rad_opc')].forEach(e =>{
                            e.type = 'radio';
                            e.classList.remove('form-control');
                        });
                        document.getElementById('devolucion_sal').classList.add('ml-4');
                        document.getElementById('baja_sal').classList.add('ml-2');
                        document.getElementById('baja_sal').checked = true
                        document.getElementById('cobro_sal').classList.add('ml-3');
                    }
                    else{
                        [...document.querySelectorAll('.rad_opc')].forEach(e =>{
                            e.type = 'number';
                            e.classList.add('form-control');
                            e.classList.remove('ml-4', 'ml-2', 'ml-3');
                        });
                    }
                    let des = data_cod_cate.find(e=>e.codigo == cod1).descripcion
                    document.getElementById('span_mod_cod').innerHTML = cod1;
                    document.getElementById('span_mod_des').innerHTML = des;
                    document.getElementById('span_mod_ser').innerHTML = ser1;
                    document.getElementById('cc_mod_h3').innerHTML = cc_saldo;
                    document.getElementById('h5_mod_title').innerHTML = `${workData.categoria} - ${workData.sub_cate}`
                    document.getElementById('span_mod_title').innerHTML = `${workData.sede}`
                    data_sal_mod.cedula = cc_saldo;
                    data_sal_mod.codigo = cod1;
                    data_sal_mod.serial = ser1;
                });
            });
        };
        function saldoSer(){
            [...document.querySelectorAll('.saldoSer')].forEach(e=>{
                e.addEventListener('click', e =>{
                    Swal.fire({
                        title: `Desea Eliminar Este Reporte?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#42ba96',
                        cancelButtonColor: '#dd3333',
                        confirmButtonText: `Eliminar`,
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let dat = e.target?.dataset?.ser;
                            let dat_cod = e.target.dataset?.sal;
                            dataSalTotal.find(e=>e.serial == dat).ico = `
                            <i class="icon_abrir text-primary c_pointer mr-4 fa-solid fa-pen-to-square" data-cod="${dat_cod}" data-ser="${dat}" id="${dat_cod}_${dat}"  data-bs-toggle="modal" data-bs-target="#modal_saldos"></i>
                                `;
                            dataSalTotal.find(e=>e.serial == dat).movi = 0;
                            data_sal_arr = data_sal_arr.filter(e=> e.serial != dat );
                        }
                        setTimeout(()=>{
                            saldoNor();
                            saldoSer();
                        },200);
                        dataTables();
                    });
                });
            });
        };
        function saldoNor(){
            [...document.querySelectorAll('.saldoNor')].forEach(e=>{
                e.addEventListener('click', e =>{
                    Swal.fire({
                        title: `Desea Eliminar Este Reporte?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#42ba96',
                        cancelButtonColor: '#dd3333',
                        confirmButtonText: `Eliminar`,
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let dat = e.target?.dataset?.sed;
                            let dat_cod = e.target.dataset?.nor;
                            dataSalTotal.find(e=>e.codigo == dat_cod && e.serial == '').ico = `
                            <i class="icon_abrir text-primary c_pointer mr-4 fa-solid fa-pen-to-square" data-cod="${dat_cod}" data-ser="${dat}" id="${dat_cod}_${dat}"  data-bs-toggle="modal" data-bs-target="#modal_saldos"></i>
                                `;
                            dataSalTotal.find(e=>e.codigo == dat_cod && e.serial == '').movi = 0;
                            data_sal_arr = data_sal_arr.filter(e=> e.serial != dat && e.codigo != dat_cod);
                        }
                        setTimeout(()=>{
                            saldoSer();
                            saldoNor();
                        },200);
                        dataTables();
                    });
                });
            });
        };
        function reporteSave(valor){
            if(validarSesion(location.pathname+location.search)) {
                if(valor === "report_all"){
                    pro_ajax = $.ajax({
                        url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                        method:'POST',
                        data: {
                            save_salAll: JSON.stringify(workData),
                            data_save_salAll: JSON.stringify(data_sal_arr)
                        },
                        dataType : "json",
                        success: function(data){
                            if(data.success){
                                Swal.fire({
                                    title: data.msg,
                                    icon: 'success',
                                    confirmButtonText: 'Ok'
                                });
                                data_sal_arr.forEach(e=>{
                                    if(e.serial != ''){
                                        dataSalTotal = dataSalTotal.filter(a=> a.serial != e.serial)
                                    }
                                    if(e.devolucion != '' && e.serial == ''){
                                        let dat1 = dataSalTotal.find(a=>a.codigo == e.codigo)
                                        dat1.movi = 0;
                                        dat1.cantidad -= e.devolucion;
                                        dat1.ico = `
                                        <i class="icon_abrir text-primary c_pointer mr-4 fa-solid fa-pen-to-square" data-cod="${e.codigo}" data-ser="${e.serial}" id="${e.codigo}_${e.serial}"  data-bs-toggle="modal" data-bs-target="#modal_saldos"></i>
                                            `;
                                    }
                                    if((e.baja!='' || e.cobro != '') && e.serial == ''){
                                        let dat = dataSalTotal.find(a=>a.codigo == e.codigo)
                                        dat.movi = 0;
                                        dat.cantidad -= e.baja;
                                        dat.ico = `
                                        <i class="icon_abrir text-primary c_pointer mr-4 fa-solid fa-pen-to-square" data-cod="${e.codigo}" data-ser="${e.serial}" id="${e.codigo}_${e.serial}"  data-bs-toggle="modal" data-bs-target="#modal_saldos"></i>
                                        `;
                                    }
                                });
                                data_sal_arr = [];
                                dataTables();
                                document.getElementById('guar_reporte').classList.add('pe-none');
                                document.getElementById('eli_reporte').classList.add('pe-none');
                            }
                            else{
                                Swal.fire({
                                    title: data.msg,
                                    icon: 'error',
                                    confirmButtonText: 'Ok'
                                });
                            }
                        },
                        error: function( jqXHR, textStatus, errorThrown ) {
                            error_ajax( jqXHR, textStatus, errorThrown );
                        }
                    });
                }
                else if(valor === "report"){
                    pro_ajax = $.ajax({
                        url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                        method:'POST',
                        data: {
                            save_sal: JSON.stringify(workData),
                            data_save_sal: JSON.stringify(data_sal_arr.find(e=>e.codigo == dat_nor1 && e.serial== dat_ser1))
                        },
                        dataType : "json",
                        success: function(data){
                            if(data.success){
                                Swal.fire({
                                    title: data.msg,
                                    icon: 'success',
                                    confirmButtonText: 'Ok'
                                });
                                let dat = data_sal_arr.find(e=> e.codigo == dat_nor1 && e.serial== dat_ser1);
                                if(dat.serial == ''){    
                                        data_sal_arr = data_sal_arr.filter(e=>e.codigo != dat_nor1);
                                        let dat1 = dataSalTotal.find(e=>e.codigo == dat_nor1);
                                        dat1.ico = `
                                        <i class="icon_abrir text-primary c_pointer mr-4 fa-solid fa-pen-to-square" data-cod="${dat_nor1}" data-ser="${dat_ser1}" id="${dat_nor1}_${dat_ser1}"  data-bs-toggle="modal" data-bs-target="#modal_saldos"></i>
                                            `;
                                        dat1.movi = 0;
                                        let total = +dat.baja + +dat.cobro + +dat.devolucion;
                                        if(dat1.cantidad - total < 0){
                                            dat1.cantidad = 0;
                                        }
                                        else{
                                            dat1.cantidad -= total;
                                        }
                                }
                                else{
                                    data_sal_arr = data_sal_arr.filter(e=>e.codigo != dat_nor1);
                                    dataSalTotal = dataSalTotal.filter(e=> e.serial != dat_ser1);
                                }
                                dataTables();
                            }
                            else{
                                Swal.fire({
                                    title: data.msg,
                                    icon: 'error',
                                    confirmButtonText: 'Ok'
                                });
                            }
                        },
                        error: function( jqXHR, textStatus, errorThrown ) {
                            error_ajax( jqXHR, textStatus, errorThrown );
                        }
                    });
                };
            };
        };
        function selectAliado(){
            if(validarSesion(location.pathname+location.search)) {
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                    method:'POST',
                    data: {
                        get_aliado: workData.categoria
                    },
                    dataType : "json",
                    success: function(data){
                        dataAliado = data;//
                        llamarAliado();
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                });
            };
        };
        function llamarAliado(){
            if(!!dataAliado.length){
                let conten = document.getElementById('select_aliado');
                conten.innerHTML = '';
                let option_1 = document.createElement('option');
                option_1.setAttribute('selected','');
                option_1.setAttribute('disabled','');
                option_1.textContent = 'Seleccione Aliado...';
                conten.appendChild(option_1);
                dataAliado.forEach(e => {
                    let option = document.createElement('option');
                    option.textContent = e.nombre_aliado;
                    option.setAttribute('value', e.id_aliado);
                    conten.appendChild(option);
                });
            };
        };
        function agregarTraslado(){
            document.getElementById('agregar_tras').addEventListener('click',e=>{
                createTabMov();
            });
        };
        const clickCod = ()=>{ 
            for (let el of document.querySelector("#tabla_cod_buscar tbody").querySelectorAll('.link_tr_cod')) {
                el.addEventListener('click', (e) =>{
                    document.getElementById('select_codigo').value = e.target.parentNode.children[0].textContent;
                    const event_cod = new Event('input');  
                    document.querySelector('#select_codigo').dispatchEvent(event_cod);
                    setTimeout(()=>document.getElementById('div_tabla_cod_buscar').classList.add('d-none'),200);
                });
            };
        };
        // Ready
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.cerrr_Modcod').forEach(e=>{
                e.addEventListener('click',e=>{
                    document.getElementById('form_mod_cod').reset();
                    !!document.getElementById('codItem-msg') && document.getElementById('codItem-msg').remove();
                    document.getElementById('guardar_cod').removeAttribute('disabled');
                    document.getElementById('precioItem').classList.remove('pe-none');
                    document.getElementById('descripItem_material').classList.remove('pe-none');
                });
            })
            document.getElementById('mostrar_area').addEventListener('click',()=>{
                document.getElementById('admin_area').classList.remove('active');
                document.getElementById('input_area_admin').classList.add('d-none');
                if(!document.getElementById('mostrar_area').classList.contains('active')){
                    document.getElementById('input_area').classList.remove('d-none');
                    document.getElementById('mostrar_area').classList.add('active');
                }
                else{
                    document.getElementById('mostrar_area').classList.remove('active');
                    document.getElementById('input_area').classList.add('d-none');
                }
            });
            document.getElementById('admin_area').addEventListener('click',()=>{
                document.getElementById('mostrar_area').classList.remove('active');
                document.getElementById('input_area').classList.add('d-none');
                if(!document.getElementById('admin_area').classList.contains('active')){
                    document.getElementById('input_area_admin').classList.remove('d-none');
                    document.getElementById('admin_area').classList.add('active');
                }
                else{
                    document.getElementById('admin_area').classList.remove('active');
                    document.getElementById('input_area_admin').classList.add('d-none');
                }
            })
            document.getElementById('abrir_mod_ali').addEventListener('click', () =>{
                !!document.getElementById('traslado_csv_aliado-msg') && document.getElementById('traslado_csv_aliado-msg').remove();
                document.getElementById('tras_otro_aliado').classList.add('d-none');
                document.getElementById('agregar_tras').classList.add('d-none');
                limpiarTab();
            });
            document.querySelector('input[name=traslado_csv_aliado]').addEventListener('change', e=>{
                document.getElementById('celdasVacias_tras').classList.add('d-none');
                document.getElementById('errorEscritura_tras').classList.add('d-none');
                document.getElementById('serial_nexi_tras').classList.add('d-none');
                document.getElementById('serial_dupli_tras').classList.add('d-none');
                document.getElementById('tras_ser_div').classList.add('d-none');
                document.getElementById('agregar_tras').classList.add('d-none');
                let file = e.target.files[0];
                let type_file = file.type;
                let countError = 0;
                if (type_file == 'application/vnd.ms-excel' || type_file == 'text/csv') {
                    let file_sav = document.getElementById('traslado_csv_aliado').files[0];
                    let datos_sav_entre = new FormData();
                    datos_sav_entre.append('save_csv_traslado', file_sav);
                    datos_sav_entre.append('entre_data_traslado', JSON.stringify(workData));
                    if(validarSesion(location.pathname+location.search)) {
                        pro_ajax = $.ajax({
                            url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                            data: datos_sav_entre,
                            method:'POST',
                            contentType:false,
                            cache:false,
                            processData:false,
                            beforeSend: function(){
                            },
                            dataType : "json",
                            success:function(data){
                                if(data.success){
                                    document.getElementById('tras_otro_aliado').classList.remove('d-none');
                                    document.getElementById('traslado_csv_aliado').value = '';
                                    if(data.errores_vac.length > 0){
                                        document.getElementById('celdasVacias_tras').classList.remove('d-none');
                                        let dat = data.errores_vac;
                                        $('#tablaCelVac_tras').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "CELDA", data: "celda"},
                                                { title: "ERROR", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countCelVac_tras').innerHTML = data.errores_vac.length;
                                        countError++;
                                    }
                                    if(data.errores_Es.length > 0){
                                        document.getElementById('errorEscritura_tras').classList.remove('d-none')
                                        let dat = data.errores_Es;
                                        $('#tablaEscritura_tras').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "ERROR", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countEscritura_tras').innerHTML = data.errores_Es.length;
                                        countError++;
                                    }
                                    if(data.nexi.length > 0){
                                        document.getElementById('serial_nexi_tras').classList.remove('d-none');
                                        let dat = data.nexi;
                                        $('#tabla_ser_nexi').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "CELDA", data: "celda"},
                                                { title: "ERROR", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('count_ser_nexi').innerHTML = data.nexi.length;
                                        countError++;
                                    }
                                    if(data.errorduplic.length > 0){
                                        document.getElementById('serial_dupli_tras').classList.remove('d-none');
                                        let dat = data.errorduplic;
                                        $('#tabla_ser_dupli').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "CELDA", data: "celda"},
                                                { title: "ERROR", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('count_ser_dupli').innerHTML = data.errorduplic.length;
                                        countError++;
                                    }
                                    if(!!data.seriales_guar.length != 0 && countError == 0){
                                        document.getElementById('agregar_tras').classList.remove('d-none');
                                        document.getElementById('tras_ser_div').classList.remove('d-none');
                                        let dat = data.seriales_guar;
                                        $('#tabla_tras_ser_guar').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "CELDA", data: "celda"},
                                                { title: "SERIAL", data: "serial"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        let countOk = data.seriales_guar.length;
                                        document.getElementById('countCant_tras_ser').innerHTML = countOk;
                                        dat.forEach(e=>{
                                            let arr = data_cod_cate.find(a=>a.codigo == e.codigo);
                                            codTabla.datosTabla.push({
                                                descripcion:arr.descripcion,
                                                medida:arr.medida,
                                                codigo:e.codigo,
                                                serial:e.serial,
                                                cantidad:e.cantidad,
                                                precio:'',
                                                odc:'',
                                                observaciones:''
                                            })
                                        })
                                        setTimeout(() => {
                                            agregarTraslado();
                                        }, 100); 
                                    }
                                    if(countError != 0){
                                        new createDivMsg('traslado_csv_aliado', `Archivo con Errores, Debe Corregir`);
                                    }
                                    else{
                                        !!document.getElementById('traslado_csv_aliado-msg') && document.getElementById('traslado_csv_aliado-msg').remove();
                                    }
                                }
                                else{
                                    Swal.fire({
                                        title: data.title_error,
                                        html:`${data.msg} ${data.error}`, 
                                        icon: 'error',
                                        confirmButtonText: 'Ok'
                                    })
                                    document.getElementById('traslado_csv_aliado').value = '';
                                    document.getElementById('tras_otro_aliado').classList.add('d-none');
                                }
                            },
                            error: function( jqXHR, textStatus, errorThrown ) {
                                error_ajax( jqXHR, textStatus, errorThrown );
                            }
                        });
                    };
                }
                else{
                    Swal.fire({
                        position: "center",
                        title: 'Tipo de archivo no válido',
                        icon: 'error',
                        type: 'info',
                        html: 'Por favor, subir un archivo .csv',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    document.getElementById('traslado_csv_aliado').value = '';
                }
            });
            document.getElementById('select_aliado').addEventListener('change',()=>{
                let select = document.getElementById('select_aliado');
                let selectedOption = select.options[select.selectedIndex];
            });
            document.getElementById('icon_add_aliado').addEventListener('click', ()=>{
                document.getElementById('add_aliado_email_msg').classList.add('d-none');
                [...document.querySelectorAll('.aliado_class ')].forEach(e=>{
                    !!document.getElementById(`${e.id}-msg`) && document.getElementById(`${e.id}-msg`).remove();
                });
                document.getElementById('add_aliado_form').reset();
            });
            document.getElementById('add_aliado_form').addEventListener('submit', (e) => {
                e.preventDefault();
                if(validarSesion(location.pathname+location.search)) {
                    document.getElementById('add_aliado_email_msg').classList.add('d-none');
                    let email = document.getElementById('add_aliado_email').value;
                    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    let cont = 0;
                    [...document.querySelectorAll('.aliado_class ')].forEach(e=>{
                        if(e.value == ''){
                            new createDivMsg(e.id, "Campo obligatorio");
                            document.getElementById('add_aliado_email_msg').classList.add('d-none');
                            cont++;
                        }
                        else if(!regex.test(email)){
                            !!document.getElementById('add_aliado_email-msg') && document.getElementById('add_aliado_email-msg').remove();
                            document.getElementById('add_aliado_email_msg').classList.remove('d-none');
                        };
                    });
                    if(cont == 0 && !!regex.test(email)){
                        [...document.querySelectorAll('.aliado_class ')].forEach(e=>{
                            !!document.getElementById(`${e.id}-msg`) && document.getElementById(`${e.id}-msg`).remove();
                        });
                        const save_aliado = {
                            nit:document.getElementById('add_aliado_nit').value,
                            nom_aliado:document.getElementById('add_aliado_name').value,
                            direccion:document.getElementById('add_aliado_addres').value,
                            email:document.getElementById('add_aliado_email').value,
                            nom_contact:document.getElementById('add_aliado_contact').value,
                            tel:document.getElementById('add_aliado_celu').value,
                            categoria:workData.categoria,
                            ciudad:workData.sede
                        };
                        pro_ajax = $.ajax({
                            url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                            method:'POST',
                            data: {
                                save_aliado: JSON.stringify(save_aliado)
                            },
                            dataType : "json",
                            async: false,
                            success:  function(data){
                                if(data.success){
                                    Swal.fire({
                                        title: data.msg,
                                        icon: 'success',
                                        confirmButtonText: 'Ok'
                                    });
                                    dataAliado.push({
                                        id_aliado:save_aliado.nit+workData.categoria,
                                        nombre_aliado:save_aliado.nom_aliado,
                                        nit:save_aliado.nit,
                                    });
                                    llamarAliado();
                                    document.getElementById('close_aliado').click();
                                }
                                else{
                                    Swal.fire({
                                    title: data.msg,
                                    icon: 'error',
                                    confirmButtonText: 'Ok'
                                });
                                }
                            },
                            error: function( jqXHR, textStatus, errorThrown ) {
                                error_ajax( jqXHR, textStatus, errorThrown );
                            }
                        });
                    };
                };
            });
            saldoNor();
            saldoSer();
            document.getElementById('guar_reporte').addEventListener('click',e=>{
                if(validarSesion(location.pathname+location.search)) {
                    if(!!data_sal_arr.length && !!data_sal_arr.find(e=> e.cobro!='')){
                        Swal.fire({
                            title: `Desea procesar los cobros?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#42ba96', 
                            cancelButtonColor: '#dd3333',
                            confirmButtonText: `procesar`,
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                reporteSave('report_all');
                            }
                        });
                    }
                    else{
                        reporteSave('report_all');
                    };
                    
                }
            });
            document.getElementById('eli_reporte').addEventListener('click',e=>{
                Swal.fire({
                    title: `Desea Eliminar Todos Los Reportes?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#42ba96',
                    cancelButtonColor: '#dd3333',
                    confirmButtonText: `Eliminar`,
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        data_sal_arr = [];
                        dataSalTotal.forEach(e=>{
                            e.movi = 0
                            e.ico = `<i class="icon_abrir text-primary c_pointer mr-4 fa-solid fa-pen-to-square" data-cod="${e.codigo}" data-ser="${e.serial}" id="${e.codigo}_${e.serial}"  data-bs-toggle="modal" data-bs-target="#modal_saldos"></i>`
                        });
                        dataTables();
                        document.getElementById('guar_reporte').classList.add('pe-none');
                        document.getElementById('eli_reporte').classList.add('pe-none');
                    }
                });
            });
            document.getElementById('form_sal_mod').addEventListener('submit',a=>{
                a.preventDefault();
                if(validarSesion(location.pathname+location.search)) {
                    !!document.getElementById('baja_sal-msg') && document.getElementById('baja_sal-msg').remove();
                    if(!data_sal_mod.serial){
                        data_sal_mod.devolucion = document.getElementById('devolucion_sal').value;
                        data_sal_mod.baja = document.getElementById('baja_sal').value ;
                        data_sal_mod.cobro = document.getElementById('cobro_sal').value ;
                        data_sal_mod.observaciones = document.getElementById('obs_sal').value;
                        if(document.getElementById('baja_sal').value != '' && document.getElementById('obs_sal').value == ''){
                            new createDivMsg('obs_sal', "Campo obligatorio");
                            return false;
                        }
                        else if(document.getElementById('baja_sal').value == '' && !!document.getElementById('cobro_sal').parentNode.classList.contains('d-none')){
                            new createDivMsg('baja_sal', "Campo obligatorio");
                            return false;
                        }
                        else{
                            !!document.getElementById('baja_sal-msg') && document.getElementById('baja_sal-msg').remove();
                            if(!data_sal_arr.find(e=> e.codigo == data_sal_mod.codigo && e.serial=='')){
                                data_sal_arr.push({
                                    cedula:data_sal_mod.cedula,
                                    codigo:data_sal_mod.codigo,
                                    serial:data_sal_mod.serial,
                                    devolucion:data_sal_mod.devolucion,
                                    baja:data_sal_mod.baja,
                                    cobro:data_sal_mod.cobro,
                                    observaciones:data_sal_mod.observaciones
                                });
                            }
                            let dev = !!data_sal_mod?.devolucion ? data_sal_mod?.devolucion : 0;
                            let baja = !!data_sal_mod?.baja ? data_sal_mod?.baja : 0;
                            let cobro = !!data_sal_mod?.cobro ? data_sal_mod?.cobro : 0;
                            let dat1 = dataSalTotal.find(e=>e.codigo == data_sal_mod.codigo && data_sal_mod.serial == '');
                            dat1.movi = `
                            <span class="text-success">${dev} </span>
                            <span class="text-danger">${baja} </span>
                            <span class="text-primary">${cobro}</span>
                            `;
                            dat1.ico = `
                            <i class="saldoNor fa-solid c_pointer fa-trash text-danger" data-nor="${data_sal_mod.codigo}" data-sed="${data_sal_mod.serial}"></i>
                            `;
                            setTimeout(()=>{
                                saldoNor();
                                saldoSer();
                                
                            },100)
                            document.getElementById('cancelar_saldo').click();
                            data_sal_mod = {
                                cedula:'',
                                codigo:'',
                                serial:'',
                                devolucion:'',
                                baja:'',
                                cobro:'',
                                observaciones:''
                            };
                        }
                    }
                    else{
                        !!document.getElementById('devolucion_sal').checked && (data_sal_mod.devolucion = '1');
                        !!document.getElementById('baja_sal').checked && (data_sal_mod.baja = '1');
                        !!document.getElementById('cobro_sal').checked && (data_sal_mod.cobro = '1');
                        data_sal_mod.observaciones = document.getElementById('obs_sal').value;
                        if (!data_sal_arr.find(e=> e.serial == data_sal_mod.serial)){
                            data_sal_arr.push({
                                cedula:data_sal_mod.cedula,
                                codigo:data_sal_mod.codigo,
                                serial:data_sal_mod.serial,
                                devolucion:data_sal_mod.devolucion,
                                baja:data_sal_mod.baja,
                                cobro:data_sal_mod.cobro,
                                observaciones:data_sal_mod.observaciones
                            });
                        }    
                        let dat = dataSalTotal.find(e=>e.serial == data_sal_mod.serial);
                        if(data_sal_mod.cobro != ''){
                            dat.movi = `<span class="text-success">0</span><span class="mr-1 ml-1 text-danger">0</span><span class="text-primary">1</span>`;
                        }
                        else if(data_sal_mod.baja != ''){
                            dat.movi = `<span class="text-success">0</span><span class="mr-1 ml-1 text-danger">1</span><span class="text-primary">0</span>`;
                        }
                        else{
                            dat.movi = `<span class="text-success">1</span><span class="mr-1 ml-1 text-danger">0</span><span class="text-primary">0</span>`;
                        }
                        dat.ico = `
                            <i class="saldoSer fa-solid c_pointer fa-trash text-danger" data-sal="${data_sal_mod.codigo}" data-ser="${data_sal_mod.serial}"></i>
                            `;
                        setTimeout(()=> {
                            saldoSer();
                            saldoNor();
                        },200);
                        
                        document.getElementById('cancelar_saldo').click();
                        data_sal_mod = {
                            cedula:'',
                            codigo:'',
                            serial:'',
                            devolucion:'',
                            baja:'',
                            cobro:'',
                            observaciones:''
                        };
                    }
                    [...document.querySelectorAll('.icon_guar')].forEach(a=> {
                        setTimeout(()=>{
                            a.addEventListener('click', e=>{
                                let dat_ser = e.target?.dataset?.serg;
                                let dat_nor = e.target?.dataset?.codg;
                            });
                        },200);
                    });
                    dataTables();
                }
            });
            tabInico();
            document.getElementById('guardar_sal_mod').addEventListener('click', e=>{
                document.getElementById('guar_reporte').classList.remove('pe-none');
                document.getElementById('eli_reporte').classList.remove('pe-none');
            })
            document.getElementById('btn-card-codigos').addEventListener('click', e=>{
                document.getElementById('div_mov').classList.add('d-none');
                document.getElementById('div_mov_kardex').classList.add('d-none');
                dataTablesCodigos();
                workData.movimiento = '';
                workData.cedula = '';
                document.querySelector('.container__left').classList.add('bg-primary');
                reloadTab();
                setTimeout(()=>{
                    if(!!workData.sede){
                        document.getElementById('work_sede').textContent = `${workData.sede.toCapitalizeCase()}`;
                    };
                },100);
            });
            document.getElementById('btn-card-kardex').addEventListener('click', e=>{
                
                setTimeout(()=>workData.movimiento = 'entrada',100);
                workData.cedula = '';
                document.querySelectorAll('.rad_mat_mov').forEach(e=>e.parentNode.classList.remove('active'));
                document.getElementById('entrada_label').parentNode.classList.add('active');
                document.querySelector('.container__left').classList.remove('bg-primary');
                document.getElementById('div_mov').classList.add('d-none');
                document.getElementById('inp_kar_ser').value = '';
                document.getElementById('inp_kar').value =  '';
                bodega_kardex = [];
                document.getElementById('inp_kar_ser').classList.remove('d-none');
                reloadTab();
                if(!!workData.categoria){
                    document.querySelector('#sel_sede').parentElement.classList.remove('pe-none');
                }
                if(!!workData.sede){
                    document.getElementById('work_sede').innerHTML = workData.sede;
                    document.getElementById('div_mov_kardex').classList.remove('d-none');
                    document.getElementById('mostrar_tecnico').classList.remove('d-none');
                    document.getElementById('tecnico_movimiento_inv').classList.remove('d-none');
                };
                document.getElementById('tecnico_movimiento_inv').classList.remove('pe-none');
                document.querySelector('#codigo_buscar').classList.remove('active');
                redload();
            });
            document.querySelector('#btn-card-invent').addEventListener('click', e=>{
                document.querySelector('.container__left').classList.add('bg-primary');
                document.getElementById('div_mov_kardex').classList.add('d-none');
                document.getElementById('saldos_tecnicos').classList.remove('d-none');
                reloadTab();
                if(!!workData.categoria){
                    document.querySelector('#sel_sede').parentElement.classList.remove('pe-none');
                    
                };
                if(!!workData.sede){
                    document.getElementById('work_sede').innerHTML = workData.sede;
                    document.getElementById('mostrar_tecnico').classList.remove('d-none');
                };
                redload();
                setTimeout(()=>{
                document.getElementById('div_mov').classList.add('d-none');
                document.getElementById('guar_reporte').classList.add('pe-none');
                document.getElementById('eli_reporte').classList.add('pe-none');
                document.getElementById('tecnico_movimiento_inv').classList.remove('pe-none');
                dataSaldos = [];
                dataSalTotal = [];
                dataTables();
                },100);
            });
            document.querySelector('#btn-card-admin').addEventListener('click', e=>{
                document.querySelector('.container__left').classList.add('bg-primary');
                document.getElementById('div_mov_kardex').classList.add('d-none');
                reloadTab();
                if(!!workData.categoria){
                    document.querySelector('#sel_sede').parentElement.classList.remove('pe-none');
                };
                if(!!workData.sede){
                    document.getElementById('work_sede').innerHTML = workData.sede;
                };
                document.getElementById('div_mov').classList.remove('d-none');
                redload();
            });
            document.querySelector('#inven_permisos table').addEventListener('click', e=>{ 
                !!document.getElementById('msg_sede_perm-msg') && document.getElementById('msg_sede_perm-msg').remove();
                document.querySelector('#tabla_asignar').classList.remove('d-none');
                addPermiMod();
                addPermiCat('general');
                document.querySelector('.l_general_perm').click();
            });
            document.querySelector('#resultado_tecnico_movimiento_inv table').addEventListener('click', e => {
                cc_saldo = document.getElementById('cc_tecnico_movimiento_inv').value;
                setTimeout(()=>{
                getSaldos();
                },100);
                document.getElementById('guar_reporte').classList.add('pe-none');
                document.getElementById('eli_reporte').classList.add('pe-none');
            });
            document.querySelector('.close_permi').addEventListener('click', e => {
                !!document.getElementById('msg_sede_perm-msg') && document.getElementById('msg_sede_perm-msg').remove();
                document.querySelector('.l_general_perm').click();
                document.getElementById('inven_permisos_search').value = '';
                document.querySelector('#tabla_asignar tbody').innerHTML = '';
                document.querySelector('#tabla_asignar').classList.add('d-none');
            });
            document.querySelector('.cerrar_permi').addEventListener('click', e => {
                !!document.getElementById('msg_sede_perm-msg') && document.getElementById('msg_sede_perm-msg').remove();
                document.querySelector('.l_general_perm').click();
                document.getElementById('inven_permisos_search').value = '';
                document.querySelector('#tabla_asignar tbody').innerHTML = '';
                document.querySelector('#tabla_asignar').classList.add('d-none');
            });
            abrirModalCate();
            actModaAddlCate();
            document.getElementById('form_mod_cod').addEventListener('submit', a =>{
                a.preventDefault();
                if(validarSesion(location.pathname+location.search)) {
                    if(!saveCodigo.area.length){
                        saveCodigo.area = ['GENERAL'];
                    };
                    saveCodigo.codigo = document.getElementById("codItem").value.toUpperCase();
                    saveCodigo.descripcion = document.getElementById("descripItem_material").value.toUpperCase();
                    saveCodigo.precio = document.getElementById("precioItem").value;
                    saveCodigo.medida = document.getElementById("medida_material").value;
                    let val_form = new validateForm(a.target).validate();
                    if(!!val_form){
                        document.getElementById('codItem').classList.remove('is-invalid');
                        document.getElementById('precioItem').classList.remove('is-invalid');
                        document.getElementById('descripItem_material').classList.remove('is-invalid');
                        pro_ajax = $.ajax({
                            url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                            method:'POST',
                            data: {
                                saveCod: JSON.stringify(saveCodigo)
                            },
                            dataType : "json",
                            success: function(data){
                                if(data.success){
                                    Swal.fire({
                                        title: data.msg,
                                        icon: 'success',
                                        confirmButtonText: 'Ok',
                                    })
                                    document.querySelector('#i_close').click(); 
                                    let msg_inp = a.target.form?.querySelectorAll('.inp-error');
                                    if(!!msg_inp){
                                        for(let el of msg_inp){
                                            let id_inp = el.id;
                                            !!document.getElementById(`${id_inp}-msg`) && document.getElementById(`${id_inp}-msg`).remove();
                                        }
                                        !!document.getElementById(`descripItem_material-msg`) && document.getElementById(`descripItem_material-msg`).remove();
                                    }
                                    saveCodigo.area = saveCodigo.area.join(',');
                                    data_cod_cate = data_cod_cate.map(e=>{
                                        if(e.codigo == saveCodigo.codigo){
                                            e.area = saveCodigo.area;
                                        }
                                        return e;
                                    });
                                    if(!document.getElementById('btn-card-codigos').classList.contains('active')){
                                        data_cod_cate.push({
                                            area:saveCodigo.area,
                                            categoria:saveCodigo.categoria,
                                            codigo:saveCodigo.codigo,
                                            descripcion:saveCodigo.descripcion,
                                            medida:saveCodigo.medida,
                                            precio:saveCodigo.precio,
                                            subcategoria:saveCodigo.subcategoria,
                                            tipo:saveCodigo.tipo
                                        });
                                    }
                                    setTimeout(()=> saveCodigo.area = [],100);
                                    if(!!document.getElementById('btn-card-codigos').classList.contains('active')){
                                        let data_cod = data_cod_cate.filter(e=>e.categoria == workData.categoria);
                                        dataTablesCodigos();
                                    }
                                }else{
                                    if(!!data.msg_err){
                                        Swal.fire({
                                            title: data.msg,
                                            html:data.msg_err,
                                            icon: 'error',
                                            confirmButtonText: 'Ok'
                                        });
                                        for(el of document.querySelectorAll('.vaciar_form')){
                                            el.value = '';
                                        };
                                    }else{
                                        
                                        Swal.fire({
                                                title: data.msg,
                                                html:data.duplicado,
                                                icon: 'error',
                                                confirmButtonText: 'Ok'
                                        });
                                        for(el of document.querySelectorAll('.vaciar_form')){
                                            el.value = '';
                                        };
                                    }
                                }
                            },
                            error: function( jqXHR, textStatus, errorThrown ) {
                                error_ajax( jqXHR, textStatus, errorThrown );
                            }
                        });
                    } 
                }
            });
            document.querySelector('#familia').addEventListener('submit', e => {
                e.preventDefault();
                if(validarSesion(location.pathname+location.search)) {
                    !!document.getElementById('familia_elemento-msg') && document.getElementById('familia_elemento-msg').remove();
                    !!document.getElementById('inp_add_sede-msg') && document.getElementById('inp_add_sede-msg').remove();
                    if (document.getElementById('fila_sedes').textContent == '') {
                        new createDivMsg('inp_add_sede', `Para Agregar una Sede Presione: <i class="fas fa-check-circle ml-3 text-success"></i>`);
                        // document.getElementById('sede_msg').removeAttribute('hidden');
                        document.getElementById('inp_add_sede').classList.add('is-invalid');
                        return false;
                    }
                    if(document.getElementById("familia_elemento").value.trim() == '') {
                        Swal.fire({
                        title: 'Debe Ingresar Una Categoria',
                        icon: 'warning',                
                        confirmButtonText: 'ok'
                        });
                        
                        document.getElementById('categoria_msg').removeAttribute('hidden');
                        document.getElementById('familia_elemento').classList.add('is-invalid');
                        return false;
                    }
                    saveNewCate();
                }
            });
            document.querySelector('input[name=entrega_csv_codigos]').addEventListener('change', (e) => {
                for (el of document.querySelectorAll('.acordeonEntrega')){
                    el.classList.add('d-none');
                };
                let file = e.target.files[0];
                let type_file = file.type;
                if (type_file == 'application/vnd.ms-excel' || type_file == 'text/csv') {
                    document.getElementById('countCelVacEntre').innerHTML = 0;
                    document.getElementById('countEscrituraente').innerHTML = 0;
                    document.getElementById('countCodigoNoExi').innerHTML = 0;
                    document.getElementById('countentregaOk').innerHTML = 0;
                    let countError = 0;
                    let file_sav = document.getElementById('entrega_csv_codigos').files[0];
                    let datos_sav_entre = new FormData();
                    datos_sav_entre.append('save_csv_entregaCodigo', file_sav);
                    datos_sav_entre.append('entre_data', JSON.stringify(workData));
                    
                    if(validarSesion(location.pathname+location.search)) {
                        pro_ajax = $.ajax({
                            url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                            data: datos_sav_entre,
                            method:'POST',
                            contentType:false,
                            cache:false,
                            processData:false,
                            beforeSend: function(){
                            },
                            dataType : "json",
                            success:function(data){
                                if(data.success){
                                    dataEntra = data.codigos_guar;
                                    document.getElementById('entregaInventario').classList.remove('d-none');
                                    document.getElementById('entrega_csv_codigos').value = '';
                                    if(data.celdas_vacias.length > 0){
                                        document.getElementById('celdasVaciasEntre').classList.remove('d-none');
                                        let dat = data.celdas_vacias;
                                        $('#tablaCelVacEntre').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countCelVacEntre').innerHTML = data.cnt_celvac;
                                        countError++;
                                    }
                                    if(data.err_escritura.length > 0){
                                        document.getElementById('errorEscrituraEntre').classList.remove('d-none')
                                        let dat = data.err_escritura;
                                        $('#tablaEscrituraErrEntre').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "Descripcion", data: "descripcion"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countEscrituraente').innerHTML = data.cnt_erres;
                                        countError++;
                                    }
                                    if(data.codigo_nexi.length > 0){
                                        document.getElementById('codigosNoExiEntre').classList.remove('d-none');
                                        let dat = data.codigo_nexi;
                                        $('#tablaCodigoNoExi').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countCodigoNoExi').innerHTML = data.cnt_nexi;
                                        countError++;
                                    }
                                    if(data.cedula_err.length > 0){
                                        document.getElementById('cedulaNoExiEntre').classList.remove('d-none');
                                        let dat = data.cedula_err;
                                        $('#tablaCedulaNoExi').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Cedula", data: "cedula"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countCedulaNoExi').innerHTML = data.cedula_err.length;
                                        countError++;
                                    }
                                    if(data.codigos_guar.length != 0){
                                        document.getElementById('codigosNormalesEntre').classList.remove('d-none');
                                        let dat = data.codigos_guar;
                                        $('#tabla_entre_ok').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Codigo", data: "codigo"},
                                                { title: "Cedula", data: "cedula"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        let countOk = data.codigos_guar.length;
                                        document.getElementById('countentregaOk').innerHTML = countOk;
                                    }
                                    if(data.codigos_guar_ser.length != 0){
                                        document.getElementById('entreSerializados').classList.remove('d-none');
                                        let dat = data.codigos_guar_ser;
                                        $('#tabla_entre_ok_serial').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Serial", data: "serial"},
                                                { title: "Cedula", data: "cedula"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        let countOk = data.codigos_guar_ser.length;
                                        document.getElementById('countEntregaOkSerial').innerHTML = countOk;
                                    }
                                    if(data.exis_ser_base.length != 0 ){
                                        document.getElementById('serialNoExiste').classList.remove('d-none');
                                        let dat = data.exis_ser_base;
                                        $('#tablaSerialNoExis').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countSerialNoExis').innerHTML = data.exis_ser_base.length;
                                        countError++;
                                    }
                                    if(data.serial_duplicado.length != 0 ){
                                        document.getElementById('serialDupliEntre').classList.remove('d-none');
                                        let dat = data.serial_duplicado;
                                        $('#tablaSerialesDupliEntre').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns:[
                                                { title: "Celda", data: "celda"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countSerialDupliEntre').innerHTML = data.serial_duplicado.length;
                                        countError++;
                                    }
                                    if(data.cantidad_err.length != 0 ){
                                        document.getElementById('cantidadSerialEntre').classList.remove('d-none');
                                        let dat = data.cantidad_err;
                                        $('#tablacantidadSerialEntre').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countCantidaSerEntre').innerHTML = data.cantidad_err.length;
                                        countError++;
                                    }
                                    if(countError != 0){
                                        new createDivMsg('entrega_csv_codigos', `Archivo con Errores, Debe Corregir`);
                                    }
                                    else{
                                        !!document.getElementById('entrega_csv_codigos-msg') && document.getElementById('entrega_csv_codigos-msg').remove();
                                    }
                                }
                                else{
                                    Swal.fire({
                                        title: data.title_error,
                                        html:`${data.msg} ${data.error}`, 
                                        icon: 'error',
                                        confirmButtonText: 'Ok'
                                    })
                                    document.getElementById('entrega_csv_codigos').value = '';
                                    document.getElementById('entradaInventario').classList.add('d-none');
                                }
                            },
                            error: function( jqXHR, textStatus, errorThrown ) {
                                error_ajax( jqXHR, textStatus, errorThrown );
                            }
                        });
                    };
                }
                else{
                    Swal.fire({
                        position: "center",
                        title: 'Tipo de archivo no válido',
                        icon: 'error',
                        type: 'info',
                        html: 'Por favor, subir un archivo .csv',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    document.getElementById('entrega_csv_codigos').value = '';
                }
            });
            document.querySelector('input[name=entrada_csv_codigos]').addEventListener('change', (e) => {
                for (el of document.querySelectorAll('.acordeonEntra')){
                    el.classList.add('d-none');
                };
                let file = e.target.files[0];
                let type_file = file.type;
                if (type_file == 'application/vnd.ms-excel' || type_file == 'text/csv') {
                    document.getElementById('countCelVac').innerHTML = 0;
                    document.getElementById('countEscritura').innerHTML = 0;
                    document.getElementById('countCodigoExi').innerHTML = 0;
                    document.getElementById('countCodigoOk').innerHTML = 0;
                    let countError = 0;
                    let file_sav = document.getElementById('entrada_csv_codigos').files[0];
                    let datos_sav = new FormData();
                    datos_sav.append('save_csv_entradaCodigo', file_sav);
                    datos_sav.append('entra_data', JSON.stringify(workData));
                    
                    if(validarSesion(location.pathname+location.search)) {
                        pro_ajax = $.ajax({
                            url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                            data: datos_sav,
                            method:'POST',
                            contentType:false,
                            cache:false,
                            processData:false,
                            beforeSend: function(){
                            },
                            dataType : "json",
                            success:function(data){
                                if(data.success){
                                    dataEntra = data.codigos_guar;
                                    document.getElementById('entradaInventario').classList.remove('d-none');
                                    document.getElementById('entrada_csv_codigos').value = '';
                                    if(data.celdas_vacias.length > 0){
                                        document.getElementById('celdasVacias').classList.remove('d-none');
                                        let dat = data.celdas_vacias;
                                        $('#tablaCelVac').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countCelVac').innerHTML = data.cnt_celvac;
                                        countError++;
                                    }
                                    if(data.err_escritura.length > 0){
                                        document.getElementById('errorEscritura').classList.remove('d-none')
                                        let dat = data.err_escritura;
                                        $('#tablaEscrituraErr').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "Descripcion", data: "descripcion"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countEscritura').innerHTML = data.cnt_erres;
                                        countError++;
                                    }
                                    if(data.codigo_nexi.length > 0){
                                        document.getElementById('codigosNoExi').classList.remove('d-none');
                                        let dat = data.codigo_nexi;
                                        $('#tablaCodigoExi').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countCodigoExi').innerHTML = data.cnt_nexi;
                                        countError++;
                                    }
                                    if(data.codigos_guar.length != 0){
                                        document.getElementById('codigosNormales').classList.remove('d-none');
                                        let dat = data.codigos_guar;
                                        $('#tabla_t_ok').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Codigo", data: "codigo"},
                                                { title: "Descripcion", data: "descripcion"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        let countOk = data.codigos_guar.length;
                                        document.getElementById('countCodigoOk').innerHTML = countOk;
                                    }
                                    if(data.codigos_guar_ser.length != 0){
                                        document.getElementById('codigosSerializados').classList.remove('d-none');
                                        let dat = data.codigos_guar_ser;
                                        $('#tabla_t_ok_serial').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Codigo", data: "codigo"},
                                                { title: "Descripcion", data: "descripcion"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        let countOk = data.codigos_guar_ser.length;
                                        document.getElementById('countCodigoOkSerial').innerHTML = countOk;
                                    }
                                    if(data.exis_ser_base.length != 0 ){
                                        document.getElementById('serialExiste').classList.remove('d-none');
                                        let dat = data.exis_ser_base;
                                        $('#tablaSerialExis').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Serial", data: "serial"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countSerialExis').innerHTML = data.exis_ser_base.length;
                                        countError++;
                                    }
                                    if(data.serial_duplicado.length != 0 ){
                                        document.getElementById('serialDupli').classList.remove('d-none');
                                        let dat = data.serial_duplicado;
                                        $('#tablaSerialesDupli').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countSerialDupli').innerHTML = data.serial_duplicado.length;
                                        countError++;
                                    }
                                    if(data.cantidad_err.length != 0 ){
                                        document.getElementById('cantidadSerial').classList.remove('d-none');
                                        let dat = data.cantidad_err;
                                        $('#tablacantidadSerial').DataTable({
                                            destroy:true,
                                            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
                                            buttons: dataTable_all_btn,
                                            data: dat,
                                            columns: [
                                                { title: "Celda", data: "celda"},
                                                { title: "Error", data: "error"}
                                            ],
                                            responsive: true,
                                            language: dataTable_espanol
                                        });
                                        document.getElementById('countCantidaSer').innerHTML = data.cantidad_err.length;
                                        countError++;
                                    }
                                    if(countError != 0){
                                        new createDivMsg('entrada_csv_codigos', `Archivo con Errores, Debe Corregir`);
                                    }
                                    else{
                                        !!document.getElementById('entrada_csv_codigos-msg') && document.getElementById('entrada_csv_codigos-msg').remove();
                                    }
                                }
                                else{
                                    Swal.fire({
                                        title: data.title_error,
                                        html:`${data.msg} ${data.error}`, 
                                        icon: 'error',
                                        confirmButtonText: 'Ok'
                                    })
                                    document.getElementById('entrada_csv_codigos').value = '';
                                    document.getElementById('entradaInventario').classList.add('d-none');
                                }
                            },
                            error: function( jqXHR, textStatus, errorThrown ) {
                                error_ajax( jqXHR, textStatus, errorThrown );
                            }
                        });
                    };
                }
                else{
                    Swal.fire({
                        position: "center",
                        title: 'Tipo de archivo no válido',
                        icon: 'error',
                        type: 'info',
                        html: 'Por favor, subir un archivo .csv',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    document.getElementById('entrada_csv_codigos').value = '';
                    document.getElementById('entrada_csv_codigos').value = '';
                }
            });
            
            document.querySelector('input[name=import_csv_codigos]').addEventListener('change', (e) => {
                let file = e.target.files[0];
                let type_file = file.type;
                if(type_file == 'application/vnd.ms-excel' || type_file == 'text/csv') {
                    let file_sav = document.getElementById('import_csv_codigos').files[0];
                    let datos_sav = new FormData();
                    datos_sav.append('import_data', JSON.stringify(workData));
                    datos_sav.append('save_csv_codigos', file_sav); // post $_FILES['save_csv_codigos']ss
                    pro_ajax = $.ajax({
                        url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                        data: datos_sav,
                        method:'POST',
                        dataType:"json",
                        contentType:false,
                        cache:false,
                        processData:false,
                        beforeSend: function(){
                            // $("#loader").show();
                        },
                        success:function(data){
                            
                            if(data.success){
                                new createDivMsg('import_csv_codigos', `Archivo con Errores, Debe Corregir`);
                                document.getElementById('imp_cod_guar').classList.add('d-none');
                                document.getElementById('importInventario').classList.remove('d-none');
                                document.getElementById('imp_es').classList.add('d-none');
                                document.getElementById('cod_imp').classList.add('d-none');
                                document.getElementById('imp_cel').classList.add('d-none');
                                document.getElementById('import_csv_codigos').value = '';

                                document.getElementById('countCelVacImp').innerHTML = '0';
                                if(data.errores_vac.length > 0){
                                    document.getElementById('imp_cel').classList.remove('d-none');
                                    let dat = data.errores_vac;
                                    $('#tablaCelVacImp').DataTable({
                                        destroy:true,
                                        lengthChange:false,
                                        data: dat,
                                        columns: [
                                            { title: "Celda", data: "celda"},
                                            { title: "Error", data: "error"}
                                        ],
                                        responsive: true,
                                        language: dataTable_espanol,
                                    });
                                    document.getElementById('countCelVacImp').innerHTML = data.cantidad_err_va;
                                }
                                document.getElementById('countEscrituraImp').innerHTML = '0';
                                if(data.errores_Es.length > 0){
                                    document.getElementById('imp_es').classList.remove('d-none');
                                    let dat = data.errores_Es;
                                    $('#tablaEscrituraErrImp').DataTable({
                                        destroy:true,
                                        lengthChange:false,
                                        data: dat,
                                        columns: [
                                            { title: "Celda", data: "celda"},
                                            { title: "Descripcion", data: "descripcion"}
                                        ],
                                        responsive: true,
                                        language: dataTable_espanol,
                                    });
                                    document.getElementById('countEscrituraImp').innerHTML = data.cantidad_err_Es;
                                }
                                document.getElementById('countCodigoExiImp').innerHTML = '0';
                                if(data.errorduplic.length > 0){
                                    document.getElementById('cod_imp').classList.remove('d-none');
                                    let dat = data.errorduplic;
                                    $('#tablaCodigoExiImp').DataTable({
                                        destroy:true,
                                        lengthChange:false,
                                        data: dat,
                                        columns: [
                                            { title: "Codigo", data: "codigo"},
                                            { title: "Celda", data: "celda"}
                                        ],
                                        responsive: true,
                                        language: dataTable_espanol,
                                    });
                                    document.getElementById('countCodigoExiImp').innerHTML = data.cantidadDuplic;
                                }
                                document.getElementById('countCodigoimportOk').innerHTML = '0';
                                if(!data.errores_vac.length  && !data.errores_Es.length && !data.errorduplic.length){
                                    document.getElementById('imp_cod_guar').classList.remove('d-none');
                                    let dat = data.codigos_correctos;
                                    dat.forEach(e=>{
                                        data_cod_cate.push({
                                        categoria: e.categoria,
                                        subcategoria: e.subcategoria,
                                        descripcion:e.descripcion,
                                        medida:e.medida,
                                        codigo:e.codigo,
                                        precio:e.precio,
                                        tipo:e.tipo,
                                        area:['general']
                                    });
                                    });
                                    $('#tabla_import_ok').DataTable({
                                        destroy:true,
                                        lengthChange:false,
                                        data: dat,
                                        columns: [
                                            { title: "Codigo", data: "codigo"},
                                            { title: "Descripcion", data: "descripcion"}
                                        ],
                                        responsive: true,
                                        language: dataTable_espanol,
                                    });
                                    let countOk = data.codigos_correctos.length;
                                    !!document.getElementById('import_csv_codigos-msg') && document.getElementById('import_csv_codigos-msg').remove();
                                    document.getElementById('codGuardados').textContent = 'Se Guardaron Los Siguientes Codigos:'
                                    document.getElementById('countCodigoimportOk').innerHTML = countOk;
                                }
                                
                            }
                            else{
                                Swal.fire({
                                    title: data.title_error,
                                    html:data.msg, 
                                    icon: 'error',
                                    confirmButtonText: 'Ok'
                                })
                                document.getElementById('import_csv_codigos').value = '';
                                document.getElementById('importInventario').classList.add('d-none');
                            }
                        },
                        error: function( jqXHR, textStatus, errorThrown ) {
                            error_ajax( jqXHR, textStatus, errorThrown );
                        }
                    });
                } 
                else{
                    Swal.fire({
                        position: "center",
                        title: 'Tipo de archivo no válido',
                        icon: 'error',
                        type: 'info',
                        html: 'Por favor, subir un archivo .csv',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
            document.getElementById('iconBuscar').addEventListener('click',(e) => {
                if(!!document.getElementById('iconBuscar').classList.contains('ti')){
                    document.getElementById('iconBuscar').classList.remove('ti');
                    document.getElementById('iconBuscar').classList.add('fa-search');
                    document.getElementById('iconBuscar').classList.remove('text-black-50');
                    document.getElementById('iconBuscar').classList.add('text-primary');
                }
                else{
                    document.getElementById('iconBuscar').classList.add('ti');
                    document.getElementById('iconBuscar').classList.remove('fa-search');
                    document.getElementById('iconBuscar').classList.add('text-black-50');
                    document.getElementById('iconBuscar').classList.remove('text-primary');
                };
            });
            document.getElementById('buscarDes').addEventListener('input',(e) =>{
                let val = e.target.value;
                let res_des = document.querySelector('#tablaBuscar tbody');
                res_des.innerHTML = '';
                let filtroDes = data_cod_cate.filter(e => e.descripcion.includes(val.toUpperCase()));
                if(filtroDes.length > 0 && val != ''){

                    let count1 = 0;
                    let count2 = 0;
                    filtroDes.forEach(e => {
                        if(count1 < 10){
                            let num = e.precio
                            let new_tr = document.createElement('tr');
                            let new_td = document.createElement('td');
                            let new_td1 = document.createElement('td');
                            let new_td2 = document.createElement('td');
                            let new_td3 = document.createElement('td');
                            new_tr.classList.add('link_tr');
                            new_td.textContent = e.codigo;
                            new_td1.textContent = e.descripcion;
                            new_td2.textContent = e.tipo;
                            new_td3.textContent = num;
                            new_td.classList.add('c_pointer', 'text-decoration-underline', 'text-primary', 'w-25', 'pl-2', 'text-justify', 'fz-07');
                            new_td1.classList.add('c_pointer', 'text-decoration-underline', 'pl-2', 'text-justify', 'fz-07');
                            new_td2.classList.add('c_pointer', 'text-decoration-underline', 'pl-2', 'text-justify', 'fz-07');
                            new_td3.classList.add('c_pointer', 'text-decoration-underline', 'pl-2', 'text-justify', 'fz-07');
                            new_tr.appendChild(new_td);
                            new_tr.appendChild(new_td1);
                            new_tr.appendChild(new_td2);
                            new_tr.appendChild(new_td3);
                            res_des.appendChild(new_tr);
                            count2++;
                        }
                        else{
                            return true;
                        }
                        count1++;
                    });
                }
                else{
                    res_des.innerHTML = '';
                }
                for (let el of document.querySelector("#bodyBuscar").querySelectorAll('.link_tr')) {
                    el.addEventListener('click', (e) =>{
                        document.getElementById('select_codigo').value = e.target.parentNode.children[0].textContent;
                        const event = new Event('input');  
                        document.querySelector('#select_codigo').dispatchEvent(event);
                        res_des.innerHTML = '';
                        document.getElementById('buscarDes').value = '';
                        [...document.querySelectorAll('.btn-close')].map(el => el.click());
                    });
                };
                for (let el of document.querySelectorAll('.cerrarB')){
                    el.addEventListener('click', (e) =>{
                        res_des.innerHTML = '';
                        document.getElementById('buscarDes').value = '';
                    });
                };
            });            
            document.getElementById('fecha_movimiento').addEventListener('input', () => {
                workData.fecha = document.getElementById('fecha_movimiento').value;
                codTabla.fecha = workData.fecha;
            });
            document.getElementById('ticketPerson').addEventListener('input', () => {
                workData.ticket = document.getElementById('ticketPerson').value;
                codTabla.ticket = workData.ticket;
            });
            document.getElementById('btn_add_sedes').addEventListener('click', () => {
                //createSubCaSede('sede');
                !!document.getElementById('inp_add_sede-msg') && document.getElementById('inp_add_sede-msg').remove();
                let categoria = document.getElementById('familia_elemento').value.toCapitalizeCase().trim();
                let newSede = document.getElementById('inp_add_sede').value.toLowerCase().toCapitalizeCase().trim();
                if(newSede == '') return;
                if(!!saveDataCate.sedes.find(c => c.sede == newSede)){
                    !!document.getElementById('inp_add_sede-msg') && document.getElementById('inp_add_sede-msg').remove();
                    new createDivMsg('inp_add_sede', `Sede: ${newSede} ya existe`);
                    // document.getElementById('sede_msg').setAttribute('hidden','');
                    document.getElementById('inp_add_sede').classList.remove('is-invalid');
                    return;
                }
                let cedula_admin_cate = '<?php echo $_SESSION['cedula'] ?>'
                const permiso = {
                    admin: [cedula_admin_cate],
                    editor: [],
                    lector: []
                };
                saveDataCate.sedes = [...saveDataCate.sedes, {sede: newSede, permisos:permiso}];
                document.getElementById('inp_add_sede').value = ''; 
                crearSedes();
            });
            document.getElementById('btn_add_sub_cat').addEventListener('click',(i)=>{
                //createSubCaSede('sub_cat');
                
                let categoria = document.getElementById('familia_elemento').value.toCapitalizeCase().trim();
                let newSubCate = document.getElementById('sub_familia').value.toLowerCase().toCapitalizeCase().trim();
                
                let exisSubCat = saveDataCate.sub_cate.find(c => c == newSubCate);
                if(newSubCate == '') return;
                if(!!exisSubCat){
                    new createDivMsg('sub_familia', `Sub Categoría: ${newSubCate} ya existe`);
                    return;
                }
                saveDataCate.sub_cate = [...saveDataCate.sub_cate, newSubCate];
                !!document.getElementById('sub_familia-msg') && document.getElementById('sub_familia-msg').remove()
                document.getElementById('sub_familia').value = '';
                crearSubcate();
            });
            document.getElementById('tecnico_movimiento_inv').addEventListener('change', (e) => {
                if(!!document.getElementById('btn-card-kardex').classList.contains('active')){
                    document.getElementById('tarjeta_tecnico_movimiento_inv').classList.add('d-none');
                }
                else{
                    document.getElementById('tarjeta_tecnico_movimiento_inv').classList.remove('d-none');
                }
                dbTarjeta(e.target.id);
            });
            document.getElementById('serialCodigo').addEventListener('input', (e) => {
                if(e.target.value.length > 2){
                    
                    e.target.value = e.target.value.toUpperCase();
                    let valorCod = e.target.value;
                    document.getElementById('obsCodigo').value = '';
                    document.getElementById("sel_cod_msg").setAttribute('hidden','');
                    valSerial = dataSerial.find(e => e.serial == valorCod.toUpperCase());
                    if ((workData.movimiento == 'entrega' || workData.movimiento == 'devolucion') && valSerial != undefined) {
                        !!document.getElementById('serialCodigo-msg') && document.getElementById('serialCodigo-msg').remove();
                        document.getElementById('cantCodigo').value = 1;
                        document.getElementById("sel_serial_msg").setAttribute('hidden','');
                        document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                        document.getElementById('odcCodigo').value = valSerial.odc;
                        let dat = valSerial.precio;
                        document.getElementById('select_codigo').value = valSerial.codigo;
                        let result = data_cod_cate.find(e=> e.codigo == valSerial.codigo);
                        document.getElementById('select_descripcion').textContent = result.descripcion;
                        document.getElementById('medidaCod').textContent = result.medida;
                        document.getElementById('precioCodigo').value = dat.split('.')[0];
                        document.getElementById('precioTarjet').textContent = dat.split('.')[0];
                        addItemMov();
                        setTimeout(()=> document.getElementById('cantCodigo').value = '',100);
                    }
                    if (workData.movimiento == 'baja'  && valSerial != undefined) {
                        !!document.getElementById('serialCodigo-msg') && document.getElementById('serialCodigo-msg').remove();
                        document.getElementById('cantCodigo').value = 1;
                        document.getElementById("sel_serial_msg").setAttribute('hidden','');
                        document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                        document.getElementById('odcCodigo').value = valSerial.odc;
                        let dat = valSerial.precio;
                        document.getElementById('select_codigo').value = valSerial.codigo;
                        let result = data_cod_cate.find(e=> e.codigo == valSerial.codigo);
                        document.getElementById('select_descripcion').textContent = result.descripcion;
                        document.getElementById('medidaCod').textContent = result.medida;
                        document.getElementById('precioCodigo').value = dat.split('.')[0];
                        document.getElementById('precioTarjet').textContent = dat.split('.')[0];
                        document.getElementById('obsCodigo').classList.remove('pe-none');
                        document.getElementById('obsCodigo').classList.remove('bg-gray-200');
                        document.getElementById('obsCodigo').value = '';
                        document.getElementById('obsCodigo').focus();
                        document.getElementById('agregar_item').classList.remove('pe-none');
                    }
                    else if(workData.movimiento == 'entrada' && valSerial != undefined){
                        new createDivMsg('serialCodigo', 'Serial ya esta registrado');
                        document.getElementById('cantCodigo').classList.add('pe-none');
                        document.getElementById('cantCodigo').classList.add('bg-gray-200');
                        document.getElementById('cantCodigo').value = '';
                        document.getElementById("sel_serial_msg").setAttribute('hidden','');
                        document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                        document.getElementById('obsCodigo').classList.remove('pe-none');
                        document.getElementById('obsCodigo').classList.remove('bg-gray-200');
                        document.getElementById('obsCodigo').value = '';
                        document.getElementById('precioCodigo').classList.add('pe-none');
                        document.getElementById('precioCodigo').classList.add('bg-gray-200');
                        document.getElementById('precioCodigo').value = '';
                        document.getElementById('precioTarjet').textContent = '';
                        document.getElementById('select_descripcion').textContent = '';
                        document.getElementById('medidaCod').textContent = ''; 
                        document.getElementById('odcCodigo').value = '';
                        document.getElementById('odcCodigo').classList.add('pe-none');
                        document.getElementById('odcCodigo').classList.add('bg-gray-200');
                        document.getElementById('agregar_item').classList.add('pe-none');
                    }
                    else if (workData.movimiento != 'entrada' && valSerial == undefined){
                        !!document.getElementById('serialCodigo-msg') && document.getElementById('serialCodigo-msg').remove();
                        document.getElementById("sel_serial_msg").removeAttribute('hidden');
                        document.getElementById("sel_serial_msg1").setAttribute('hidden', '');
                        document.getElementById('odcCodigo').classList.add('pe-none');
                        document.getElementById('odcCodigo').classList.add('bg-gray-200');
                        document.getElementById('odcCodigo').value = '';
                        document.getElementById('select_descripcion').textContent = '';
                        document.getElementById('medidaCod').textContent = ''; 
                        document.getElementById('precioCodigo').value = '';
                        document.getElementById('obsCodigo').value = '';
                        document.getElementById('precioTarjet').textContent = '';
                        document.getElementById('obsCodigo').classList.add('pe-none');
                        document.getElementById('obsCodigo').classList.add('bg-gray-200');
                        document.getElementById('precioCodigo').classList.add('pe-none');
                        document.getElementById('precioCodigo').classList.add('bg-gray-200');
                        document.getElementById('cantCodigo').classList.add('pe-none');
                        document.getElementById('cantCodigo').classList.add('bg-gray-200');
                        document.getElementById('cantCodigo').value = '';
                        document.getElementById('agregar_item').classList.add('pe-none');
                        if(document.getElementById('serialCodigo').value == ''){
                            document.getElementById("sel_serial_msg").setAttribute('hidden','');
                            document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                        }
                    }
                    else{
                        document.getElementById('agregar_item').classList.remove('pe-none');
                        document.getElementById("sel_serial_msg").setAttribute('hidden','');
                        document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                        !!document.getElementById('serialCodigo-msg') && document.getElementById('serialCodigo-msg').remove();
                        document.getElementById('precioCodigo').classList.remove('pe-none');
                        document.getElementById('precioCodigo').classList.remove('bg-gray-200');
                        document.getElementById('odcCodigo').classList.remove('pe-none');
                        document.getElementById('odcCodigo').classList.remove('bg-gray-200');
                        document.getElementById('cantCodigo').value = 1;
                        document.getElementById('odcCodigo').value = '';
                        document.getElementById('precioCodigo').value = '';
                        document.getElementById('obsCodigo').value = '';
                        document.getElementById('precioTarjet').textContent = '';
                        if(document.getElementById('serialCodigo').value == ''){
                            document.getElementById("sel_serial_msg").setAttribute('hidden','');
                            document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                            !!document.getElementById('serialCodigo-msg') && document.getElementById('serialCodigo-msg').remove();
                        }
                    }
                }
                else{
                    !!document.getElementById('serialCodigo-msg') && document.getElementById('serialCodigo-msg').remove();
                    document.getElementById("sel_serial_msg1").removeAttribute('hidden');
                    document.getElementById("sel_serial_msg").setAttribute('hidden','');
                    document.getElementById('cantCodigo').value = '';
                    if(document.getElementById('serialCodigo').value == ''){
                        !!document.getElementById('serialCodigo-msg') && document.getElementById('serialCodigo-msg').remove();
                        document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                    }
                }
            });
            document.getElementById('select_codigo').addEventListener('input', (e) => {
                e.target.value = e.target.value.toUpperCase();
                let valorCod = e.target.value;
                let res_des = document.querySelector('#tabla_cod_buscar tbody');
                res_des.innerHTML = '';
                let filtroDes = data_cod_cate.filter(e => e.codigo.includes(valorCod) || e.descripcion.includes(valorCod));
                if(filtroDes.length > 0 && valorCod != '' && !!document.getElementById('iconBuscar').classList.contains('fa-search')){
                    document.getElementById('div_tabla_cod_buscar').classList.remove('d-none');
                    let count1 = 0;
                    let count2 = 0;
                    filtroDes.forEach(e => {
                        if(count1 < 10){
                            let new_tr = document.createElement('tr');
                            let new_td = document.createElement('td');
                            let new_td1 = document.createElement('td');
                            new_tr.classList.add('link_tr_cod');
                            new_td.textContent = e.codigo;
                            new_td1.textContent = e.descripcion;
                            new_td.classList.add('c_pointer', 'text-primary', 'pl-2', 'text-justify', 'fz-07');
                            new_td1.classList.add('c_pointer', 'pl-2', 'text-justify', 'fz-07');
                            new_tr.classList.add('border-dark');
                            new_tr.appendChild(new_td);
                            new_tr.appendChild(new_td1);
                            res_des.appendChild(new_tr);
                            count2++;
                        }
                        count1++;
                    });
                    document.getElementById('countBuscar').classList.remove('d-none');
                    document.getElementById('countBuscar').textContent = `mostrando ${count2} de ${filtroDes.length}`
                }
                else{
                    res_des.innerHTML = '';
                    document.getElementById('div_tabla_cod_buscar').classList.add('d-none');
                    document.getElementById('countBuscar').classList.add('d-none');
                }
                !!document.getElementById('serialCodigo-msg') && document.getElementById('serialCodigo-msg').remove();
                document.getElementById("sel_serial_msg").setAttribute('hidden','');
                document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                valData = data_cod_cate.find(e => e.codigo == valorCod.toUpperCase());
                document.getElementById('obsCodigo').value = '';
                document.getElementById('odcCodigo').value = '';
                
                document.getElementById('precioTarjet').textContent = '';
                if (valData == undefined) {
                    document.getElementById('serialCodigo').value = '';
                    document.getElementById("sel_cod_msg").removeAttribute('hidden');
                    document.getElementById('serialCodigo').classList.remove('pe-none');
                    document.getElementById('serialCodigo').classList.remove('bg-gray-200');
                    document.getElementById('select_descripcion').textContent = '';
                    document.getElementById('medidaCod').textContent = '';
                    document.getElementById('precioTarjet').textContent = '';  
                    document.getElementById('precioCodigo').value = '';
                    document.getElementById('obsCodigo').value = '';
                    document.getElementById('odcCodigo').value = '';
                    document.getElementById('obsCodigo').classList.add('pe-none');
                    document.getElementById('obsCodigo').classList.add('bg-gray-200');
                    document.getElementById('precioCodigo').classList.add('pe-none');
                    document.getElementById('precioCodigo').classList.add('bg-gray-200');
                    document.getElementById('odcCodigo').classList.add('pe-none');
                    document.getElementById('odcCodigo').classList.add('bg-gray-200');
                    document.getElementById('cantCodigo').classList.add('pe-none');
                    document.getElementById('cantCodigo').classList.add('bg-gray-200');
                    document.getElementById('cantCodigo').value = '';
                    document.getElementById('agregar_item').classList.add('pe-none');
                    if(document.getElementById('select_codigo').value == ''){
                        document.getElementById("sel_cod_msg").setAttribute('hidden','');
                        document.getElementById("sel_serial_msg1").setAttribute('hidden','');
                    };
                }
                else
                {
                    if(valData.tipo == "SERIALIZADO"){
                        document.getElementById('div_tabla_cod_buscar').classList.add('d-none');
                        document.getElementById('serialCodigo').value = '';
                        document.getElementById('odcCodigo').value = '';
                        document.getElementById('cantCodigo').classList.add('pe-none');
                        document.getElementById('cantCodigo').classList.add('bg-gray-200');
                        document.getElementById('cantCodigo').value = 1;
                        document.getElementById("sel_cod_msg").setAttribute('hidden','');
                        document.getElementById('select_descripcion').textContent = valData.descripcion;
                        document.getElementById('medidaCod').textContent = valData.medida;
                        document.getElementById('obsCodigo').classList.remove('pe-none');
                        document.getElementById('obsCodigo').classList.remove('bg-gray-200');
                        document.getElementById('serialCodigo').classList.remove('pe-none');
                        document.getElementById('serialCodigo').classList.remove('bg-gray-200');
                        document.getElementById('precioCodigo').classList.remove('pe-none');
                        document.getElementById('precioCodigo').classList.remove('bg-gray-200');
                        document.getElementById('odcCodigo').classList.remove('pe-none');
                        document.getElementById('odcCodigo').classList.remove('bg-gray-200');
                        document.getElementById('agregar_item').classList.remove('pe-none');
                        document.getElementById('serialCodigo').focus();
                    }
                    else{
                        
                        document.getElementById('cantCodigo').classList.remove('pe-none');
                        document.getElementById('cantCodigo').classList.remove('bg-gray-200');
                        document.getElementById('cantCodigo').focus();
                        document.getElementById("sel_cod_msg").setAttribute('hidden','');
                        document.getElementById('cantCodigo').value = '';
                        document.getElementById('select_descripcion').textContent = valData.descripcion;
                        document.getElementById('medidaCod').textContent = valData.medida;
                        document.getElementById('serialCodigo').classList.add('pe-none');
                        document.getElementById('serialCodigo').classList.add('bg-gray-200');
                        document.getElementById('obsCodigo').classList.remove('pe-none');
                        document.getElementById('obsCodigo').classList.remove('bg-gray-200');
                        document.getElementById('precioCodigo').classList.remove('pe-none');
                        document.getElementById('precioCodigo').classList.remove('bg-gray-200');
                        //document.getElementById('precioCodigo').value = valData.precio.split('.')[0];
                        document.getElementById('odcCodigo').classList.remove('pe-none');
                        document.getElementById('odcCodigo').classList.remove('bg-gray-200');
                        document.getElementById('agregar_item').classList.remove('pe-none');
                        setTimeout(() => {
                            document.getElementById('cantCodigo').focus();
                        }, 500);
                    }
                    
                }
                clickCod();
            });
            document.getElementById('formInventario').addEventListener('submit', (e)=>{
                e.preventDefault();
                addItemMov();
                let nom = document.getElementById('select_codigo').value;
                
            });
            document.getElementById('movInvent').addEventListener('click',(e)=>{
                let val_btn = document.getElementById('movInvent').value;
                Swal.fire({
                    title: `Desea ${val_btn} Estos Items?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#42ba96',
                    cancelButtonColor: '#dd3333',
                    confirmButtonText: `${val_btn}`,
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('ticketPerson').value = reset_ticket();
                        if(validarSesion(location.pathname+location.search)) {
                            pro_ajax = $.ajax({
                                url: "./controladores/control_inventario_controlador/controlador/control_inventario_controlador.php",
                                method:'POST',
                                data: {
                                    saveCodTabla: JSON.stringify(codTabla)
                                },
                                dataType : "json",
                                success: function(data){
                                    if(data.success){
                                        Swal.fire({
                                            title: data.msg,
                                            icon: 'success',
                                            confirmButtonText: 'Ok'
                                        });
                                        if(workData.movimiento == 'entrada'){
                                            codTabla.datosTabla.forEach(e=> {
                                                if(e.serial != ''){
                                                    dataSerial.push(e)}
                                                })
                                            }
                                        getSeriales();
                                        // DivAPdf();
                                        limpiarTab();
                                    }
                                    else{
                                        if(!!data.msg_err){
                                            Swal.fire({
                                                title: data.msg,
                                                html: data.msg_err,
                                                icon: 'error',
                                                confirmButtonText: 'Ok'
                                            });
                                        }
                                        else{
                                            Swal.fire({
                                                title: data.msg,
                                                icon: 'error',
                                                confirmButtonText: 'Ok'
                                            });
                                        }
                                        if (workData.movimiento != 'entrada'){
                                            setTimeout(()=>{
                                                document.getElementById('serialCodigo').focus(); 
                                        },200);
                                        }
                                        else{
                                            setTimeout(()=>{
                                                document.getElementById('select_codigo').focus(); 
                                        },200);
                                        }
                                    }
                                },
                                error: function( jqXHR, textStatus, errorThrown ) {
                                    error_ajax( jqXHR, textStatus, errorThrown );
                                }
                            });
                        }
                    }
                });
            });
            document.getElementById('limpiarIvent').addEventListener('click',(e)=>{
                Swal.fire({
                    title: `Desea Limpiar la Tabla?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dd3333',
                    cancelButtonColor: '#898989',
                    confirmButtonText: 'Limpiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        limpiarTab();
                    }
                });
            });
            let categoria = document.getElementById('familia_elemento').value.toCapitalizeCase();
            document.getElementById('familia_elemento').addEventListener('input',()=> {
                valCateExist();
                createEditcat();
            });
            document.getElementById('addModalC').addEventListener('click', ()=>{
                !!document.getElementById(`import_csv_codigos-msg`) && document.getElementById(`import_csv_codigos-msg`).remove();
                document.getElementById('importInventario').classList.add('d-none');
                document.getElementById('modalCodigo').classList.remove('d-none');
                document.getElementById('modalImp').classList.add('d-none');
                document.getElementById('guardar_cod').classList.remove('d-none');
                
                document.getElementById('plan_imp_cod').classList.add('d-none');
                document.getElementById('ocultarImport').classList.remove('d-none');
            });
            document.getElementById('addImport').addEventListener('click',()=>{
                document.getElementById('modalImp').classList.remove('d-none');
                document.getElementById('modalCodigo').classList.add('d-none');
                
                document.getElementById('guardar_cod').classList.add('d-none');
                document.getElementById('plan_imp_cod').classList.remove('d-none');
                document.getElementById('ocultarImport').classList.add('d-none');
            });
            document.getElementById('item_cod_serial').addEventListener('click',()=>checkCodSerial());
            mod_cod_inven.addEventListener('click',()=>{
                document.getElementById('h5_cod_mod').textContent = 'Agregar Codigo';
                document.getElementById('h5_cod_mod').parentNode.classList.add('bg-primary');
                document.getElementById('h5_cod_mod').parentNode.classList.remove('bg-success');
                document.getElementById('guardar_cod').textContent = 'Guardar';
                document.getElementById('guardar_cod').classList.add('bg-primary');
                document.getElementById('guardar_cod').classList.remove('bg-success');
                document.getElementById('ocultarImport').classList.remove('d-none');
                // document.getElementById('ope_admin').parentNode.classList.add('d-none');
                document.getElementById('importInventario').classList.add('d-none');
                document.getElementById('form_mod_cod').reset();
                document.getElementById('precioItem').classList.remove('pe-none');
                document.getElementById('descripItem_material').classList.remove('pe-none');
                !!document.getElementById(`import_csv_codigos-msg`) && document.getElementById(`import_csv_codigos-msg`).remove();
                !!document.getElementById('codItem-msg') && document.getElementById('codItem-msg').remove();
                checkCodSerial();
                let msg_inp = document.getElementById('form_mod_cod')?.querySelectorAll('.inp-error');
                if(!!msg_inp){
                    for(let el of msg_inp){
                        let id_inp = el.id;
                        !!el.classList.contains('inp-error') && el.classList.remove('inp-error');
                        !!el.classList.contains('border-bottom-danger') && el.classList.remove('border-bottom-danger');
                        !!document.getElementById(`${id_inp}-msg`) && document.getElementById(`${id_inp}-msg`).remove();
                    }
                    !!document.getElementById(`descripItem_material-msg`) && document.getElementById(`descripItem_material-msg`).remove();
                };
                jsonAreaCarp();
                let dat1 = [];
                let admi = [];
                let dat = json_area_carp.filter(e=>e.tipo_area == 'OPERATIVA').map(e=> dat1.push(e.area));
                let operativa = json_area_carp.filter(e=>e.tipo_area == 'ADMINISTRATIVA').map(e=> admi.push(e.area));
                dat1 = [...new Set(dat1)];
                admi = [...new Set(admi)];
                funActive('',dat1,admi);
                codExisCate();
            });
            document.getElementById('btn_cerrar_modal').addEventListener('click',(e)=>{
                document.getElementById('familia').reset();
                document.getElementById('fila_sedes').innerHTML = '';
                document.getElementById('fila_subcate').innerHTML = '';
                !!document.getElementById('familia_elemento-msg') && document.getElementById('familia_elemento-msg').remove();
                createSelFam('sel_fam');
            });
            document.querySelector('.close-edit').addEventListener('click',(e)=>{
                document.getElementById('familia').reset();
                document.getElementById('fila_sedes').innerHTML = '';
                document.getElementById('fila_subcate').innerHTML = '';
                !!document.getElementById('familia_elemento-msg') && document.getElementById('familia_elemento-msg').remove();
                createSelFam('sel_fam');
            });
            document.getElementById('guardar_cod').addEventListener('click',(e)=>{
                document.getElementById('guadarCodigo').click();
            });
            document.getElementById('entradaCod').addEventListener('click',(e) => {
                document.getElementById('entradaInventario').classList.add('d-none');
                document.getElementById('entrada_csv_codigos').value = '';
                !!document.getElementById('entrada_csv_codigos-msg') && document.getElementById('entrada_csv_codigos-msg').remove();
                
            });
            document.getElementById('entregaCod').addEventListener('click',(e) => {
                document.getElementById('entregaInventario').classList.add('d-none');
                document.getElementById('entrega_csv_codigos').value = '';
                !!document.getElementById('entrega_csv_codigos-msg') && document.getElementById('entrega_csv_codigos-msg').remove();
                
            });
        });
    </script>
</body>
</html>
<?php  ?>