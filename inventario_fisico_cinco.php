<?php
error_reporting(0);
session_start();

function FechaDateTimeLocal($fecha){
    $z_in_fic = strtotime ('-5 hour',  strtotime ($fecha) ) ;
    $in_fic_num = date ( 'YmdHis', $z_in_fic); //20210121210000
    $in_fic_print = date ('Y-m-d\TH:i:s' , $z_in_fic); //21:00:00 21/01/2021
    return  $in_fic_print;
}

$cedula = $_SESSION['cedula'];
$fechahoy = date('Y-m-d G:i:s', strtotime('-5 hour, -00 minutes, -00 seconds,', strtotime(date('Y-m-d G:i:s'))));
$fecha_str = strtotime($fechahoy);
$fecha_num = date('YmdHis', $fecha_str); //20210121210000
$fecha_print = date('d/m/Y H:i:s', $fecha_str);
$ticket = "{$cedula}{$fecha_num}";

include "../../configure.php";$acentos = $abc4n2x34n->query("SET NAMES 'utf8'");
require_once('../controller_cinco.php');
header("Content-Type: text/html; charset=utf-8");
$conn_log = connect_PDO('logistica');


if (isset($_POST['un_post'])) {

}
else {
    require 'header.php';
    ?>

    <style>
        *{
            box-sizing: border-box;
        }
        .nav-tabs .active {
            color: #0054cf !important;
        }
        :root{
            --bg: #FFFFFF;
        }
        .navigation{
            position: absolute;
            inset: 0 0 0 1px;
            background: #fff;
            width: 75px;
            height: max-content;
            justify-content: center;
            align-items: center;
            transition: 0.5s;
            z-index: 99;
        }
        .navigation.active{
            width: 250px;
        }
        .menuTog{
            position: absolute;
            top:0;
            left:0;
            width: 100%;
            height: 60px;
            padding: 0 23px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            display: flex;
            justify-content: flex-start;
            align-items: center;
            cursor: pointer;
        }
        .menuTog::before{
            content: '';
            position: absolute;
            width:30px;
            height: 2px;
            background: #fff;
            transform: translateY(-8px);
            transition: 0.5s;
        }
        .navigation.active .menuTog::before{
            transform: translateY(0) rotate(45deg);
        }
        .menuTog::after{
            content: '';
            position: absolute;
            width:30px;
            height: 2px;
            background: #fff;
            transform: translateY(8px);
            box-shadow: 0 -8px 0 #fff;
            transition: 0.5s;
        }
        .navigation.active .menuTog::after{
            transform: translateY(0) rotate(-45deg);
            box-shadow: 0 0 0 #fff;
        }
        .navigation ul{
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .navigation ul li{
            list-style: none;
            position: relative;
            width: 100%;
            border-radius: 12px 0  0 12px;
            border: 2px solid transparent;
            transition: 0.5s;
        }
        .navigation ul li.active {
            transform: translatex(25px);
            background: var(--bg);
        }
        .navigation ul li a {
            position: relative;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            width: 100%;
            margin: -11px;
            text-align: center;
            text-decoration: none;
            z-index: 1000;
        }
        .navigation ul li a .icon{
            position:relative;
            display: block;
            inset: 0 0 0 24px;
            /* min-width: 60px;
            height: 60px; */
            border-radius: 10px;
            font-size: 1.4em;
            /* background-color: #0275d8; */
            line-height: 60px;
            border: 6px solid transparent;
            transition: 0.5s;
        }
        .navigation ul li.active a .icon{
            color: #fff;
            background: var(--clr);
            margin: -12px;
        }
        .navigation ul li a .icon::before{
            content: '';
            position: absolute;
            top: 12px;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--clr);
            filter:blur(8px);
            opacity:0;
            transition: 0.5s;
        }
        .navigation ul li.active a .icon::before{
            opacity: 0.5;
        }
        .navigation ul li a .icon::after{
            content:'';
            position: absolute;
            top: 10px;
            left: -50px;
            width: 15px;
            height: 15px;
            background: var(--clr);
            border: 3px solid var(--bg);
            border-radius: 50%;
        }
        .navigation ul li a .text{
            position: relative;
            padding: 0 45px;
            color:#fff;
            display:flex;
            align-items: center;
            height:60px;
            opacity: 0;
            visibility: hidden;
            transition: 0.5s;
        }
        .navigation.active ul li a .text{
            visibility: visible;
            opacity: 1;
        }
        .navigation ul li.active a .text{
            color: #0054cf;
        }
        main{
            height: 100%;
        }
    </style>


    <!-- Contenedor principal -->
        <div class="container-fluid p-0">
            <div class="bg-primary d-flex" id="header_tabs">
                <div class="border-0 col-auto nav nav-tabs p-0" id="nav-tab" role="tablist">
                    <button class="btn_tab fw-bold nav-link text-white px-1 px-sm-3 active" id="tab_1-tab" data-bs-toggle="tab" data-bs-target="#tab_1" type="button" role="tab" aria-controls="tab_cons_mat" aria-selected="true">Tecnico</button>
                </div>
                <div class="border-0 col-auto nav nav-tabs p-0" id="nav-tab2" role="tablist">
                    <button class="btn_tab fw-bold nav-link text-white px-1 px-sm-3 " id="tab_2-tab" data-bs-toggle="tab" data-bs-target="#tab_2" type="button" role="tab" aria-controls="tab_cons_mat" aria-selected="true">Inventario</button>
                </div>
            </div>
        </div>
        <div class="h-100 tab-content" id="nav-tabContent">
            <!-- INICIO PRIMER TAB  -->
                <div class="tab-pane h-100 fade show active" id="tab_1" role="tabpanel" aria-labelledby="tab_1-tab">
                    <div class="row h-100 flex-nowrap">
                        <main class="col h-100 columns position-relative px-4">
                            <!-- CONTENIDO PRIMER TAB --> 
                            <div class="d-flex justify-content-center">
                                <div class="mr-5">
                                    <!-- AQUI IBA EL SIDE-VAR -->
                                    <div id="nav_side" class="bg-primary ml-n2 navigation h-100">
                                        <div class="menuTog ml-2"></div>
                                        <br><br><br>
                                        <ul class=" ml-n4">
                                            <li data-cod="home" class="list active">
                                                <a style="--clr:#54A4FF;">
                                                    <span class="icon"><i class="text-white fa-solid fa-house"></i></span>
                                                    <span class="text b ml-n2">
                                                        INICIO
                                                    </span> 
                                                </a>
                                            </li>
                                            <li data-cod="user" class="list">
                                                <a style="--clr:#54A4FF;">
                                                    <span class="icon"><i class="text-white fa-regular fa-user"></i></span>
                                                    <span class="text b">
                                                        USUARIO
                                                    </span>
                                                </a>
                                            </li>
                                            <li data-cod="not" class="list">
                                                <a style="--clr:#54A4FF;">
                                                    <span class="icon"><i class="text-white fas fa-bell"></i></span>
                                                    <span class="text b">
                                                        NOTIFICACIONES
                                                    </span>
                                                </a>
                                            </li>
                                            <li data-cod="fot" class="list">
                                                <a style="--clr:#54A4FF;">
                                                    <span class="icon"><i class="text-white fas fa-camera"></i></span>
                                                    <span class="text b">
                                                        FOTOS
                                                    </span>
                                                </a>
                                            </li>
                                            <li data-cod="confi" class="list">
                                                <a style="--clr:#54A4FF;">
                                                    <span class="icon"><i class="text-white fas fa-cog"></i></span>
                                                    <span class="text b">
                                                        CONFIGURACION
                                                    </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class=" mt-1 " style="width: 80%;">
                                    <span id="" class="span_fecha ml-2 text-secondary mb-0"></span>
                                    <div class="d-flex m-auto row justify-content-center p-2" style="border-radius: 5px; border: 1px solid lightgray;">

                                        <div class=" p-0">
                                            <div class="mb-0  d-flex form-group form-row g-2 p-0" id="div_tec_asoc_ots">
                                                <div class="col-xl-4 col-md-4 form-group  ml-1" id="div_tec_asoc">
                                                    <div class=" p-0" id="inven_tec" data-search-label="Tecnico:"></div>
                                                </div>
                                                <div class="col-md-4 col-xl-4 ml-1 form-group" id="">
                                                    <label for="" class="mb-0 b mr-2">Cédula:</label>
                                                    <input type="text" class="inp_clear form-control form-control-sm text-uppercase cc_tec_asoc" id="" placeholder="Cédula" disabled="" readonly>
                                                </div>
                                                <div class="col-md-3 col-xl-3 ml-1 form-group" id="">
                                                    <label for="" class="mb-0 b mr-2">Area:</label>
                                                    <input type="text" class="inp_clear form-control form-control-sm text-uppercase area_tec_asoc" id="" placeholder="Area" disabled="" readonly>
                                                </div>
                                                <!-- <div class="col-12 col-md-2 col-sm-6 form-group">
                                                    <span class="mb-0 b mr-2">Agregar Categoria:</span>
                                                    <i id="btn_add_cate" class="c_pointer fa-plus-circle fas my-auto text-primary" data-bs-toggle="modal" data-bs-target="#cateModal" ></i>
                                                </div> --> 
                                                
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <!-- ------------------INICIO Acordeon---------------- -->
                                    <form id="cate_" class="agregar_invent">
                                        <div class="acor accordion my-2" id="acor_cate">
                                            <h2 class="text-primary">Elja un Tecnico</h2>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <input type="submit" class="d-none agregar_invent btn-primary form-control form-control-sm text-uppercase mx-1 w-auto" id="movInvent" value="GUARDAR">
                                        </div>
                                    </form>
                                    <!-- -----------------FIN Acordeon----------------- -->
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
            <!-- FIN PRIMER TAB  -->
            <!-- INICIO SEGUNDO TAB -->
                <div class="tab-pane h-100 fade " id="tab_2" role="tabpanel" aria-labelledby="tab_2-tab">
                    <div class=" mt-1 ml-2 mr-2">
                        <span id="" class="span_fecha ml-2 text-secondary mb-0"></span>
                        <div class="d-flex m-auto row justify-content-center p-2" style="border-radius: 5px; border: 1px solid lightgray;">
                            <div class=" p-0">
                                <div class="mb-0  d-flex form-group form-row g-2 p-0" id="">
                                    <div class="col-xl-4 col-md-4 form-group  ml-1" id="">
                                        <div class=" p-0" id="inven_tec_saldo" data-search-label="Tecnico:"></div>
                                    </div>
                                    <div class="col-md-4 col-xl-4 ml-1 form-group" id="">
                                        <label for="" class="mb-0 b mr-2">Cédula:</label>
                                        <input type="text" class="inp_clear form-control form-control-sm text-uppercase cc_tec_asoc" id="" placeholder="Cédula" disabled="" readonly>
                                    </div>
                                    <div class="col-md-3 col-xl-3 ml-1 form-group" id="">
                                        <label for="" class="mb-0 b mr-2">Area:</label>
                                        <input type="text" class="inp_clear form-control form-control-sm text-uppercase area_tec_asoc" id="" placeholder="Area" disabled="" readonly>
                                    </div>
                                </div>
                                <div>
                                    <label for="" class="b mb-0 ml-1">Fecha:</label>
                                    <select id="sel_fecha" name="medItem_material" class="col-5 col-xl-2 form-control form-control-sm form-select ml-1 validate-form">
                                        <option value="" disabled="true">Seleccionar...</option>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- ------------------INICIO Acordeon---------------- -->
                            <div class="acor accordion my-2" id="acor_cate_tab_inven">
                                <h2 class="text-primary">Elja un Tecnico</h2>
                            </div>
                        <!-- -----------------FIN Acordeon----------------- -->
                    </div>
                </div>
            <!-- FIN SEGUNDO TAB -->
            <!-- inicio modal categoria -->
                <!-- <div class="modal fade " id="cateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h4 class="h4_cate text-white">Crear Categoria</h4>
                                <button type="button" class="btn-close close-edit" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="familia">
                                <div class="modal-body">
                                    <div class="d-flex justify-content-between">
                                        <a data-bs-toggle="dropdown" data-parent="#sidebar" aria-expanded="false">
                                            <div class="div_cate">
                                                <i class="fas fa-cubes"></i> <span class="text-uppercase" id="">Categorias</span>  
                                            </div>
                                        </a>
                                        <ul class="dropdown-menu " id="sel_category">
                                            
                                        </ul>
                                        
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="familia_elemento">Categoria:</label>
                                        <input id="familia_elemento" class="form-control form-control-sm" type="search" name="familia_elemento" autofocus="">
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
                                    <div class="bg-gray-300 d-flex flex-wrap gap-1 p-2 rounded-3" id="fila_subcate"></div>
                                    <div class="row" style="margin-top:20px;">
                                        <label for="inp_add_sede">Sedes:</label>
                                        <div class="col d-flex justify-content-between">
                                            <div class="form-group ">
                                                <input id="inp_add_sede" class="form-control form-control-sm" type="search" name="inp_add_sede">
                                            </div>  
                                            <div class="form-group col ">
                                                <i id="btn_add_sedes" class="fas fa-check-circle ml-3 text-success"></i>
                                            </div>
                                        </div>
                                    </div>   
                                    <div class="bg-gray-300 d-flex flex-wrap gap-1  p-2 rounded-3" id="fila_sedes"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" id="btn_env_revaluar">Enviar</button>
                                    <button type="button" class="btn btn-secondary" id="btn_cerrar_modal" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> -->
            <!-- fin modal categoria -->
            
        </div>
    <!-- Fin Contenedor principal -->

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
    
    <script>
        let fecha = '';
        new searchPersonal('inven_tec');
        new searchPersonal('inven_tec_saldo');
        // let cedula_sesion = '<?php echo $_SESSION['cedula'] ?>';
        let workData ={
            cedula:"",
            area:"", 
            tbody:"", 
            hoy: new Date().toLocaleDateString().split('/').reverse().join('-'),
            dataTecnico:[]
        }
        function valueFecha(){
            if(this.value == 'qwerty'){fecha = '';}
            else{fecha = this.value;}
            let data_guar ={fecha:fecha,cedula:workData.cedula};
            getInventFisico(data_guar);
        }
        function activeList(){
            document.querySelectorAll('.list').forEach(e=>{
                e.classList.remove('active');
                this.classList.add('active');
            });
        };
        function activeTab(){
            document.getElementById('sel_fecha').innerHTML = '';
            document.getElementById('sel_fecha').innerHTML = '<option value="" disabled="true">Seleccionar...</option>';
            fecha = '';
            document.getElementById("movInvent").classList.add('d-none');
            document.querySelectorAll('.inp_clear').forEach(e=>e.value= '');
            document.querySelector('#inven_tec_search').value = '';
            document.querySelector('#inven_tec_saldo_search').value = '';
            document.querySelectorAll('.acor').forEach(e=>e.innerHTML= '<h2 class="text-primary">Elja un Tecnico</h2>');
            document.querySelectorAll('.btn_tab').forEach(e=>{
                e.classList.remove('active');
                this.classList.add('active');
            });
            document.querySelectorAll('.fecha_sel')?.forEach(e=>e.classList.remove('active'));
        };
        function prom(){
            Swal.fire({
                title: `Desea Guardar Este Inventario?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#42ba96', 
                cancelButtonColor: '#dd3333',
                confirmButtonText: `Guardar`,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    saveInventario();
                    setTimeout(()=> document.querySelectorAll('.limpiar_inp').forEach(e=> e.value = ''),200);
                }
            });
        }
        function getInventario(){
            if(validarSesion(location.pathname+location.search)) {
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/controlador_inventario_fisico.php",
                    method:'POST',
                    data: {
                        getTecnico: workData.cedula
                    },
                    dataType : "json",
                    success: function(data){
                        workData.tbody = '';
                        document.getElementById('acor_cate').innerHTML = '';
                        workData.dataTecnico = data.flatMap(e=>e);
                        if(!!workData.dataTecnico.length){
                            document.getElementById("movInvent").classList.remove('d-none');
                            workData.dataTecnico.reduce((acc,e)=>{
                                let cont = 0;
                                workData.tbody = document.createElement('tbody');
                                if(!acc.includes(e.categoria)){
                                    acc.push(e.categoria);
                                    let categoria = e.categoria;
                                    let new_fil = workData.dataTecnico.filter(i=>i.categoria === e.categoria);
                                    new_fil.reduce((acc,i)=>{
                                        if(!acc.includes(i.codigo) || i.serial != ''){
                                            acc.push(i.codigo);
                                            let cantidad = 0;
                                            if(i.serial != ''){ cantidad = 1}//${cantidad}
                                            else{cantidad = workData.dataTecnico.filter(a=>a.categoria === i.categoria && a.codigo== i.codigo).reduce((a,b)=>a + +b.cantidad ,0);}
                                            let new_tr = document.createElement('tr');
                                            let new_td = document.createElement('td');
                                            let new_td1 = document.createElement('td');
                                            let new_td2 = document.createElement('td');
                                            let new_td3 = document.createElement('td');
                                            let new_inp = document.createElement('input');
                                            new_td.textContent = i.codigo;
                                            new_td1.textContent = i.descripcion;
                                            new_td2.textContent = i.serial;
                                            new_td3.classList.add('px-2');
                                            new_inp.classList.add('limpiar_inp');
                                            new_inp.setAttribute('type','number');
                                            new_inp.setAttribute('name',`${i.codigo}`);
                                            new_inp.setAttribute('for',`${cantidad}`);
                                            new_inp.setAttribute('dataser',`${i.serial}`);
                                            new_inp.setAttribute('data',`${categoria}`);
                                            new_inp.setAttribute('style','max-width: 60px;');
                                            new_td3.appendChild(new_inp);
                                            new_tr.appendChild(new_td);
                                            new_tr.appendChild(new_td1);
                                            new_tr.appendChild(new_td2);
                                            new_tr.appendChild(new_td3);
                                            workData.tbody.appendChild(new_tr);
                                            cont ++;
                                        }
                                        return acc;
                                    },[]);
                                    let div_super = document.createElement('div');
                                    div_super.classList.add('acordeon_cate','accordion-item')
                                    let span = document.createElement('span');
                                    span.classList.add('badge', 'badge-primary', 'border');
                                    span.textContent = cont;
                                    let div_sp = document.createElement('div');
                                    div_sp.classList.add('me-2');
                                    div_sp.appendChild(span);
                                    let span1 = document.createElement('span');
                                    span1.classList.add('ml-n2');
                                    span1.textContent = e.categoria;
                                    let div_c = document.createElement('div');
                                    div_c.classList.add('d-flex','justify-content-between','w-100');
                                    div_c.appendChild(span1);
                                    div_c.appendChild(div_sp);
                                    let button = document.createElement('button');
                                    button.classList.add('accordion-button','fw-bold','text-primary','collapsed','bg-gray-100');
                                    button.setAttribute('type','button');
                                    button.setAttribute('data-toggle','collapse');
                                    button.setAttribute('data-target',`#div_cate${e.categoria}`);
                                    button.setAttribute('aria-expanded','true');
                                    button.setAttribute('aria-controls',`div_cate${e.categoria}`);
                                    button.appendChild(div_c);
                                    let h2 = document.createElement('h2');
                                    h2.classList.add('accordion-header');
                                    h2.setAttribute('id',`h2_${e.categoria}`);
                                    h2.appendChild(button);
                                    div_super.appendChild(h2);
                                    let th = document.createElement('th');
                                    let th1 = document.createElement('th');
                                    let th2 = document.createElement('th');
                                    let th3 = document.createElement('th');
                                    th3.setAttribute('style', 'width: 90px;');
                                    th.textContent = 'CÓDIGO';
                                    th1.textContent = 'DESCRIPCIÓN';
                                    th2.textContent = 'SERIAL';
                                    th3.textContent = 'CANTIDAD';
                                    let tr = document.createElement('tr');
                                    tr.appendChild(th);
                                    tr.appendChild(th1);
                                    tr.appendChild(th2);
                                    tr.appendChild(th3);
                                    let thead = document.createElement('thead');
                                    thead.classList.add('bg-primary','text-white');
                                    thead.appendChild(tr);
                                    let table = document.createElement('table');
                                    table.classList.add('table_kardex','table','table-bordered','table-sm','text-center','table-hover','table-striped');
                                    table.setAttribute('id',`h2_${e.categoria}`);
                                    table.appendChild(thead);
                                    table.appendChild(workData.tbody);
                                    let div_t = document.createElement('div');
                                    div_t.setAttribute('style','max-height: 40vh;');
                                    div_t.classList.add('accordion-body','overflow-scroll', 'info-tables');
                                    div_t.appendChild(table);
                                    div_div = document.createElement('div');
                                    div_div.classList.add('accordion-collapse', 'collapse');
                                    div_div.setAttribute('id',`div_cate${e.categoria}`);
                                    div_div.setAttribute('data-parent','#acor_cate');
                                    div_div.setAttribute('aria-labelledby',`h2_${e.categoria}`);
                                    div_div.appendChild(div_t);
                                    div_super.appendChild(div_div);
                                    document.getElementById('acor_cate').appendChild(div_super);
                                };
                                return acc;
                            },[]);
                            setTimeout(()=>{
                                (function(){
                                    document.querySelectorAll(`.agregar_invent`).forEach(e=>{
                                        e.addEventListener('submit',i=>{
                                            i.preventDefault();
                                            let formDataObj = [...document.querySelector(`#${i.target.id}`).querySelectorAll("input[type='number']")].reduce((acc,e)=>{
                                                acc.push({codigo:e.getAttribute("name"),cantidad:e.value,saldo:e.getAttribute("for"),categoria:e.getAttribute("data"),serial:e.getAttribute("dataser")});
                                                return acc;
                                            },[]);
                                            workData.data_inven = formDataObj;
                                            console.log(workData.data_inven);
                                            prom();
                                        });
                                    });
                                })();
                            },200);
                        }
                        else{
                            document.getElementById("movInvent").classList.add('d-none');
                            document.getElementById('acor_cate').innerHTML = '<h2 class="text-primary">No Registra Informacion</h2>';
                        }
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                }); 
            };
        };
        function getInventFisico(data_guar){
            if(validarSesion(location.pathname+location.search)) {
                let dataSal = [];
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/controlador_inventario_fisico.php",
                    method:'POST',
                    data: {
                        getTecnicoInvent: JSON.stringify(data_guar)
                    },
                    dataType : "json",
                    success: function(data){
                        dataSal = data;
                        document.getElementById('acor_cate_tab_inven').innerHTML = '';
                        if(!!dataSal.length){
                            dataSal.reduce((acc,e)=>{
                                let cont = 0;
                                let tbody = document.createElement('tbody');
                                if(!acc.includes(e.categoria)){
                                    acc.push(e.categoria);
                                    let new_fil = dataSal.filter(i=>i.categoria === e.categoria);
                                    new_fil.forEach(i=>{
                                        let new_tr = document.createElement('tr');
                                        let new_td = document.createElement('td');
                                        let new_td1 = document.createElement('td');
                                        let new_td2 = document.createElement('td');
                                        let new_td3 = document.createElement('td');
                                        let new_td4 = document.createElement('td');
                                        new_td.textContent = i.codigo;
                                        new_td1.textContent = i.serial;
                                        new_td2.textContent = i.saldo;
                                        new_td3.textContent = i.cantidad;
                                        new_td4.textContent = i.saldo - i.cantidad;
                                        new_tr.appendChild(new_td);
                                        new_tr.appendChild(new_td1);
                                        new_tr.appendChild(new_td2);
                                        new_tr.appendChild(new_td3);
                                        new_tr.appendChild(new_td4);
                                        tbody.appendChild(new_tr);
                                        cont ++;
                                    });
                                    let div_super = document.createElement('div');
                                    div_super.classList.add('acordeon_cate', 'accordion-item');
                                    let span = document.createElement('span');
                                    span.classList.add('badge', 'badge-primary', 'border');
                                    span.textContent = cont;
                                    let div_sp = document.createElement('div');
                                    div_sp.classList.add('me-2');
                                    div_sp.appendChild(span);
                                    let span1 = document.createElement('span');
                                    span1.classList.add('ml-n2');
                                    span1.textContent = e.categoria;
                                    let div_c = document.createElement('div');
                                    div_c.classList.add('d-flex', 'justify-content-between', 'w-100'); 
                                    div_c.appendChild(span1);
                                    div_c.appendChild(div_sp);
                                    let button = document.createElement('button');
                                    button.classList.add('accordion-button', 'fw-bold', 'text-primary', 'collapsed', 'bg-gray-100'); 
                                    button.setAttribute('aria-expanded', 'true');
                                    button.setAttribute('type', 'button');
                                    button.setAttribute('data-toggle', 'collapse');
                                    button.setAttribute('data-target', `#div_cate${e.categoria}`);
                                    button.setAttribute('aria-controls', `div_cate${e.categoria}`);
                                    button.appendChild(div_c);
                                    let h2 = document.createElement('h2');
                                    h2.classList.add('accordion-header');
                                    h2.setAttribute('id', `h2_${e.categoria}`);
                                    h2.appendChild(button);
                                    div_super.appendChild(h2);
                                    let th = document.createElement('th');
                                    let th1 = document.createElement('th');
                                    let th2 = document.createElement('th');
                                    let th3 = document.createElement('th');
                                    let th4 = document.createElement('th');
                                    th.textContent = 'CÓDIGO';
                                    th1.textContent = 'SERIAL';
                                    th2.textContent = 'SALDO';
                                    th3.textContent = 'CANTIDAD';
                                    th4.textContent = 'DIFERENCIA';
                                    let tr = document.createElement('tr');
                                    tr.appendChild(th);
                                    tr.appendChild(th1);
                                    tr.appendChild(th2);
                                    tr.appendChild(th3);
                                    tr.appendChild(th4);
                                    let thead = document.createElement('thead');
                                    thead.classList.add('bg-primary', 'text-white');
                                    thead.appendChild(tr);
                                    let table = document.createElement('table');
                                    table.classList.add('table_kardex', 'table', 'table-bordered', 'table-sm', 'text-center', 'table-hover', 'table-striped');
                                    table.setAttribute('id', `tabla_cate_${e.categoria}`);
                                    table.appendChild(thead);
                                    table.appendChild(tbody);
                                    let div_t = document.createElement('div');
                                    div_t.classList.add('accordion-body', 'overflow-scroll', 'info-tables')
                                    div_t.setAttribute('style', 'max-height: 40vh;');
                                    div_t.appendChild(table);
                                    let div_div = document.createElement('div');
                                    div_div.classList.add('accordion-collapse', 'collapse');
                                    div_div.setAttribute('id',`div_cate${e.categoria}`);
                                    div_div.setAttribute('data-parent','#acor_cate_tab_inven');
                                    div_div.setAttribute('aria-labelledby',`h2_${e.categoria}`);
                                    div_div.appendChild(div_t);
                                    div_super.appendChild(div_div);
                                    document.getElementById('acor_cate_tab_inven').appendChild(div_super);
                                };
                                return acc;
                            },[]); 
                        }
                        else{
                            document.getElementById('sel_fecha').innerHTML = '';
                            let opc = document.createElement('option');
                            opc.setAttribute('value','');
                            opc.setAttribute('disabled','true');
                            opc.textContent = 'Seleccionar...';
                            document.getElementById('sel_fecha').appendChild(opc);
                            document.getElementById('acor_cate_tab_inven').innerHTML = '<h2 class="text-primary">No Registra Informacion</h2>';
                        }
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                }); 
            };
        };
        function getFecha(ced){
            if(validarSesion(location.pathname+location.search)) {
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/controlador_inventario_fisico.php",
                    method:'POST',
                    data: {
                        getFecha: ced 
                    },
                    dataType : "json",
                    success: function(data){
                        document.getElementById('sel_fecha').innerHTML = '';
                        let data_fecha = data.reduce((acc,e)=>{
                            acc.push(e.fecha.slice(0,10));
                            return acc;
                        },[]);
                        data_fecha = [...new Set(data_fecha)].reverse();
                        data_fecha.forEach(e=>{
                            let option = document.createElement('option');
                            option.setAttribute('value',`${e}`);
                            option.textContent = e;
                            document.getElementById('sel_fecha').appendChild(option);
                        });
                        let data_guar ={fecha:'',cedula:workData.cedula};
                        getInventFisico(data_guar);
                        (()=>document.getElementById('sel_fecha').addEventListener('change', valueFecha))();
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                }); 
            };
        };
        function saveInventario(){
            if(validarSesion(location.pathname+location.search)) {
                pro_ajax = $.ajax({
                    url: "./controladores/control_inventario_controlador/controlador/controlador_inventario_fisico.php",
                    method:'POST',
                    data: {
                        save_inventario: JSON.stringify(workData)
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
                        };
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        error_ajax( jqXHR, textStatus, errorThrown );
                    }
                }); 
            }
        };
        
        // ready
        document.addEventListener('DOMContentLoaded',()=>{
            document.querySelectorAll('.btn_tab').forEach(e=>e.addEventListener('click',activeTab));
            document.querySelectorAll('.list').forEach(e=> e.addEventListener('click',activeList));
            document.querySelector('.menuTog').addEventListener('click',e=>{
                document.querySelector('.navigation').classList.toggle('active');
            });
            document.querySelectorAll('.span_fecha').forEach(e=>e.textContent = workData.hoy);
            document.querySelector('#inven_tec table').addEventListener('click', e=>{;
                workData.cedula = document.getElementById('cc_inven_tec_search').value;
                document.querySelectorAll('.cc_tec_asoc').forEach(e=>e.value = workData.cedula);
                workData.area = jsonAllPer.find(e=>e.cedula == workData.cedula).area;
                document.querySelectorAll('.area_tec_asoc').forEach(e=>e.value = workData.area);
                getInventario();
            });
            document.querySelector('#inven_tec_saldo table').addEventListener('click', e=>{
                workData.cedula = document.getElementById('cc_inven_tec_saldo_search').value;
                getFecha(workData.cedula)
                document.querySelectorAll('.cc_tec_asoc').forEach(e=>e.value = workData.cedula);
                workData.area = jsonAllPer.find(e=>e.cedula == workData.cedula).area;
                document.querySelectorAll('.area_tec_asoc').forEach(e=>e.value = workData.area);
            });
            document.querySelector('#inven_tec_search')?.addEventListener('input', e=>{
                if(!e.target.value){
                    document.querySelectorAll('.cc_tec_asoc').forEach(e=>e.value = '');
                    document.querySelectorAll('.area_tec_asoc').forEach(e=>e.value = '');
                    document.getElementById('area_tec_asoc').value = '';
                    workData.cedula = '';
                }
            });
            document.querySelector('#inven_tec_saldo_search')?.addEventListener('input', e=>{
                if(!e.target.value){
                    document.querySelectorAll('.cc_tec_asoc').forEach(e=>e.value = '');
                    document.querySelectorAll('.area_tec_asoc').forEach(e=>e.value = '');
                    workData.cedula = '';
                }
            });
            // document.getElementById('btn_add_cod').addEventListener('click',()=>{
            //     if(!!document.getElementById('btn_add_cod').classList.contains('active')){
            //         !!document.getElementById('inven_tec-msg') && document.getElementById('inven_tec-msg').remove();
            //         [...document.querySelectorAll('.div_add_cod')].forEach(e=>e.classList.add('d-none'));
            //     }
            //     else{
            //         [...document.querySelectorAll('.div_add_cod')].forEach(e=>e.classList.remove('d-none'));
            //     }
            //     document.getElementById('btn_add_cod').classList.toggle('active');
            // });
        });
    </script>
</body>
</html>
<?php } ?>