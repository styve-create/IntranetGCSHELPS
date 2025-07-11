<?php 
include_once(__DIR__ . '/../../../../app/controllers/config.php');
include_once(__DIR__ . '/../../../analisisRecursos.php');
?>
<link rel="stylesheet" href="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/librerias/vendor/npm-asset/bootstrap/dist/css/bootstrap.min.css">

<style>
  #graficaBarrasContainer {
    max-height: 300px;
    overflow-x: auto;
    overflow-y: hidden;
  }

  .contenedor-tabla-grafico {
    min-height: 250px;
  }


</style>

<div id="contenido-principal">
    <div class="container-fluid p-4">
        <h4 class="mb-4">facturacion</h4>

        <!-- Filtros: Sección para seleccionar los filtros de cliente, actividad y fechas -->
        <div class="row g-2 mb-4">
            <!-- Filtro de cliente -->
            <div class="col-md-2">
                <select id="filtroCliente" class="form-select form-select-sm">
                    <option value="">Cargando clientes...</option>
                </select>
            </div>

            <!-- Filtro de actividad -->
            <div class="col-md-2">
                <select id="filtroActividad" class="form-select form-select-sm">
                    <option value="all">Todas las actividades</option>
                </select>
            </div>

            <!-- Filtro de fecha de inicio -->
            <div class="col-md-2">
                <input type="date" id="fechaInicio" class="form-control form-control-sm" />
            </div>

            <!-- Filtro de fecha de fin -->
            <div class="col-md-2">
                <input type="date" id="fechaFin" class="form-control form-control-sm" />
            </div>

            <!-- Botón para aplicar los filtros -->
            <div class="col-md-2">
                <button id="btnFiltrar" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel"></i> Aplicar Filtro
                </button>
            </div>
        </div>
        
       <!-- 1. Botones de acción -->
<!-- 1. Botones de acción -->
<div class="d-flex justify-content-end mb-4">
  <button id="btnDescargarFactura" class="btn btn-outline-secondary me-2">
    <i class="bi bi-download"></i> Descargar Factura
  </button>
  <button id="btnAbrirEnvio" class="btn btn-primary">
    <i class="bi bi-envelope"></i> Enviar Factura
  </button>
</div>

<!-- 2. Contenedor de la factura -->
<div id="invoiceContainer" class="border rounded p-4">
  <!-- 2.a Header: número + fechas -->
  <div class="d-flex justify-content-between mb-4">
    <div>
      <h5 class="mb-1">N° Factura: <span id="invoiceNumber">XXXXX</span></h5>
      <small>Fecha inicio: <span id="invoiceFechaInicio">dd/mm/aaaa</span></small><br>
      <small>Fecha fin: <span id="invoiceFechaFin">dd/mm/aaaa</span></small>
    </div>
    
  </div>
  

<!-- 3. Bill From / Bill To en la misma fila -->
<div class="row mb-4">
  <!-- Bill From: valor por defecto pero modificable -->
  <div class="col-6">
    <h6 class="mb-1">Bill From:</h6>
    <input
      type="text"
      id="billFromInput"
      class="form-control form-control-sm"
      value="Global Connection SAS"
    >
  </div>

  <!-- Bill To: se rellena al filtrar, pero el usuario puede cambiarlo -->
  <div class="col-6 text-end">
    <h6 class="mb-1">Bill To:</h6>
    <input
      type="text"
      id="billToInput"
      class="form-control form-control-sm "
      placeholder="Nombre del Cliente"
      value=""
    >
  </div>
</div>
<div>
    <h6 class="mt-3 mb-1">Subject (optional)</h6>
    <textarea class="form-control form-control-mb" placeholder="¿Para qué es esta factura?"></textarea>
    <br>
</div>


  <!-- 4. Tabla de ítems -->
  <div class="table-responsive">
    <table class="table table-bordered align-middle text-center">
      <thead class="table-light">
        <tr>
          <th>Actividad</th>
          <th>Descripción</th>
          <th class="text-end">Cant.</th>
          <th class="text-end">Precio Unit.</th>
          <th class="text-end">Subtotal</th>
        </tr>
      </thead>
      <tbody id="tablaResumen">

      </tbody>
    </table>
  </div>

<!-- 5. Resumen de totales -->
<div class="d-flex justify-content-end mt-4">
  <div class="w-50">
    <!-- Descuento: ahora un input editable -->
    <div class="d-flex justify-content-between align-items-center mb-2">
  <label for="invoiceDiscountInput" class="mb-0">Descuento (%)</label>
  <div class="input-group input-group-sm" style="max-width: 120px;">
    <input
      type="number"
      id="invoiceDiscountInput"
      class="form-control text-end"
      value="0"
      step="0.1"
      min="0"
      max="100"
    >
    <span class="input-group-text">%</span>
  </div>
</div>
<div class="d-flex justify-content-between mb-2">
  <span>Descuento aplicado</span>
  <span id="invoiceDiscount">0.00 USD (0%)</span>
</div>
    <hr>

    <div class="d-flex justify-content-between fw-bold">
      <span>Subtotal</span>
      <span id="invoiceSubtotal">0.00 USD</span>
    </div>
    <div class="d-flex justify-content-between fs-4">
      <span>Total</span>
      <span id="invoiceTotal">0.00 USD</span>
    </div>
    <div class="d-flex justify-content-between">
      <span>Total Amount Due</span>
      <span id="invoiceAmountDue">0.00 USD</span>
    </div>
  </div>
</div>

  <!-- 6. Notas adicionales -->
  <div >
    <h6>Notas adicionales:</h6>
    <textarea  class="form-control form-control-mb" id="invoiceNotas" placeholder="Aquí puedes agregar cualquier comentario extra."></textarea>
  </div>
</div>

<!-- 7. Acciones Guardar  -->
<div class="d-flex justify-content-end mt-4">
  <button id="btnGuardar" class="btn btn-success">
    <i class="bi bi-save"></i> Guardar Factura
  </button>
</div>


<!-- Modal Enviar Factura -->
<div class="modal fade" id="modalEnviar" tabindex="-1" aria-labelledby="modalEnviarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalEnviarLabel">Send Invoice</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Body -->
      <div class="modal-body">
        <form id="formEnviarEmail">
          <!-- From -->
          <div class="mb-3">
            <label for="emailFrom" class="form-label">From *</label>
            <input type="email" id="emailFrom" class="form-control" 
                 placeholder="Enter client email"  required>
          </div>
          <!-- To -->
          <div class="mb-3">
            <label for="emailTo" class="form-label">To *</label>
            <input type="email" id="emailTo" class="form-control" placeholder="Enter client email" required>
          </div>
          <!-- Subject -->
          <div class="mb-3">
            <label for="emailSubject" class="form-label">Subject *</label>
            <input type="text" id="emailSubject" class="form-control" 
                   value="Invoice1 from hola" required>
          </div>
          <!-- Body -->
          <div class="mb-3">
            <label for="emailBody" class="form-label">Body *</label>
            <textarea id="emailBody" class="form-control" rows="6" required>
            hola has sent you an invoice.
            
            InvoiceID: Invoice1
            Issue date: 26/05/2025
            Client: global1
            Amount: 0.00 USD
            Due: 05/06/2025
            
            The detailed invoice is attached as a PDF.
            
            Thank you!
            
            </textarea>
            <iframe id="previewPDF" style="width:100%;height:400px;border:1px solid #ccc;"></iframe>
          </div>
          <!-- Checkboxes -->
          <div class="form-check mb-1">
            <input class="form-check-input" type="checkbox" id="attachInvoice" checked>
            <label class="form-check-label" for="attachInvoice">Attach invoice as PDF</label>
          </div>
     
        </form>
      </div>
      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        <button id="btnSendInvoice" type="button" class="btn btn-primary">SEND</button>
      </div>
    </div>
  </div>
</div>
   
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="<?php echo $URL; ?>/librerias/vendor/npm-asset/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="<?= $URL ?>/librerias/vendor/npm-asset/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    console.log("Script cargado correctamente");

    const filtroClienteElement = document.getElementById('filtroCliente');
    const filtroActividadElement = document.getElementById('filtroActividad');
    const btnFiltrarElement = document.getElementById('btnFiltrar');
    const tablaResumenElement = document.getElementById('tablaResumen');
   let ultimaData = []; // guardaremos aqui los registros
   const btnDescargar = document.getElementById('btnDescargarFactura');
   const btnAbrir = document.getElementById('btnAbrirEnvio');
   const btnGuardarFactura = document.getElementById('btnGuardar');
   btnDescargar.disabled = true;
   btnAbrir.disabled     = true;
   btnGuardarFactura.disabled     = true;

    // Función para formatear hora
    function formatearHora(horaTexto) {
        if (!horaTexto) return '';
        const date = new Date(`1970-01-01T${horaTexto}`);
        const horas = String(date.getHours()).padStart(2, '0');
        const minutos = String(date.getMinutes()).padStart(2, '0');
        return `${horas}:${minutos}`;
    }
window.cargarClientes = function() {
    
        const selectedValue = filtroClienteElement.value;

        fetch(`/intranet/sistema/pages/panelAdministrativoClockify/facturacion/get_clientes.php?t=${new Date().getTime()}`)
            .then(res => res.json())
            .then(data => {
                console.log("data", data);
                filtroClienteElement.innerHTML = '<option value="">Seleccione un cliente</option>';
                if (data && Array.isArray(data) && data.length) {
                    data.forEach(cliente => {
                        const option = document.createElement('option');
                        option.value = cliente.id_cliente;
                        option.textContent = cliente.nombre_cliente;
                        filtroClienteElement.appendChild(option);
                    });
                } else {
                    filtroClienteElement.innerHTML = '<option value="">No hay clientes disponibles</option>';
                }
                filtroClienteElement.value = selectedValue; // restaurar selección
            })
            .catch(err => {
                filtroClienteElement.innerHTML = '<option value="">Error al cargar clientes</option>';
                console.error('Error al obtener clientes:', err);
            });
  
        // Evento para aplicar los filtros
    btnFiltrarElement.addEventListener('click', () => {
        const clienteId = filtroClienteElement.value;
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        btnDescargar.disabled = true;
        btnAbrir.disabled     = true;
        btnGuardarFactura.disabled     = true;
        const fmt = ymd => {
    if (!ymd) return '';
    const [y, m, d] = ymd.split('-');
    return `${d}/${m}/${y}`;
  };
  document.getElementById('invoiceFechaInicio') .textContent = fmt(fechaInicio);
  document.getElementById('invoiceFechaFin')   .textContent = fmt(fechaFin);
  const sel    = filtroClienteElement;
  const nombre = sel.options[sel.selectedIndex]?.text || '';
  document.getElementById('billToInput').value = nombre;
   const invoiceNum = 'INV-' + Date.now(); 
    document.getElementById('invoiceNumber') .textContent = invoiceNum;

        const selectedOptions = Array.from(filtroActividadElement.selectedOptions).map(opt => opt.value);
        const actividadesParam = selectedOptions.includes('all') || selectedOptions.length === 0
            ? ''
            : selectedOptions.join(',');

        fetch(`/intranet/sistema/pages/panelAdministrativoClockify/facturacion/api_reporte.php?cliente=${clienteId}&actividades=${encodeURIComponent(actividadesParam)}&inicio=${fechaInicio}&fin=${fechaFin}&t=${new Date().getTime()}`)
            .then(res => res.json())
            .then(data => {
                console.log("data", data);
                renderTabla(data.registros);
                registrosData = (data.registros);
                ultimaData = data.registros;           // guardo los registros
                renderInvoice(data.registros);         // renderizo tabla + totales
                if (data.registros && data.registros.length > 0) {
                  btnDescargar.disabled = false;
                  btnAbrir.disabled     = false;
                  btnGuardarFactura.disabled     = false;
              }
               
            });
    });
     // Evento para cargar actividades basado en cliente
    filtroClienteElement.addEventListener('change', function () {
        const clienteId = this.value;
        const actividadSelect = filtroActividadElement;
        actividadSelect.innerHTML = '<option value="all">Todas las actividades</option>';

        fetch(`/intranet/sistema/pages/panelAdministrativoClockify/facturacion/get_actividades.php?cliente_id=${clienteId}&t=${new Date().getTime()}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(actividad => {
                    const option = document.createElement('option');
                    option.value = actividad.nombre_actividad;
                    option.textContent = actividad.nombre_actividad;
                    actividadSelect.appendChild(option);
                });
            })
            .catch(err => {
                actividadSelect.innerHTML = '<option value="all">Error al cargar actividades</option>';
                console.error('Error al obtener actividades:', err);
            });
    });
};

filtroClienteElement.addEventListener('click', window.cargarClientes);    

    // Función para renderizar la tabla con los registros
    function renderTabla(registros) {
        const tbody = tablaResumenElement;
        tbody.innerHTML = ''; 

        registros.forEach((r) => {
            const tr = document.createElement('tr');
            // Asignar el data-id a cada fila (esto es importante para identificar la fila de manera única)
            tr.setAttribute('data-id', r.id);

            tr.innerHTML = `
                <td>${r.actividad}</td>
                <td>${r.descripcion}</td>
                <td><strong>${r.duracion}</strong></td>
                <td><strong>${r.precio_unitario}</strong></td>
                <td>${r.cobro_calculado}</td>
                
            `;
            tbody.appendChild(tr);
        });
    }
    const descuentoInput = document.getElementById('invoiceDiscountInput');

  
  // funcion: refresca tabla de ítems y totales
  function renderInvoice(registros) {
    const tbody = document.getElementById('tablaResumen');
    tbody.innerHTML = '';
    registros.forEach(r => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${r.actividad}</td>
        <td>${r.descripcion}</td>
        <td class="text-end">${r.duracion}</td>
        <td class="text-end">${r.precio_unitario.toFixed(2)} USD</td>
        <td class="text-end">${r.cobro_calculado.toFixed(2)} USD</td>
      `;
      tbody.appendChild(tr);
    });
    
    const subtotal = registros
    .reduce((sum, r) => sum + parseFloat(r.cobro_calculado), 0);
      const descuentoPct = parseFloat(descuentoInput.value) || 0;
      const descuentoMonto = subtotal * (descuentoPct / 100);
      const total = subtotal - descuentoMonto;
  document.getElementById('invoiceSubtotal') .textContent = subtotal.toFixed(2) + ' USD';
  document.getElementById('invoiceDiscount') .textContent = `- ((${descuentoMonto.toFixed(2)}) USD (${descuentoPct.toFixed(1)}%))`;
  document.getElementById('invoiceTotal')     .textContent = total.toFixed(2) + ' USD';
  document.getElementById('invoiceAmountDue') .textContent = total.toFixed(2) + ' USD';
  
  }
  // re-renderizar totales cuando cambie el descuento
  descuentoInput.addEventListener('input', () => {
    renderInvoice(ultimaData);
  });


function collectInvoiceData() {
  return {
    invoiceNumber: document.getElementById('invoiceNumber').textContent,
    fechaInicio:   document.getElementById('invoiceFechaInicio').textContent,
    fechaFin:      document.getElementById('invoiceFechaFin').textContent,
    billFrom:      document.getElementById('billFromInput').value,
    billTo:        document.getElementById('billToInput').value,
    discountPct:   parseFloat(document.getElementById('invoiceDiscountInput').value)||0,
    notes:         document.getElementById('invoiceNotas').value,
    items: ultimaData.map(r => ({
      actividad:      r.actividad,
      descripcion:    r.descripcion,
      duracion:       r.duracion,
      precioUnitario: r.precio_unitario,
      subtotal:       r.cobro_calculado
    }))
  };
}

// Descargar factura
document.getElementById('btnDescargarFactura').addEventListener('click', () => {
  const data = collectInvoiceData();
  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/facturacion/generate_pdf.php?t=${new Date().getTime()}`, {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(data)
  })
  .then(res => res.blob())
  .then(blob => {
    const url = URL.createObjectURL(blob);
    const a   = document.createElement('a');
    a.href    = url;
    a.download= `${data.invoiceNumber}.pdf`;
    a.click();
    URL.revokeObjectURL(url);
  });
});

// Vista previa y abrir modal de envío
document.getElementById('btnAbrirEnvio').addEventListener('click', () => {
  const data = collectInvoiceData();
 
  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/facturacion/generate_pdf.php?t=${new Date().getTime()}`, {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(data)
  })
  .then(res => res.blob())
      .then(blob => {
      const url       = URL.createObjectURL(blob);
      const recipient = filtroClienteElement
                          .options[filtroClienteElement.selectedIndex]
                          .dataset.email || '';
    
      document.getElementById('previewPDF').src = url;
      const invoiceNum = document.getElementById('invoiceNumber').textContent;
      document.getElementById('emailFrom').value = '';  // o tu usuario real
      document.getElementById('emailTo')  .value = recipient;
      document.getElementById('emailSubject').value = `Factura ${invoiceNum} de Global Connection`;
      document.getElementById('emailBody').value = `
        Hola,
        Te adjunto la factura ${invoiceNum} correspondiente al periodo 
        ${document.getElementById('invoiceFechaInicio').textContent} – 
        ${document.getElementById('invoiceFechaFin').textContent}.
        Saludos.`;
      new bootstrap.Modal(document.getElementById('modalEnviar')).show();
    });
});


const btnSend = document.getElementById('btnSendInvoice');
btnSend.addEventListener('click', () => {
  
  btnSend.disabled = true;

  const emailData = {
    to:      document.getElementById('emailTo').value,
    subject: document.getElementById('emailSubject').value,
    body:    document.getElementById('emailBody').value
  };
  const data = collectInvoiceData();

 
  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/facturacion/generate_pdf.php?t=${Date.now()}`, {
    method: 'POST',
    headers: { 'Content-Type':'application/json' },
    body: JSON.stringify(data)
  })
  .then(res => res.blob())
  .then(blob => {
    
    const fd = new FormData();
    fd.append('invoiceNumber', data.invoiceNumber);
    fd.append('email',         emailData.to);
    fd.append('subject',       emailData.subject);
    fd.append('body',          emailData.body);
    fd.append('pdf',           blob, `${data.invoiceNumber}.pdf`);

    return fetch(`/intranet/sistema/pages/panelAdministrativoClockify/facturacion/send_invoice.php?t=${Date.now()}`, {
      method: 'POST',
      body: fd
    });
  })
  .then(r => {
    if (!r.ok) return r.text().then(txt => Promise.reject(txt));
    return r.json();
  })
  .then(resp => {
    if (resp.success) {
      Swal.fire('Enviado', 'Factura enviada correctamente', 'success');
    } else {
      Swal.fire('Error', resp.message, 'error');
    }
  })
  .catch(err => {
    console.error('Error en envío:', err);
    Swal.fire('Error', 'Servidor respondió: ' + err, 'error');
  })
  .finally(() => {
    
    btnSend.disabled = false;
  });
});

// --- Guardar Factura en BD ---
document.getElementById('btnGuardar').addEventListener('click', () => {
  const payload = collectInvoiceData();

  fetch(`/intranet/sistema/pages/panelAdministrativoClockify/facturacion/save_invoice.php?t=${new Date().getTime()}`, {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(r => r.json())
  .then(resp => {
    if (resp.success) {
      Swal.fire('Guardado', 'La factura se guardó en la base de datos.', 'success');
    } else {
      Swal.fire('Error', resp.message||'No se pudo guardar.', 'error');
    }
  });
});



})();
</script>

