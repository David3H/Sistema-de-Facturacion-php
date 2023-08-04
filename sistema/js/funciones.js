$(document).ready(function(){

    //--------------------- SELECCIONAR FOTO PRODUCTO ---------------------
    $("#foto").on("change",function(){
    	var uploadFoto = document.getElementById("foto").value;
        var foto       = document.getElementById("foto").files;
        var nav = window.URL || window.webkitURL;
        var contactAlert = document.getElementById('form_alert');
        
            if(uploadFoto !='')
            {
                var type = foto[0].type;
                var name = foto[0].name;
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png')
                {
                    contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';                        
                    $("#img").remove();
                    $(".delPhoto").addClass('notBlock');
                    $('#foto').val('');
                    return false;
                }else{  
                        contactAlert.innerHTML='';
                        $("#img").remove();
                        $(".delPhoto").removeClass('notBlock');
                        var objeto_url = nav.createObjectURL(this.files[0]);
                        $(".prevPhoto").append("<img id='img' src="+objeto_url+">");
                        $(".upimg label").remove();
                        
                    }
              }else{
              	alert("No selecciono foto");
                $("#img").remove();
              }              
    });

    $('.delPhoto').click(function(){
    	$('#foto').val('');
    	$(".delPhoto").addClass('notBlock');
    	$("#img").remove();

        if($("#foto_actual") && $("#foto_remove")){
            $("#foto_remove").val('img_producto.png');
        }
    });

    //Modal Formato Add Product
$('.add_product').click(function(e){
    /* Act on the evento */
    e.preventDefault();
    var producto = $(this).attr('product');
    var action = 'infoProducto';

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: {action:action,producto:producto},

        success: function(response){

            if(response != 'error'){
                var info = JSON.parse(response);
                
                $('.bodyModal').html(
                '<form action="" method="post" name="form_add_product" id="form_add_product" onsubmit="event.preventDefault(); sendDateProduct();">'+
                '<h1><i class="fa-solid fa-cubes fa-2x"></i><br> Agregar Producto</h1>'+
                '<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+
                '<input type="number" name="cantidad" id="txtCantidad" placeholder="Cantida producto" required><br>'+
                '<input type="text" name="precio" id="txtPrecio" placeholder="Precio producto" required>'+
                '<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required>'+
                '<input type="hidden" name="action" value="addProduct" required>'+
                '<br>'+
                '<div class="msg_save alertAddProduct alert"></div>'+
                '<br>'+
                '<button type="submit" class="btn_editar">Agregar</button>'+
                '<button href="#" class="btn_eliminar closeModal" onclick="coloseModal();">Cerrar</button>'+
                '</form>');
            }
        },

        error: function(error){
            console.log(error);
        }
    });

    $('.modal').fadeIn();
});


    //Listar productos para la venta
    $('.lista_productos').click(function(e){
        e.preventDefault();
        var action = 'infoListProducto';
    
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: {action:action},
    
            success: function(response){
    
                if(response != 'error'){

                    var info = JSON.parse(response);
                    $('.bodyModal').html(
                        '<form action="" onsubmit="event.preventDefault();">'+
                        '<div class="table-responsive">'+
                        '<form action="#" method="get" class="form_search">'+
                        '<input type="text" class="light-table-filter" data-table="table_id" name="busqueda" id="busqueda" placeholder="Buscar">'+
                        '</form>'+
                        '<br>'+
                        '<table class="table table_id" id="personas tblDatos" align="center">'+
                        '<tr>'+
                        '<th> Seleccionar </th>'+
                        '<th> Nombre Producto </th>'+
                        '<th> Existencia </th>'+
                        '<th> Precio </th>'+
                        '</tr>'+
                        ''+info.detalles+''+
                        '</table>'+
                        '</div>'+
                        '</form>');
                    
                }
            },
    
            error: function(error){
                console.log(error);
            }
        });
    
        $('.modal').fadeIn();
    });

     //Listar productos para la venta
     $('.listar_clientes').click(function(e){
        e.preventDefault();
        var action = 'infoListCliente';
    
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: {action:action},
    
            success: function(response){
    
                if(response != 'error'){

                    var info = JSON.parse(response);
                    $('.bodyModal').html(
                        '<form action="" onsubmit="event.preventDefault();">'+
                        '<div class="table-responsive">'+
                        '<form action="#" method="get" class="form_search">'+
                        '<input type="text" class="light-table-filter" data-table="table_id" name="busqueda" id="busqueda" placeholder="Buscar">'+
                        '</form>'+
                        '<br>'+
                        '<table class="table table-striped table_id" id="personas tblDatos" align="center">'+
                        '<tr>'+
                        '<th> Seleccionar </th>'+
                        '<th> Cédula </th>'+
                        '<th> Nombre Cliente </th>'+
                        '<th> Dirección </th>'+
                        '</tr>'+
                        ''+info.detallesCli+''+
                        '</table>'+
                        '</div>'+
                        '</form>');
                    
                }
            },
    
            error: function(error){
                console.log(error);
            }
        });
    
        $('.modal').fadeIn();
    });

    //Modal Formato Delete Product
    $('.del_product').click(function(e){
        /* Act on the evento */
        e.preventDefault();
        var producto = $(this).attr('product');
        var action = 'infoProducto';
    
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            async: true,
            data: {action:action,producto:producto},
    
            success: function(response){
    
                if(response != 'error'){
                    var info = JSON.parse(response);
                    //$('#producto_id').val(info.codproducto);
                    //$('.nameProducto').html(info.descripcion);
                    
                    $('.bodyModal').html(
                    '<form action="" method="post" name="form_del_product" id="form_del_product" onsubmit="event.preventDefault(); delProduct();">'+
                    '<h1><i class="fa-solid fa-cubes fa-2x"></i><br> Eliminar Producto</h1>'+
                    '<p>¿Esta seguro de eliminar el siguiente registro?</p>'+
                    '<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+
                    '<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required>'+
                    '<input type="hidden" name="action" value="delProduct" required>'+
                    '<div class="msg_save alertAddProduct alert"></div>'+
                    '<a href="#" class="btn_cancelar" onclick="coloseModal();">Cerrar</a>'+
				    '<button type="submit" class="btn_ok">Eliminar</button>'+
                    '</form>');
                }
            },
    
            error: function(error){
                console.log(error);
            }
        });
    
        $('.modal').fadeIn();
    });

    $('#search_proveedor').change(function(e){
        e.preventDefault();

        var sistema = getUrl();
        location.href = sistema+'buscar_producto.php?proveedor='+$(this).val();
    });

    //Activar campos para registrar cliente
    $('.btn_new_cliente').click(function(e){
        e.preventDefault();
        $('#nom_cliente').removeAttr('disabled');
        $('#tel_cliente').removeAttr('disabled');
        $('#dir_cliente').removeAttr('disabled');

        $('#div_registro_cliente').slideDown();
    });
    
    //Buscar cliente
    $('#nit_cliente').keyup(function(e){
        e.preventDefault();

        var cl = $(this).val();
        var action = 'searchCliente';

        $.ajax({
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: {action:action,cliente:cl},

            success: function(response)
            {
                //console.log(response);
                if(response == 0){
                    $('#idcliente').val('');
                    $('#nom_cliente').val('');
                    $('#tel_cliente').val('');
                    $('#dir_cliente').val('');
                    //Mostrar boton agregar
                    $('.btn_new_cliente').slideDown();
                }else{
                    var data = $.parseJSON(response);
                    $('#idcliente').val(data.idcliente);
                    $('#nom_cliente').val(data.nombre);
                    $('#tel_cliente').val(data.telefono);
                    $('#dir_cliente').val(data.direccion);
                    //Ocultar boton agregar
                    $('.btn_new_cliente').slideUp();

                    //Bloqueo campos
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');

                    //Ocultar boton guardar
                    $('#div_registro_cliente').slideUp();

                }
            },
            error: function(error){

            }
        });
    });


    //Crear cliente - Ventas
    $('#form_new_cliente_venta').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: $('#form_new_cliente_venta').serialize(),

            success: function(response)
            {
                if(response != 'error'){
                //Agregar id a input hidden
                $('#idcliente').val(response);

                 //Bloqueo campos
                 $('#nom_cliente').attr('disabled','disabled');
                 $('#tel_cliente').attr('disabled','disabled');
                 $('#dir_cliente').attr('disabled','disabled');

                 //Ocultar boton agregar
                 $('.btn_new_cliente').slideUp();
                 
                 //Ocultar boton guardar
                 $('#div_registro_cliente').slideUp();
                }

            },
            error: function(error){

            }
        });
    });

    //Buscar productos
    $('#txt_cod_producto').keyup(function(e){
        e.preventDefault();

        var producto = $(this).val();
        var action = 'infoProducto';

        if(producto != ''){
            $.ajax({
                url: 'ajax.php',
                type: "POST",
                async: true,
                data: {action:action,producto:producto},
    
                success: function(response)
                {
                   if(response != 'error'){
                    var info = JSON.parse(response);
                    console.log(info);
                    $('#txt_descripcion').html(info.descripcion);
                    $('#txt_existencia').html(info.existencia);
                    $('#txt_cant_producto').val('1');
                    $('#txt_precio').html(info.precio);
                    $('#txt_precio_total').html(info.precio);

                    //Activar Cantidad
                    $('#txt_cant_producto').removeAttr('disabled');

                    //Mostrar bptón agregar
                    $('#add_product_venta').slideDown();
                   }else{
                    $('#txt_descripcion').html('-');
                    $('#txt_existencia').html('-');
                    $('#txt_cant_producto').val('0');
                    $('#txt_precio').html('$0.00');
                    $('#txt_precio_total').html('$0.00');

                    //Activar Cantidad
                    $('#txt_cant_producto').attr('disabled');

                    //Mostrar bptón agregar
                    $('#add_product_venta').slideUp();
                   }
    
                },
                error: function(error){
    
                }
            });
        }
        
    });

    //Validar cantidad antes de agregar producto
    $('#txt_cant_producto').keyup(function(e){
        e.preventDefault();

        var precio_total = ($(this).val() * $('#txt_precio').html()).toFixed(2);
        var existencia = parseInt($('#txt_existencia').html());
        $('#txt_precio_total').html(precio_total);

        //Ocultar el botón agregar si la vantidad es menor que 1
        if( ($(this).val() < 1 || isNaN($(this).val())) || ($(this).val() > existencia) ){
            $('#add_product_venta').slideUp();
        }else{
            $('#add_product_venta').slideDown();
        }
        
    });


    function detalleNuevo(correlativo){

    }
    //Agregar producto al detalle
    $('#add_product_venta').click(function(e){
        e.preventDefault();

        if($('#txt_cant_producto').val() > 0){

        var codproducto = $('#txt_cod_producto').val();
        var cantidad = $('#txt_cant_producto').val();
        var action = 'addProductoDetalle';

        $.ajax({
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: {action:action,producto:codproducto,cantidad:cantidad},

            success: function(response)
            {
                if(response != 'error'){
                    console.log(response);
                    var info = JSON.parse(response);
                    
                    $('#detalle_venta').html(info.detalle);
                    $('#detalle_totales').html(info.totales);

                    $('#txt_cod_producto').val('');
                    $('#txt_descripcion').html('-');
                    $('#txt_existencia').html('-');
                    $('#txt_cant_producto').val('0');
                    $('#txt_precio').html('0.00');
                    $('#txt_precio_total').html('0.00');

                    //Desactivar Cantidad
                    $('#txt_cant_producto').attr('disabled', 'disabled');

                    //Ocultatar botón agregar
                    $('#add_product_venta').slideUp();
                }else{
                    console.log('No se encontraron datos');
                    alert("El producto ya se ecuentra seleccionado");
                }
                viewProcesar();
            },
            error: function(error){
                
            }
        });

        }
        
    });    

    //Anular Venta
    $('#btn_anular_venta').click(function(e){
        e.preventDefault();

        var row = $('#detalle_venta tr').length;
        if(row > 0){
            var action = 'anularVenta';

            $.ajax({
                url: 'ajax.php',
                type: "POST",
                async: true,
                data: {action:action},
    
                success: function(response)
                {
                    //console.log(response);
                    if(response != 'error'){
                        location.reload();
                    }
    
                },
                error: function(error){
                }
            });            
        }

    });

     //Facturar venta
    $('#btn_facturar_venta').click(function(e){
        e.preventDefault();
        var resultado = window.confirm('¿ Confirmal la venta ?');
        if (resultado === true) {
        var row = $('#detalle_venta tr').length;
        if(row > 0){
            var action = 'procesarVenta';
            var codcliente = $('#idcliente').val();

            $.ajax({
                url: 'ajax.php',
                type: "POST",
                async: true,
                data: {action:action,codcliente:codcliente},
    
                success: function(response)
                {
                    
                    if(response != 'error'){
   
                        var info = JSON.parse(response);
                        console.log(response);
                        generarPDF(info.codcliente,info.nofactura);
                        location.reload();
                    }else{
                        console.log('No datos');
                    }
    
                },
                error: function(error){
                }
            });            
        }
    } else { 
       
    }

    });
    
    //Modal anular factura
$('.anular_factura').click(function(e){
    /* Act on the evento */
    e.preventDefault();
    var nofactura = $(this).attr('fac');
    var action = 'infoFactura';

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        async: true,
        data: {action:action,nofactura:nofactura},

        success: function(response){

            if(response != 'error'){
                var info = JSON.parse(response);
                
                $('.bodyModal').html(
                    '<form action="" method="post" name="form_anular_factura" id="form_anular_factura" onsubmit="event.preventDefault(); anularFactura();">'+
                    '<h1><i class="fa-regular fa-file-lines fa-2x"></i><br> Anular Factura</h1>'+
                    '<p>¿Esta seguro de anular la siguente factura?</p>'+

                    '<p><strong>No. '+info.nofactura+'</strong></p>'+
                    '<p><strong>Monto. $'+info.totalfactura+'</strong></p>'+
                    '<p><strong>Fecha. '+info.fecha+'</strong></p>'+
                    '<input type="hidden" name="action" value="anularFactura">'+
                    '<input type="hidden" name="no_factura" id="no_factura" value="'+info.nofactura+'" required></input>'+

                    '<div class="msg_save alertAddProduct alert"></div>'+
                    '<button type="submit" class="btn_ok">Anular</button>'+
                    '<a href="#" class="btn_cancelar" onclick="coloseModal();">Cerrar</a>'+
                    '</form>');
            }
        },

        error: function(error){
            console.log(error);
        }
    });

    $('.modal').fadeIn();
});

    //Ver factura
    $('.view_factura').click(function(e) {
        e.preventDefault();
        var codCliente = $(this).attr('cl');
        var noFactura = $(this).attr('f');
        generarPDF(codCliente,noFactura);
    });

}); //End Ready

//Anular Factura
function anularFactura(){
    var noFactura = $('#no_factura').val();
    var action = 'anularFactura';

    $.ajax({
        url: 'ajax.php',
        type: "POST",
        async: true,
        data: {action:action,noFactura:noFactura},

        success: function(response)
        {
            if(response == 'error'){

                $('.alertAddProduct').html('<p style="color:red;">Error al anular la factura</p>');

            }else{
                $('#row_'+noFactura+'.estado').html('<span class="anulada">Anulado</span>');
                $('#form_anular_factura .btn_ok').remove();
                $('#row_'+noFactura+'.div_factura').html('<button type="button" class="btn_anular inactive"><i class="fas fa-ban"></i></button>');
                $('.alertAddProduct').html('<p>Factura anulada.</p>');
            }
            location.reload();
        },
        error: function(error){

        }
    });
}

function generarPDF(cliente,factura){
    var ancho = 1000;
    var alto = 800;

    //Calcular posicion x,y para centrar la ventana
    var x = parseInt((window.screen.width/2) - (ancho / 2));
    var y = parseInt((window.screen.height/2) - (alto / 2));

    $url = 'factura/generaFactura.php?cl='+cliente+'&f='+factura;
    window.open($url,"Factura","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si, location=no, resizable=si, menubar=no");
}

//Eliminar temporal detalle venta
function del_product_detalle(correlativo){

    var action = 'del_product_detalle';
    var id_detalle = correlativo;

    $.ajax({
        url: 'ajax.php',
        type: "POST",
        async: true,
        data: {action:action,id_detalle:id_detalle},

        success: function(response)
        {
            if(response != 'error'){

                var info = JSON.parse(response);
                //console.log(response);
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);

                $('#txt_cod_producto').val('');
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0');
                $('#txt_precio').html('0.00');
                $('#txt_precio_total').html('0.00');

                //Desactivar Cantidad
                $('#txt_cant_producto').attr('disabled', 'disabled');

                //Ocultatar botón agregar
                $('#add_product_venta').slideUp();
            }else{
                $('#detalle_venta').html('');
                $('#detalle_totales').html('');
            }
            viewProcesar();
        },
        error: function(error){

        }
    });
}

//Nueva funcion para eliminar
function product_detalle_del(correlativo){

    var action = 'del_product_detalle';
    var id_detalle = correlativo;

    $.ajax({
        url: 'ajax.php',
        type: "POST",
        async: true,
        data: {action:action,id_detalle:id_detalle},

        success: function(response)
        {
            if(response != 'error'){

                var info = JSON.parse(response);
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);

            }else{
                $('#detalle_venta').html('');
                $('#detalle_totales').html('');
            }
            viewProcesar();
        },
        error: function(error){

        }
    });
}

//Mostrar / Ocultar boton
function viewProcesar(){
    if($('#detalle_venta tr').length > 0){
        $('#btn_facturar_venta').show();
    }else{
        $('#btn_facturar_venta').hide();
    }
}

function serchForDetalle(id){
    var action = 'serchForDetalle';
    var user = id;

    $.ajax({
        url: 'ajax.php',
        type: "POST",
        async: true,
        data: {action:action,user:user},

        success: function(response)
        {
            //console.log(response);
            if(response != 'error'){

                var info = JSON.parse(response);
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);

            }else{
                console.log('No se encontraron datos');
            }
            viewProcesar();
        },
        error: function(error){

        }
    });

}

function getUrl(){
    var loc = window.location;
    var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
    return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
}

function sendDateProduct(){

    $('.alertAddProduct').html('');

    $.ajax({
        url: 'ajax.php', 
        type: 'POST',
        async: true,
        data: $('#form_add_product').serialize(),

        success: function(response){
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color: red;">Error al agregar el producto.</p>');

            }else{
                
                var resp = JSON.parse(response);

                $('.row'+resp.producto_id+'.celPrecio').html(resp.nuevo_precio);
                $('.row'+resp.producto_id+'.celExistencia').html(resp.nueva_existencia);
                $('#txtCantidad').val('');
                $('#txtPrecio').val('');
                $('.alertAddProduct').html('<p>Producto agregado correctamente.</p>');
                location.reload();
            }
        },

        error: function(error){
            console.log(error);
        }
    });


}

//Eliminar producto
function delProduct(){

    var pr = $('#producto_id').val();

    $('.alertAddProduct').html('');

    $.ajax({
        url: 'ajax.php', 
        type: 'POST',
        async: true,
        data: $('#form_del_product').serialize(),

        success: function(response){
            console.log(response);
           
            if(response == 'error'){
                $('.alertAddProduct').html('<p style="color: red;">Error al eliminar el producto.</p>');

            }else{

                $('.row'+pr).remove();
                $('#form_del_product .btn_ok').remove();
                $('.alertAddProduct').html('<p>Producto eliminado correctamente.</p>');
            }

        },

        error: function(error){
            console.log(error);
        }
    });


}
/*--------------------------------------------------------------------------------*/

//Funcion para busqueda dinamica
(function(document) { 
    'buscador';

    var LightTableFilter = (function(Arr) {

      var _input;

      function _onInputEvent(e) {
        _input = e.target;
        var tables = document.getElementsByClassName(_input.getAttribute('data-table'));
        Arr.forEach.call(tables, function(table) {
          Arr.forEach.call(table.tBodies, function(tbody) {
            Arr.forEach.call(tbody.rows, _filter);
          });
        });
      }

      function _filter(row) {
        var text = row.textContent.toLowerCase(), val = _input.value.toLowerCase();
        row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
      }

      return {
        init: function() {
          var inputs = document.getElementsByClassName('light-table-filter');
          Arr.forEach.call(inputs, function(input) {
            input.oninput = _onInputEvent;
          });
        }
      };
    })(Array.prototype);

    document.addEventListener('readystatechange', function() {
      if (document.readyState === 'complete') {
        LightTableFilter.init();
        
      }
    });
    

  })(document);



function coloseModal(){
    $('.alertAddProduct').html('');
    $('#txtCantidad').val('');
    $('#txtPrecio').val('');
    $('.modal').fadeOut();
}

//Mapear al module Producto
function resivirId(producto){
        var action = 'infoProducto';

        if(producto != ''){
            $.ajax({
                url: 'ajax.php',
                type: "POST",
                async: true,
                data: {action:action,producto:producto},
    
                success: function(response)
                {
                   if(response != 'error'){
                    var info = JSON.parse(response);
                    console.log(info);
                    $('#txt_cod_producto').val(producto);
                    $('#txt_descripcion').html(info.descripcion);
                    $('#txt_existencia').html(info.existencia);
                    $('#txt_cant_producto').val('1');
                    $('#txt_precio').html(info.precio);
                    $('#txt_precio_total').html(info.precio);

                    //Activar Cantidad
                    $('#txt_cant_producto').removeAttr('disabled');

                    //Mostrar bptón agregar
                    $('#add_product_venta').slideDown();
                   }else{
                    $('#txt_descripcion').html('-');
                    $('#txt_existencia').html('-');
                    $('#txt_cant_producto').val('0');
                    $('#txt_precio').html('$0.00');
                    $('#txt_precio_total').html('$0.00');

                    //Activar Cantidad
                    $('#txt_cant_producto').attr('disabled');

                    //Mostrar bptón agregar
                    $('#add_product_venta').slideUp();
                   }
    
                },
                error: function(error){
    
                }
            });
        }

}

//Mapear al module Cliente
function resivirCliente(cl){
        var action = 'searchCliente';

        $.ajax({
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: {action:action,cliente:cl},

            success: function(response)
            {
                //console.log(response);
                if(response == 0){
                    $('#idcliente').val('');
                    $('#nom_cliente').val('');
                    $('#tel_cliente').val('');
                    $('#dir_cliente').val('');
                    //Mostrar boton agregar
                    $('.btn_new_cliente').slideDown();
                }else{
                    var data = $.parseJSON(response);
                    $('#idcliente').val(data.idcliente);
                    $('#nit_cliente').val(cl);
                    $('#nom_cliente').val(data.nombre);
                    $('#tel_cliente').val(data.telefono);
                    $('#dir_cliente').val(data.direccion);
                    //Ocultar boton agregar
                    $('.btn_new_cliente').slideUp();

                    //Bloqueo campos
                    $('#nit_cliente').attr('disabled','disabled');
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');

                    //Ocultar boton guardar
                    $('#div_registro_cliente').slideUp();

                }
            },
            error: function(error){

            }
        });

}

function enviarDatosEdit(correlativo, producto, cantidad){
    var action = 'infoProducto';

    if(producto != ''){
        $.ajax({
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: {action:action,producto:producto},

            success: function(response)
            {
               if(response != 'error'){
                var info = JSON.parse(response);
                $('#txt_cod_producto').val(producto);
                console.log(info);
                $('#txt_descripcion').html(info.descripcion);
                $('#txt_existencia').html(info.existencia);
                $('#txt_cant_producto').val(cantidad);
                $('#txt_precio').html(info.precio);
                $('#txt_precio_total').html(info.precio);

                //Activar Cantidad
                $('#txt_cant_producto').removeAttr('disabled');

                //Mostrar bptón agregar
                $('#add_product_venta').slideDown();
               }

               product_detalle_del(correlativo);
            },
            error: function(error){

            }
        });
    }
}

