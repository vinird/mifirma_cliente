<?php
/**
Formularios de autentificación con ldap y firma digital

@category Firma_Digital
@package  Mifirma
@author   Michael Vinicio Rodríguez Delgado <mvrd_17@hotmail.com>
@license  MIT <https://opensource.org/licenses/MIT>
@link     nubila.tech
*/

$data = $_GET['code'];
$role = $_GET['role'];

require_once "../../../wp-load.php";

// Constantes
$url_ldap_login = plugins_url('ldap/ldap_login.php', __FILE__);
define('LDAP_LOGIN', $url_ldap_login);

$url_mifirma_login = plugins_url('mifirma/mifirma_login.php', __FILE__);
define('MIFIRMA_LOGIN', $url_mifirma_login);

$url_mifirma_checker_db = 'mifirma/checker_db.php';
define('MIFIRMA_CHECKER_DB', $url_mifirma_checker_db);

$this_form = plugins_url('login_form.php', __FILE__);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mi firma</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="<?php echo plugins_url('statics/css/bootstrap.min.css', __FILE__); ?>">
        <script src="<?php echo plugins_url('statics/js/jquery-3.2.1.min.js', __FILE__); ?>"></script>
        <script src="<?php echo plugins_url('statics/js/bootstrap.min.js', __FILE__); ?>"></script>
        <style media="screen">
        body {
        background-color: rgba(229, 239, 242, 1);
        }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
            <a class="navbar-brand" href="<?php echo $this_form; ?>">
                Bienvenido a Mifirma Cliente
            </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <?php
                if (count($_GET) > 0 &&  empty($data) ) {
                    echo '<li><a  href="'.$this_form.'"><button class="btn btn-xs btn-primary">
                    Limpiar errores
                    </button></a></li>';
                }
                ?>
                <?php
                if (count($_GET) > 0 && empty($data) && empty($_GET[alert]) ) {
                    if ($data == null) {
                        echo
                            '<li>
                                <a>
                                    <button
                                    class="btn btn-xs btn-danger"
                                    type="button" data-toggle="collapse"
                                    data-target="#collapseExample"
                                    aria-expanded="false"
                                    aria-controls="collapseExample">
                                        Ver error
                                    </button>
                                </a>
                            </li>';
                    }
                }
                ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo $this_form; ?>">Recargar formulario</a></li>
            </ul>
            </div>
        </div>
        </nav>
        <!-- Fin de navegación -->

        <!-- Contenedor principal -->
        <div class="container">
        <!-- Inicia fila -->
        <div class="row">

            <div class="col-xs-12">
                <?php
                if (! isset($data) && empty($data) && empty($_GET[alert])) {
                    echo
                        '<div class="collapse" id="collapseExample">
                            <br>
                            <div class="well">
                                '.json_encode($_GET).'
                            </div>
                        </div>';
                }
                ?>
            </div>

            <div class="col-xs-12">
                <?php
                if (isset($_GET['alert'])) {
                    echo '
                    <div class="col-xs-12">
                        <div class="alert alert-danger alert-dismissible" role="alert">
                        <a href="'.$this_form.'"
                            type="button" class="close"
                            data-dismiss="alert"
                            aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                        </a>
                        <strong>'.$_GET['alert'].'</strong>
                        </div>
                    </div>
                    ';
                }
                ?>
            </div>
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
                    <button type="submit" class="btn btn-primary"
                    <?php
                    if (count($_GET) > 0) {
                        echo "disabled";
                    }
                    ?>
                    >Ingresar</button>
                    </form>
                    <!-- Fin de formulario -->
                </div>
                </div>
            </div> <!-- Fin de columna de autentificación con Ldap -->

            <!-- Columna autentificación con firma digital -->
            <div class="col-xs-12 col-md-5">
                <div class="panel panel-warning">
                <div class="panel-heading">
                    Autentificación con firma digital
                </div>
                <div class="panel-body">
                    <?php
                    if (isset($data)) {
                        echo '<div class="well">Código: ' . $data .'</div>';
                    }
                    ?>
                    <!-- formulario -->
                    <br>
                    <form action="<?php echo MIFIRMA_LOGIN; ?>" method="POST">
                    <div class="form-group">
                        <label for="cedula">Cédula</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Digite su número de cédula..." required>
                    </div>
                    <button type="submit" class="btn btn-primary"
                    <?php
                    if (count($_GET) > 0) {
                        echo "disabled";
                    }
                    ?>
                    >Enviar</button>
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
            if (isset($data) && isset($role)) {
                echo 'var code = '."'".$data."';";
                echo 'var role = '."'".$role."';";
            }
            ?>

            if (code != null && null != role) {
            setInterval(function(){
                $.ajax({
                type: "post",
                url: url_checker,
                data: {"code":code},
                success: function(data) {
                    console.log(data);
                    if (data != null && data != "") {
                    window.location.href="mifirma/redirect.php?data=" + data + "&role=" + role;
                    }
                }
                });
            }, 3000);
            }

        });
        </script>
    </div>

    </body>
</html>
