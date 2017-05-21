<?php

// Obtiene los métodos de wordpress
require_once("../../../../wp-load.php");

// Obtiene los datos del usuario que ingresaron por el formulario
$user = $_POST['user'];
$user_name = $user[0]['name'];
$user_password = $user[0]['password'];


////////////////////////////////////////////////////////////////////////////////
/**
 * Lógica de Ldap aquí <----------
 * Se debe tomar el nombre de usuario y la contraseña para hacer la consulta al
 * servidor de ldap, si el usuario y la contraseña son correctas se llama a la
 * función create_local_user
 */
 // create_local_user($user_name, $user_password); <---- estos parametros se deben modificar
////////////////////////////////////////////////////////////////////////////////

/**
 * Crea o verifica un usuario local
 * Si el usuario existe, trae la información y lo Redirecciona
 * Si el usuario no existe, lo crea y redirecciona
 * @param  [string] $name     [nombre de usuario ldap]
 * @param  [string] $password [contraseña del usuario ldap]
 * @return [type]           [description]
 */
function create_local_user($name, $password) {
  // Crea datos de usuario
  $website = "https://nubila.tech";
  $userdata = array(
    'user_login'  =>  $name,
    'user_url'    =>  $website,
    'user_pass'   =>  $password  // When creating an user, `user_pass` is expected.
  );
  // Intenta crear un usuar
  $user_id = wp_insert_user( $userdata ) ;
  // On success
  if ( ! is_wp_error( $user_id ) ) {
    echo "User created : ". $user_id; // <- id del usuario creado
  } else { // Si el usuario existe
    echo "Error (puede que usuario exista)";
  }
  check_user_and_redirect($name); // <- revisa el usuario y redirecciona
}



/**
 * Revisa si el usuario existe y lo redirecciona
 * @param  [string] $username [nombre de usuario o correo]
 */
function check_user_and_redirect($username) {
  var_dump($username);
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
