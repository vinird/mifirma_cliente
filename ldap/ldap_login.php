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

// config
$ldapserver = 'ldaps://ldap.mifirmacr.org';
$ldapuser      = 'uid='.$user_name;  
$ldappass     = $user_password;
$ldaptree    = "ou=afiliados,ou=B34551,dc=ldap,dc=mifirmacr,dc=org";
$domain = home_url( ); //or home

// connect 
$ldapconn = ldap_connect($ldapserver) or die("Could not connect to LDAP server.");

if($ldapconn) {
    // binding to ldap server
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    $ldapbind = ldap_bind($ldapconn, $ldapuser.','.$ldaptree, $ldappass);
    // or die ("Error trying to bind: ".ldap_error($ldapconn))
    	
    // verify binding
    if ($ldapbind) {
        echo "LDAP bind successful...<br /><br />";
        
        
        $result = ldap_search($ldapconn,$ldaptree,$ldapuser) or die ("Error in search query: ".ldap_error($ldapconn));
        $data = ldap_get_entries($ldapconn, $result);
        
        // SHOW ALL DATA
        //echo '<h1>Dump all data</h1><pre>';
        //print_r($data);    
        //echo '</pre>';
        
        
        // iterate over array and print data for each entry
        // echo '<h1>Show me the users</h1>';
        for ($i=0; $i<$data["count"]; $i++) {
            //echo "dn is: ". $data[$i]["dn"] ."<br />";
            //echo "User: ". $data[$i]["uid"][0] ."<br />";
            $user_uid = $data[$i]["uid"][0];
            //if(isset($data[$i]["mail"][0])) {
              //  echo "Email: ". $data[$i]["mail"][0] ."<br /><br />";
            //} else {
              //  echo "Email: None<br /><br />";
            //}
        }
        // print number of entries found
        //echo "Number of entries found: " . ldap_count_entries($ldapconn, $result);
    } else {
        echo "LDAP bind failed...";
        header("Location: ".$domain."/wp-admin");
        die();
    }
}

// all done? clean up
ldap_close($ldapconn);

create_local_user($user_uid, $user_password);
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
