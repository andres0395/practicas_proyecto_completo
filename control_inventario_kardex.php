<div class="info-tables mx-2 overflow-scroll py-3 row start-0" id="content_tables" style="width: -webkit-fill-available; height: 85%;">
</div>
<script>
    let data_kar = {};
    data_kar.buscar_bodega = {
        table :  
            `<thead class="bg-primary text-center text-white">
                <tr>
                    <th style="font-size: 12px" class="text-center tdh-m">Serial</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Codigo</th>
                    <th style="font-size: 12px" class="text-center tdh-xl">Descripcion</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Estado</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Observacion</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Cantidad</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Cedula</th>
                    <th style="font-size: 12px" class="text-center tdh-xxl">Gestor</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Fecha de Gestion</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="text-center">
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-xl">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-xxl">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                </tr>
            </tfoot>
            <tbody></tbody>`
        ,
        columns : [
            { "data": "serial"},
            { "data": 'codigo'},
            { "data": "descri"},
            { "data": "movimiento"},
            { "data": "observaciones"},
            { "data": 'cantidad'},
            { "data": 'cedula'},
            { "data": 'edit'},
            { "data": 'fecha_edit'},
        ],
        columnDefs : [{
            targets: 7, 
            render: function(data, type, full, meta){
                let nom = jsonAllPer.find(e=> e.cedula == data);
                return nom ? `${nom.nombre} ${nom.apellido}` : data;
            },
        }],
    }
    data_kar.saldo_bodega = {
        table :  
            `<thead class="bg-primary text-center text-white">
                <tr>
                    <th style="font-size: 12px" class="text-center tdh-m">Codigo</th>
                    <th style="font-size: 12px" class="text-center tdh-xl">Descripcion</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Entrada</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Entrega</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Devolucion</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Baja-Cobro</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Moviles</th>
                    <th style="font-size: 12px" class="text-center tdh-m">Stock</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="text-center">
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-xl">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                    <th scope="col" class="tdh-m">Filtrar...</th>
                </tr>
            </tfoot>
            <tbody></tbody>`
        ,
        columns : [
            { "data": 'codigo'},
            { "data": "descri"},
            { "data": "entrada"},
            { "data": "entrega"},
            { "data": "devolucion"},
            { "data": 'baja'},
            { "data": 'moviles'},
            { "data": 'stock'},
        ],
        columnDefs : [{
            targets: 1, 
            render: function(data, type, full, meta){
                let descri = data_cod_cate.find(e=> e.codigo == data)?.descripcion ?? '';
                return descri;
            }
        }],
    }
    async function karConsulta(act){
        let nam_ta = act+'_bodega';
        document.getElementById('content_tables').innerHTML = '';
        let new_div = document.createElement('div');
        new_div.id = `tb_kard_con_${nam_ta}`;
        new_div.classList.add('col-12',  'kard_tb');
        document.getElementById('content_tables').appendChild(new_div);
        let new_tb = document.createElement('table');
        new_tb.id = `tb_kard_${nam_ta}`;
        new_tb.classList.add('table', 'table-striped', 'table-bordered', 'table-sm', 'table-hover', 'tb_materiales_mov', 'table_kardex', 'datatable_div', 'text-center');
        new_div.appendChild(new_tb);
        document.getElementById(`tb_kard_${nam_ta}`).innerHTML = data_kar[nam_ta].table;
        $(`#tb_kard_${nam_ta}`).addClass('datatable_div');
        $(`#tb_kard_${nam_ta} tfoot th`).each(function() {
            let title = $(this).text();
            $(this).html('<input class="w-75" type="search" placeholder=" ' + title + '" />');
        });
        let formJqObj = $('#form_kardex');
        let formDataObj = {};
        let pru = {};
        let val_inp_bus = [];
        (function(){
            document.querySelector('#form_kardex').querySelectorAll("input[type='number'],  input[type='text'],  input[type='hidden'], input[type='search'], input[type='radio']:checked, select, input[type='checkbox']:checked, select").forEach(e=>{
                formDataObj[e.getAttribute('name')] = e.value;
            })
            console.log(formDataObj);
            workData.action = act;
            setTimeout(()=> getSaldos_kardex(formDataObj, nam_ta, act),100);
        })();
    }
    function dataTablesBusSal(nam_ta){
        $(`#tb_kard_${nam_ta}`).DataTable({
            destroy:true,
            dom: 'B<"float-right"f>t<"d-flex align-items-end justify-content-between"ip><"clearfix">',
            buttons: dataTable_all_btn,
            displayLength: 20,
            lengthMenu: [
                [20,50,100,-1],
                [20,50,100,'TODO']
            ],
            data: bodega_kardex,
            columnDefs: data_kar[nam_ta].columnDefs,
            columns: data_kar[nam_ta].columns,
            responsive: true,
            language: dataTable_espanol,
            initComplete: function() {
                this.api().columns().every(function() {
                    let that = this;
                    $(`#tb_kard_${nam_ta}`, this.footer()).on('input', function() {
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
    };
    function activeList(){
        workData.movimiento = this.value; 
        document.querySelectorAll('.rad_mat_mov').forEach(e=>{
            e.parentNode.classList.remove('active');
            this.parentNode.classList.add('active','mov_sel');
        });
    };
    // ready
    document.addEventListener('DOMContentLoaded', () => {
        [...document.querySelectorAll('.rad_mat_mov')].forEach(e=>e.addEventListener('click', activeList));
        document.getElementById('sal_buscar').addEventListener('click', ()=> karConsulta('saldo'));
        document.getElementById('codigo_buscar').addEventListener('click', ()=> karConsulta('buscar'));
    });
</script>