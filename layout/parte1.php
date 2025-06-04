<!doctype html>
<html lang="en" dir="ltr">
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
      }

      .sidebar {
  background-color: rgb(0, 169, 198) !important;
  color: white;
  padding: 15px;
  position: sticky;
   top: 0;
  height: 100vh;
  overflow-y: auto;
}

      .sidebar a,
      .sidebar .btn {
        color: white !important;
        text-decoration: none;
      }

      .sidebar .btn {
        background-color: transparent;
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: background-color 0.3s ease;
      }

      .sidebar .btn:hover,
      .hover-effect:hover {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 0.25rem;
      }

      .main-content {
        padding: 20px;
      }

      .dropdown-menu {
        background-color: #00A9C6;
        border: none;
      }

      .dropdown-item {
        color: white;
      }

      .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.2);
      }

      .hover-effect:hover {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 0.25rem;
      }

      .collapse:not(.show):not(.ready) {
        display: none !important;
        visibility: hidden;
      }

      @media (max-width: 768px) {
        .sidebar {
          width: 100%;
        }

        .main-content {
          margin-left: 0;
        }
      }
       #jerarquia-tree { background: #f9f9f9; padding: 20px; border-radius: 10px; max-height: 600px; overflow-y: auto; }
    </style>

  </head>
  <body style="font-family: 'Roboto', sans-serif;">
