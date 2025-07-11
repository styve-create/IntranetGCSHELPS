<!doctype html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IntranetGlobal</title>
<!-- jQuery (requerido por DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome for icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">   
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
<style>

body {
  font-family: 'Roboto', sans-serif;
  margin: 0;
  padding: 0;
  background-color: #f8f9fa;
  overflow-x: hidden;
}

/* SIDEBAR */
.sidebar {
  background-color: rgb(0, 169, 198);
  color: white;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  overflow-y: auto;
  width: 250px;
  transition: width 0.3s ease;
  z-index: 1020;
}

.sidebar.colapsada {
  width: 90px;
}

.sidebar .nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 15px;
  white-space: nowrap;
  border: none;
  color: white;
  text-decoration: none;
}

.sidebar.colapsada + .navbar-custom {
  margin-left: 0; /* no margin! */
}
.sidebar + .navbar-custom {
  margin-left: 0; /* no margin! */
}


.sidebar .icono-wrapper {
  background-color: white;
  color: rgb(0, 169, 198);
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.sidebar .texto-btn {
  display: inline;
}

.sidebar.colapsada .texto-btn {
  display: none;
}

/* Hover para colapsada */
.sidebar.colapsada:hover {
  width: 250px;
}

.sidebar.colapsada:hover .texto-btn {
  display: inline;
}

.sidebar.colapsada:hover ~ .main-wrapper {
  margin-left: 250px;
}

.sidebar.colapsada:hover ~ .main-wrapper .navbar-custom {
  left: 250px;
}

/* NAVBAR */
.navbar-custom {
  position: fixed;
  top: 0;
  left: 250px;
  right: 0;
  background-color: #f8f9fa;
  border-bottom: 1px solid #ccc;
  height: 56px;
  display: flex;
  align-items: center;
  padding: 0 1rem;
  z-index: 1010;
  transition: left 0.3s ease;
}

.sidebar.colapsada ~ .main-wrapper .navbar-custom {
  left: 90px;
}

.toggle-btn {
  border: none;
  background: none;
  font-size: 1.2rem;
  margin-right: 10px;
}

/* CONTENIDO */
.main-wrapper {
  position: relative;
  margin-left: 250px;
  transition: margin-left 0.3s ease;
  padding-top: 56px;
  width: calc(100% - 250px);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.sidebar.colapsada ~ .main-wrapper {
  margin-left: 90px;
  width: calc(100% - 90px);
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 1rem;
  width: 100%;
}

/* Scroll visible */
.sidebar::-webkit-scrollbar {
  width: 6px;
}

.sidebar::-webkit-scrollbar-thumb {
  background-color: rgba(255,255,255,0.4);
  border-radius: 3px;
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    position: fixed;
    height: auto;
    z-index: 1050;
  }

  .sidebar.colapsada {
    width: 90px;
  }

  .navbar-custom {
    left: 90px;
  }

  .main-wrapper {
    margin-left: 90px;
  }
}

</style>
  <body style="font-family: 'Roboto', sans-serif;">
