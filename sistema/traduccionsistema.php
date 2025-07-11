<?php
session_name('mi_sesion_personalizada');
session_start();

header('Content-Type: application/javascript');

$idioma = $_SESSION['idioma'] ?? 'ES';

$log_index1 = __DIR__ . "/log_index1.txt";
function log_index1($mensaje) {
    global $log_index1;
    error_log(date('Y-m-d H:i:s') . " - $mensaje\n", 3, $log_index1);
}

log_index1(">>> Inicio del script traduccionsistema.php");
log_index1("Contenido de la sesión: " . var_export($_SESSION, true));

?>
window.currentLang = '<?php echo addslashes($idioma) ?>';
// Solo definir si aún no existe
if (!window.translations) {
    window.translations = {
    
        "Inicio": "Home",
        "inicio": "Home",
        "Informacion": "Information",
        "Informacion personal": "Personal Information",
        "Información personal": "Personal Information",
        "Panel Administrativo Anuncios": "Announcements Admin Panel",
        "Horario": "Schedule",
        "horario": "Schedule",
        "Horarios": "Schedules",
        "clockify": "Clockify",
        "Panel de Horarios": "Schedule Panel",
        "Registros Horarios Trabajadores": "Workers Time Records",
        "Registros Horarios Administrativos": "Admin Time Records",
        "Panel Administrativo clockify": "Clockify Admin Panel",
        "Clientes": "Clients",
        "Reportes": "Reports",
        "Reportes OPS": "OPS Reports",
        "facturas": "Invoices",
        "Gestión de Usuarios": "User Management",
        "Listado de Usuarios": "User List",
        "Creación de Usuario": "Create User",
        "Roles": "Roles",
        "Usuarios Activos": "Active Users",
        "Registros y Formularios": "Records and Forms",
        "Registros Huellas": "Fingerprint Records",
        "Formulario de Ausencias": "Absence Form",
        "Gestión de Trabajadores": "Worker Management",
        "Listado de Trabajadores": "Worker List",
        "Creación de Trabajador": "Create Worker",
        "jerarquias": "Hierarchies",
        "Cerrar Sesión": "Logout",
        "Remember me": "Remember me",
        "Forgot password?": "Forgot password?",
        "Ausencias": "Absences",
        "Formulario de ausencias": "Absence Form",
        "Más información": "More information",
        "Actividades": "Activities",
        "Formulario de Actividades": "Activities Form",
        "📭 No hay anuncios disponibles": "📭 No announcements available",
        "Vuelve pronto para ver nuevos anuncios corporativos.": "Check back soon for new corporate announcements.",
        "📢 Anuncio Corporativo": "📢 Corporate Announcement",
        "Publicado:": "Published:",
        "Datos Personales": "Personal Data",
        "Mi perfil": "My Profile",
        "Documentos": "Documents",
        "Documento:": "Document:",
        "Teléfono:": "Phone:",
        "Email:": "Email:",
        "Grupo sanguíneo:": "Blood group:",
        "Dirección:": "Address:",
        "Contacto de emergencia:": "Emergency contact:",
        "Tel. emergencia:": "Emergency phone:",
        "EPS:": "Health provider:",
        "Documentos disponibles": "Available documents",
        "Certificación ACTIVO GCS": "ACTIVE GCS Certificate",
        "Certificación RETIRO GCS": "RETIRED GCS Certificate",
        "Descargar certificados": "Download certificates",
        "Agregar Anuncio": "Add Announcement",
        "Imagen": "Image",
        "Título": "Title",
        "Descripción": "Description",
        "Estado": "Status",
        "Acciones": "Actions",
        "Agregar / Editar Anuncio": "Add / Edit Announcement",
        "Imagen actual:": "Current image:",
        "Fecha Inicio": "Start Date",
        "Fecha Fin": "End Date",
        "Imagen (opcional)": "Image (optional)",
        "Cancelar": "Cancel",
        "Guardar": "Save",
        "Control de Asistencia": "Attendance Control",
        "Inicio Turno": "Shift Start",
        "Break": "Break",
        "Fin": "End",
        "Fin Turno": "Shift End",
        "Inicio Tiempo Extra": "Overtime Start",
        "Fin Tiempo Extra": "Overtime End",
        "Minimizar": "Minimize",
        "Duplicar": "Duplicate",
        "Eliminar": "Delete",
        "Resumen Semanal": "Weekly Summary",

        // Nuevos términos específicos
        "Registros de Horarios -Para Jefes": "Time Records - For Managers",
        "Filtrar": "Filter",
        "Restablecer": "Reset",
        "Campaña": "Campaign",
        "Trabajador": "Worker",
        "Fecha": "Date",
        "Break 1 Inicio": "Break 1 Start",
        "Break 1 Fin": "Break 1 End",
        "Break 2 Inicio": "Break 2 Start",
        "Break 2 Fin": "Break 2 End",
        "Break 3 Inicio": "Break 3 Start",
        "Break 3 Fin": "Break 3 End",
        "Fecha Registro": "Record Date",
        "Si el calendario no está actualizado, los registros podrían verse afectados.": "If the calendar is not updated, the records may be affected.",
        "Se tendrán en cuenta los siguientes festivos registrados:": "The following registered holidays will be considered:",
        "No hay festivos registrados en este rango.": "No holidays registered in this range.",
        "Agregar Festivo": "Add Holiday",
        "Registrar Días Festivos": "Register Holidays",
        "Registrar Festivos": "Register Holidays",
        "Exportar Excel Avanzado": "Export Advanced Excel",
        "Registros de Horarios -Para la Administrativos": "Time Records - For Administrators",
         "Clientes": "Clients",
  "Mostrar todos": "Show all",
  "Mostrar activos": "Show active",
  "Mostrar inactivos": "Show inactive",
  "Search by name": "Search by name",
  "Add new Client": "Add new Client",
  "Agregar": "Add",
  "Nombre": "Name",
  "Direccion": "Address",
  "Moneda": "Currency",
  "Ver": "View",
  "Editar": "Edit",
  "Más opciones": "More options",
  "Editar Cliente": "Edit Client",
  "Estatus": "Status",
  "Activo": "Active",
  "Inactivo": "Inactive",
  "Horas Trabajadas": "Worked Hours",
  "Precio Horas": "Hourly Rate",
  "Cancelar": "Cancel",
  "Guardar": "Save",
  "Clientes Actividades": "Client Activities",
  "Nueva Actividad": "New Activity",
  "Actividad": "Activity",
  "Descripción": "Description",
  "Horas trabajadas": "Worked Hours",
  "Precio Horas": "Hourly Rate",
  "Acción": "Action",
  "Crear Nueva Actividad": "Create New Activity",
  "Nombre de Actividad": "Activity Name",
  "Trabajadores Asignados": "Assigned Workers",
  "Buscar trabajador por nombre...": "Search worker by name...",
   "Reporte de Tiempos": "Time Report",
    "Aplicar Filtro": "Apply Filter",
    "Resumen de Horas y Cobro": "Hours and Billing Summary",
    "PDF": "PDF",
    "Total de horas:": "Total hours",
    "Total a cobrar:": "Total to charge",
    "Total cobrar General:": "Total general charge",
    "Total cobrar Actividad:": "Total activity charge",
    "Usuario": "User",
    "Duración": "Duration",
    "Ver Detalle": "View Detail",
    "Ver detalle": "View detail",
    "Detalle de Actividades de": "Activities Detail of",
    "Agregar actividad": "Add Activity",
    "Editar Actividad": "Edit Activity",
    "Crear nueva actividad": "Create New Activity",
    "Guardar cambios": "Save changes",
    "Guardar actividad": "Save activity",
    "Sí": "Yes",
    "No": "No",
    "Cargando clientes...": "Loading clients...",
    "Todas las actividades": "All activities",
    "Seleccione un cliente": "Select a client",
    "¿Factura Actividad?": "Activity Invoice?",
    "¿Factura General?": "General Invoice?",
    "Hora Inicio": "Start time",
    "Hora Fin": "End Time",
    "Cliente": "Client",
    "Seleccione una actividad": "Select an activity",
    "Seleccione un cliente primero": "Select a client first",
            "Usuario": "User",
        "Tiempo": "Time",
        "Horas Extra": "Overtime",
        "Horas Extras": "Overtime",
        "Asignar Actividad": "Assign Activity",
        "Ver Detalle": "View Detail",
        "Todas las actividades": "All activities",
        "Todos los clientes": "All clients",
        "Salir": "Logout",
        "+ Nuevo Registro": "+ New Record",
        "Detalle de Horarios": "Schedule Detail",
        "Aplicar Filtro": "Apply Filter",
        "Cargando clientes...": "Loading clients...",
        "Reporte de Tiempos OPS": "OPS Time Report",

        // También estaban en tu lista estas ya presentes pero las dejo para mayor claridad
        "Break 1": "Break 1",
        "Break 2": "Break 2",
        "Break 3": "Break 3",
        "Fin": "End",
        "Inicio": "Start",
        "Guardar": "Save",
           // 🗓️ Días de la semana
        "Lunes": "Monday",
        "Martes": "Tuesday",
        "Miércoles": "Wednesday",
        "Jueves": "Thursday",
        "Viernes": "Friday",
        "Sábado": "Saturday",
        "Domingo": "Sunday",

        // 🗓️ Meses
        "enero": "January",
        "febrero": "February",
        "marzo": "March",
        "abril": "April",
        "mayo": "May",
        "junio": "June",
        "julio": "July",
        "agosto": "August",
        "septiembre": "September",
        "octubre": "October",
        "noviembre": "November",
        "diciembre": "December",
        "de": "of"
    };
}

// Solo definir si aún no existe
if (!window.traducirTextos) {
  window.traducirTextos = function (contenedor = document) {
    const lang = window.currentLang || 'ES';
    if (lang !== 'EN') return; // Solo traducir si el idioma es inglés

    contenedor.querySelectorAll('.traducible').forEach(el => {
      const originalText = el.dataset.originalText || el.textContent.trim();
      if (!el.dataset.originalText) {
        el.dataset.originalText = originalText;
      }

      let translated = window.translations[originalText];

      if (!translated) {
        // Si no hay traducción exacta, traduce palabra por palabra
        translated = originalText
          .split(/(\s+|,|\.)/) // mantener separadores
          .map(part => window.translations[part] || part)
          .join('');
      }

      el.textContent = translated;
    });
  };
}

// ejecutar inmediatamente si la sesión está en EN
if (window.currentLang === 'EN') {
    window.traducirTextos(document);
}

