<?php
/*
Plugin Name: Mifirma Cliente
Plugin URI: https://nubila.tech
Description:  Autentifica con protocolo Ldap y firma autentificación con  cédula.
Version: 1.0
Author: MV
Author URI: https://nubila.tech
*/

/**
 * Agrega una nuva acción que se utiliza para redireccionar
 */
register_activation_hook( __FILE__, 'mifirma_cliente_install_db' );
register_uninstall_hook( __FILE__, 'mifirma_cliente_uninstall_db' );

add_action('init', 'redirect_wp_admin');
add_action( 'wp_loaded','listener' );
add_action('admin_menu', 'admin_menu_administration');

/**
 * [listener description]
 * @return [type] [description]
 */
function listener() {
  // Escucha el post del servidor mifirmacr
  if( isset($_POST) ) {
    if ( isset($_POST["hashsum"]) && isset($_POST["data"]) && isset($_POST["algorithm"]) && isset($_POST["code"])) {

      global $wpdb;
      $table = $wpdb->prefix . 'mifirma_server_response';
      $data = $wpdb->get_results( 'SELECT * FROM '.$table.' WHERE code = "'.$_POST['code'].'"', OBJECT_K );

      if (count($data) == 0) {
        $table = $wpdb->prefix . 'mifirma_server_response';
        $wpdb->insert( $table, $_POST, array('%s','%s', '%s', '%s') );
      }
    }
  }
}


/**
 * Redirecciona la ruta /wp-admin a una ruta custom
 */
function redirect_wp_admin(){

  // Obtiene la ruta solicitada
  $redirect_to = $_SERVER['REQUEST_URI'];

  // Verifica si hay una redirección en el request
  if(count($_REQUEST)> 0 && array_key_exists('redirect_to', $_REQUEST)){
    $redirect_to = $_REQUEST['redirect_to'];
    $check_wp_admin = stristr($redirect_to, 'wp-admin');

    // Verifica si la redirección apunta a wp-admin
    if($check_wp_admin){
      // Obtiene ruta del plugin
      $custom_login_template_url = plugins_url('login_form.php', __FILE__);
      // Redirecciona a la plantilla con el formulario custom
      wp_safe_redirect($custom_login_template_url); // <- Aquí se muestra el formulario
    }

  }

  // add_rewrite_rule('listener/$', $custom_login_template_url, 'top');
  // $wp_rewrite->flush_rules(true);  // This should really be done in a plugin activation


  // add_rewrite_rule(
  //       "^vini/?$",
  //       plugins_url('login_form.php', __FILE__),
  //       "top");

  // add_rewrite_rule('^wp-listener$', 'mifirma/listener.php?route_name=my_plugin_index');
}

function httpPost($url, $data)
{
  $data = json_encode($data);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url.'/'.$data );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $_response = curl_exec($curl);
    curl_close($curl);
    return $_response;
}

/**
 * [mifirma_cliente_install_db description]
 * @return [type] [description]
 */
function mifirma_cliente_install_db() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'mifirma_server_response';
  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    hashsum varchar(256) NOT NULL,
    data text NOT NULL,
    algorithm varchar(10) NOT NULL,
    code varchar(100) NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

  $table_name = $wpdb->prefix . 'mifirma_users';
  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    user_hashsum varchar(256) NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";
  dbDelta( $sql );

  $table_name = $wpdb->prefix . 'mifirma_institutions';
  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    mifirmacr_listen_url varchar(255) NOT NULL,
    mifirmacr_algorithm varchar(10) NOT NULL,
    mifirmacr_institution varchar(255) NOT NULL,
    mifirmacr_private_key text NOT NULL,
    mifirmacr_public_certificate text NOT NULL,
    mifirmacr_server_public_key text NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";
  dbDelta( $sql );

  }



/**
 * [tm7200_uninstall_db description]
 * @return [type] [description]
 */
function mifirma_cliente_uninstall_db() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'mifirma_server_response';
  $sql = "DROP TABLE IF EXISTS $table_name;";
  dbDelta( $sql );

  $table_name = $wpdb->prefix . 'mifirma_users';
  $sql = "DROP TABLE IF EXISTS $table_name;";
  dbDelta( $sql );

  $table_name = $wpdb->prefix . 'mifirma_institutions';
  $sql = "DROP TABLE IF EXISTS $table_name;";
  dbDelta( $sql );
}

function admin_menu_administration() {
  add_menu_page( 'Menu administrativo de mi firma cliente', 'Mi firma', 'manage_options', 'mi-firma-plugin-administration', 'admin_page_init', 'dashicons-post-status
' );
}

function admin_page_init() {
  echo '<link rel="stylesheet" href="'.plugins_url('statics/css/bootstrap.min.css', __FILE__).'">
  <script src="'.plugins_url('statics/js/jquery-3.2.1.min.js', __FILE__).'"></script>
  <script src="'.plugins_url('statics/js/bootstrap.min.js', __FILE__).'"></script>';

  echo '
  <br>
  <div class="container">
  <div class="alert alert-dismissible" role="alert">
  <strong id="mifirma-message"></strong>
  </div>
  <h3>Administración de la institución en mifirmacr</h3>
  <br>
  <div class="row">
  <form class="form-horizontal" id="mifirma-form-update-institution">
  <div class="form-group">
    <label for="mifirmacr_listen_url" class="col-xs-12 col-sm-2 control-label">Url de notificación:</label>
    <div class="col-xs-12 col-sm-6">
      <input type="text" class="form-control" id="mifirmacr_listen_url" placeholder="http://example.com">
    </div>
  </div>
  <div class="form-group">
    <label for="mifirmacr_algorithm" class="col-xs-12 col-sm-2 control-label">Algoritmo:</label>
    <div class="col-xs-12 col-sm-2">
      <input type="text" class="form-control" id="mifirmacr_algorithm" value="sha256" disabled>
    </div>
  </div>
  <div class="form-group">
    <label for="mifirmacr_institution" class="col-xs-12 col-sm-2 control-label">Código de la institución:</label>
    <div class="col-xs-12 col-sm-6">
      <input type="password" class="form-control" id="mifirmacr_institution" placeholder="OIS0QWIE78297372...">
    </div>
  </div>
  <div class="form-group">
    <label for="mifirmacr_private_key" class="col-xs-12 col-sm-2 control-label">RSA llave privada:</label>
    <div class="col-xs-12 col-sm-6">
      <textarea class="form-control" id="mifirmacr_private_key" placeholder="-----BEGIN PRIVATE KEY-----"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="mifirmacr_server_public_key" class="col-xs-12 col-sm-2 control-label">RSA llave pública:</label>
    <div class="col-xs-12 col-sm-6">
      <textarea class="form-control" id="mifirmacr_server_public_key" placeholder="-----BEGIN PUBLIC KEY-----"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="mifirmacr_public_certificate" class="col-xs-12 col-sm-2 control-label">RSA certificado:</label>
    <div class="col-xs-12 col-sm-6">
      <textarea class="form-control" id="mifirmacr_public_certificate" placeholder="-----BEGIN CERTIFICATE-----"></textarea>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-xs-12 col-sm-6">
      <a onclick="post_institution_data()" class="btn btn-primary">Actualizar datos</a>
    </div>
  </div>
</form>
</div>
</div>';

echo '
  <script>
  var alert = $(".alert");
  alert.hide();

  function post_institution_data(){
    var data = {
      "mifirmacr_listen_url": $("#mifirmacr_listen_url").val(),
      "mifirmacr_algorithm": $("#mifirmacr_algorithm").val(),
      "mifirmacr_institution": $("#mifirmacr_institution").val(),
      "mifirmacr_private_key": $("#mifirmacr_private_key").val(),
      "mifirmacr_public_certificate": $("#mifirmacr_public_certificate").val(),
      "mifirmacr_server_public_key": $("#mifirmacr_server_public_key").val()
    };
    alert.hide();
    $.ajax({
      type: "post",
      url: "'.plugins_url('mifirma/institution_update.php', __FILE__).'",
      data: data,
      success: function(data) {
        data = JSON.parse(data);
        if (data != null) {
            alert.removeClass("alert-success");
            alert.removeClass("alert-danger");
            alert.removeClass("alert-warning");
            alert.addClass(data.class);
            var message = $("#mifirma-message").html(data.alert);


            alert.fadeIn(500);
            setTimeout(function(){
              alert.hide(1000);
            },5000)

            if (data.class == "alert-success") {
              $("#mifirma-form-update-institution").trigger("reset");
            }
        }
      }
    });
  }
  </script>
';
}
?>
