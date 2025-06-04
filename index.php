<?php
include('app/controllers/config.php');
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IntranetGlobal</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Bootstrap 4 -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Librería SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
 <style>
.form-container {
    background: #00A9C6;
    font-family: 'Roboto', sans-serif;
    font-size: 0;
    padding: 0 15px;
    border: 1px solid #00A9C6;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.2);
}
.form-container .form-icon {
    color: #fff;
    font-size: 13px;
    text-align: center;
    text-shadow: 0 0 20px rgba(0,0,0,0.2);
    width: 50%;
    padding: 120px 40px;
    vertical-align: top;
    display: inline-block;
}
.form-container .form-icon i {
    font-size: 124px;
    margin: 0 0 15px;
    display: block;
}
.form-container .form-icon .signup a {
    color: #fff;
    text-transform: capitalize;
    transition: all 0.3s ease;
}
.form-container .form-icon .signup a:hover {
    text-decoration: underline;
}
.form-container .form-horizontal {
    background: rgba(255,255,255,0.99);
    width: 50%;
    padding: 60px 30px;
    margin: -20px 0;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.2);
    display: inline-block;
}
.form-container .title {
    color: #454545;
    font-size: 23px;
    font-weight: 900;
    text-align: center;
    text-transform: capitalize;
    letter-spacing: 0.5px;
    margin: 0 0 30px 0;
}
.form-horizontal .form-group {
    background-color: rgba(255,255,255,0.15);
    margin: 0 0 15px;
    border: 1px solid #b5b5b5;
    border-radius: 20px;
}
.form-horizontal .input-icon {
    color: #b5b5b5;
    font-size: 15px;
    text-align: center;
    line-height: 38px;
    height: 35px;
    width: 40px;
    vertical-align: top;
    display: inline-block;
}
.form-horizontal .form-control {
    color: #b5b5b5;
    background-color: transparent;
    font-size: 14px;
    letter-spacing: 1px;
    width: calc(100% - 55px);
    height: 33px;
    padding: 2px 10px 0 0;
    box-shadow: none;
    border: none;
    border-radius: 0;
    display: inline-block;
    transition: all 0.3s;
}
.form-horizontal .form-control:focus {
    box-shadow: none;
    border: none;
}
.form-horizontal .form-control::placeholder {
    color: #b5b5b5;
    font-size: 13px;
    text-transform: capitalize;
}
.form-horizontal .btn {
    color: rgba(255,255,255,0.8);
   background: #FF8C6B;
    font-size: 15px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
    width: 100%;
    margin: 0 0 10px 0;
    border: none;
    border-radius: 20px;
    transition: all 0.3s ease;
}
.form-horizontal .btn:hover,
.form-horizontal .btn:focus {
    color: #fff;
    background-color: #e36d54;
    box-shadow: 0 0 5px rgba(0,0,0,0.5);
}
.form-horizontal .forgot-pass {
    font-size: 12px;
    text-align: center;
    display: block;
}
.form-horizontal .forgot-pass a {
    color: #999;
    transition: all 0.3s ease;
}
.form-horizontal .forgot-pass a:hover {
    color: #777;
    text-decoration: underline;
}
@media only screen and (max-width:576px){
    .form-container {
        padding-bottom: 15px;
    }
    .form-container .form-icon {
        width: 100%;
        padding: 20px 0;
    }
    .form-container .form-horizontal {
        width: 100%;
        margin: 0;
    }
}
</style>
</head>
<body style="background-color: #FFF; font-family: 'Roboto', sans-serif;">
<div class="container d-flex justify-content-center align-items-center flex-column" style="height: 100vh;">
   
    <div class="form-container d-flex justify-content-between flex-wrap" >
        <div class="form-icon">
            <img src="<?php echo $URL;?>/imagen/GCS3.png" alt="Logo" style="max-width: 220px;">
            <br><br>
            <span class="signup"><a href="#">¿No tienes una cuenta?<br> Contacta con soporte</a></span>
        </div>

       <form class="form-horizontal" id="loginForm" method="post" action="<?php echo $URL;?>/app/controllers/login/ingreso.php">
            <h3 class="title"><span style="color: #00A9C6;">Intranet</span><span style="color: #F5CE59;">Global</span></h3>
            <p class="text-center mb-4">Ingrese sus datos</p>

            <div class="form-group">
                <span class="input-icon"><i class="fa fa-envelope"></i></span>
                <input class="form-control" type="email" name="email" placeholder="Correo electrónico" required>
            </div>
                 <input type="hidden" name="latitud" id="latitud">
                <input type="hidden" name="longitud" id="longitud">
            <div class="form-group">
                <span class="input-icon"><i class="fa fa-lock"></i></span>
                <input class="form-control" type="password" name="password_user" placeholder="Contraseña" required>
            </div>

           <button class="btn signin" type="button"  id="loginButton">Ingresar</button>
           
            
        </form>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<?php if (isset($_GET['error'])): ?>
<script>
    let error = "<?php echo $_GET['error']; ?>";

    switch(error) {
        case 'login':
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Contraseña incorrecta. Por favor, intente nuevamente.'
            });
            break;
        case 'nouser':
            Swal.fire({
                icon: 'warning',
                title: 'Usuario no encontrado',
                text: 'El correo ingresado no pertenece a ningún usuario.'
            });
            break;
        case 'incompleto':
            Swal.fire({
                icon: 'info',
                title: 'Campos incompletos',
                text: 'Por favor, complete todos los campos.'
            });
            break;
    }
</script>

<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  console.log("Script cargado correctamente");

  const boton = document.querySelector('.btn.signin');
  if (boton) {
    console.log("Botón detectado, añadiendo listener");
    boton.addEventListener("click", handleLogin);
  } else {
    console.log("Botón NO encontrado");
  }
});
document.getElementById('loginButton').addEventListener('click', handleLogin);
function handleLogin(event) {
  event.preventDefault();
  console.log("handleLogin ejecutado");

  if (!navigator.geolocation) {
    console.log("Geolocalización no soportada");
    Swal.fire({
      icon: 'error',
      title: 'Geolocalización no soportada',
      text: 'Tu navegador no permite acceder a la ubicación.'
    });
    return;
  }

  console.log("Solicitando geolocalización...");

  navigator.geolocation.getCurrentPosition(
    function (position) {
      console.log("Ubicación obtenida");
      console.log('Latitud:', position.coords.latitude);
      console.log('Longitud:', position.coords.longitude);

      document.getElementById('latitud').value = position.coords.latitude;
      document.getElementById('longitud').value = position.coords.longitude;

      console.log("Enviando formulario...");
      document.getElementById('loginForm').submit();
    },
    function (error) {
      console.log("Error obteniendo ubicación:", error);

      let mensaje = 'No se pudo obtener tu ubicación.';
      if (error.code === 1) mensaje = 'Debes permitir el acceso a tu ubicación para continuar.';

      Swal.fire({
        icon: 'error',
        title: 'Error de ubicación',
        text: mensaje
      });
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    }
  );
}
</script>

</body>
</html>