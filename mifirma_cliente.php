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
add_action('init', 'redirect_wp_admin');

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
      wp_safe_redirect($custom_login_template_url);
    }
  }
}

?>
