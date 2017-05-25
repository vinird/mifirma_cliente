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

/**
 * [listener description]
 * @return [type] [description]
 */
function listener() {
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

////////////////////////////////////////////////////////////////////////////////
// function create_routes( $router ) {
//     $router->add_route('listener
//     ', array(
//         'path' => 'listener',
//         'access_callback' => true,
//         'page_callback' => 'listener'
//     ));
// }
// add_action( 'wp_router_generate_routes', 'create_routes' );
//
// function listener() {
//     load_template(get_template_directory() . '/user.php', false );
// }
//
// add_action( 'rest_api_init', 'wpshout_register_routes' );
//
// function wpshout_register_routes() {
//     register_rest_route(
//         'myplugin/v1',
//         '/author/(?P<id>\d+)',
//         array(
//             'methods' => 'GET',
//             'callback' => 'wpshout_find_author_post_title',
//         )
//     );
// }

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
}




?>
