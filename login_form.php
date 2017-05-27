<?php
$data = $_GET['code'];

// $url_ldap_login = plugins_url('test/login_test.php', __FILE__);
// define( 'WP_USE_THEMES', false );
// require( './wp-load.php' );
require_once("../../../wp-load.php");

// Constantes
$url_ldap_login = plugins_url('ldap/ldap_login.php', __FILE__);
define('LDAP_LOGIN', $url_ldap_login);

$url_mifirma_login = plugins_url('mifirma/mifirma_login.php', __FILE__);
define('MIFIRMA_LOGIN', $url_mifirma_login);

$url_mifirma_checker_db = 'mifirma/checker_db.php';
define('MIFIRMA_CHECKER_DB', $url_mifirma_checker_db);
//
 ?>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo plugins_url('statics/css/bootstrap.min.css', __FILE__); ?>">
<script src="<?php echo plugins_url('statics/js/jquery-3.2.1.min.js', __FILE__); ?>"></script>
<script src="<?php echo plugins_url('statics/js/bootstrap.min.js', __FILE__); ?>"></script>

<!-- Navegación -->
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">
        Bienvenido a Mifirma Cliente
      </a>
    </div>
  </div>
</nav>
<!-- Fin de navegación -->

<!-- Contenedor principal -->
<div class="container">
  <!-- Inicia fila -->
  <div class="row">
    <?php
      if (isset($_GET['alert'])) {
        echo '
          <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <strong>'.$_GET['alert'].'</strong>
            </div>
          </div>
        ';
      }
     ?>
    <!-- Columna de autentificación con Ldap -->
    <div class="col-xs-12 col-md-7">
      <div class="panel panel-primary">
        <div class="panel-heading">Autentificación con protocolo Ldap</div>
        <div class="panel-body">
          <!-- formulario -->
          <form action="<?php echo LDAP_LOGIN;  ?>" method="POST">
            <div class="form-group">
              <label for="name">Nombre de usuario</label>
              <input type="name" class="form-control" id="name" name="user[0][name]" placeholder="Ingrese su nombre de usuario...">
            </div>
            <div class="form-group">
              <label for="password">Contraseña</label>
              <input type="password" class="form-control" id="password" name="user[0][password]" placeholder="Ingrese su contraseña...">
            </div>
            <button type="submit" class="btn btn-primary">Ingresar</button>
          </form>
          <!-- Fin de formulario -->
        </div>
      </div>
    </div> <!-- Fin de columna de autentificación con Ldap -->


    <!-- Columna autentificación con firma digital -->
    <div class="col-xs-12 col-md-5">
      <div class="panel panel-info">
        <div class="panel-heading">Autentificación con firma digital
          <?php
              if (count($_GET) > 0 && $data == null ) {
                echo '<button
                class="btn btn-xs btn-danger pull-right"
                type="button" data-toggle="collapse"
                data-target="#collapseExample"
                aria-expanded="false"
                aria-controls="collapseExample">
                Error
                </button>'; }
                ?>
        </div>
        <div class="panel-body">
          <?php
          if (isset($data)) {
            echo '<div class="well">Código: ' . $data .'</div>';
          } else {
            echo '<div class="collapse" id="collapseExample">
                    <br>
                    <div class="well">
                      '.json_encode($_GET).'
                    </div>
                  </div>
                  ';
          }
           ?>
          <!-- formulario -->
          <br>
          <form action="<?php echo MIFIRMA_LOGIN; ?>" method="POST">
            <div class="form-group">
              <label for="cedula">Cédula</label>
              <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Digite su número de cédula...">
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
          </form>
          <!-- Fin de formulario -->
        </div>
      </div>
    </div> <!-- Fin de columna autentificación con firma digital -->
  </div> <!-- Fin de fila -->
</div> <!-- Fin de contenedor principal -->

<script type="text/javascript">
  jQuery(document).ready(function($) {

    var code = null;
    var url_checker = "<?php echo MIFIRMA_CHECKER_DB; ?>";
    <?php
      if (isset($data)) {
        echo 'code = '."'".$data."'";

        echo '

        ';
      }
     ?>

     if (code != null) {
       setInterval(function(){
         $.ajax({
           type: "post",
           url: url_checker,
           data: {"code":code},
           success: function(data) {
             if (data != null && data != "") {
               window.location.href="mifirma/redirect.php?data="+data;
             }
           }
         });
       }, 3000);
     }

  });
</script>
