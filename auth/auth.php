<?php
/**
Crea usuarios de wordpress utilizando el nombre de usuario,
la cédula como contraseña, y el rol determinado por el 
directorio de ldap.

@category Firma_Digital
@package  Mifirma
@author   Michael Vinicio Rodríguez Delgado <mvrd_17@hotmail.com>
@license  MIT <https://opensource.org/licenses/MIT>
@link     nubila.tech
*/
require_once "../../../../wp-load.php";

/**
 * Crea o verifica un usuario local
 * Si el usuario existe, trae la información y lo Redirecciona
 * Si el usuario no existe, lo crea y redirecciona
 * 
 * @param string $name     [nombre de usuario ldap]
 * @param string $password [contraseña del usuario ldap]
 * @param string $role     [rol de usuario ldap]
 * 
 * @return string           [description]
 */
function create_local_user($name, $password, $role = 'subscriber') 
{
    // Selecciona tipo de usuario y asigna respectivo ROL
    $userdata_role = "";
    if ($role == "abonado") {
        $userdata_role = 'subscriber';
    } elseif ($role == "funcionario") {
        $userdata_role = 'editor';
    }
    // Crea datos de usuario
    $website = "https://nubila.tech";
    $userdata = array(
      'user_login'  =>  $name,
      'user_url'    =>  $website,
      'user_pass'   =>  $password, // When creating an user, `user_pass` is expected.
      'role'        =>  $userdata_role
    );
    // Intenta crear un usuar
    $user_id = wp_insert_user($userdata);
    // On success
    if (! is_wp_error($user_id)) {
        echo "User created : ". $user_id; // <- id del usuario creado
    } else { // Si el usuario existe
        echo "Error (puede que usuario exista)";
    }
    check_user_and_redirect($name); // <- revisa el usuario y redirecciona
}

/**
 * Revisa si el usuario existe y lo redirecciona
 * 
 * @param string $username [nombre de usuario o correo]
 * 
 * @return void
 */
function check_user_and_redirect($username) 
{
    // Obtiene  el usuario por el nombre de usuario o correo
    $user = get_user_by('login', $username);
    // Se ejecuta si encuentra el usuario
    if (!is_wp_error($user)) {
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        $redirect_to = user_admin_url();
        wp_safe_redirect($redirect_to);
        exit();
    }
}

/**
 * [Verifica si un usuario existe utilizando el nombre de usuario y ceédula]
 *
 * @param string $username nombre de usuario (usuario_numerodeDeCedula)
 * 
 * @return void
 */
function check_user_by_identification($username) 
{
    // Obtiene  el usuario por el nombre de usuario o correo
    if (username_exists($username)) {
        $user = get_user_by('login', $username);
        // Se ejecuta si encuentra el usuario
        if (!is_wp_error($user)) {
            wp_clear_auth_cookie();
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID);

            $redirect_to = user_admin_url();
            wp_safe_redirect($redirect_to);
            return true;
        }
    } else {
        return false;
    }
}
?>
