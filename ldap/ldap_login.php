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
$ldapserver  = 'ldaps://ldap.mifirmacr.org';
$ldapuser    = 'uid='.$user_name;  
$ldappass    = $user_password;
$ldaptree    = "dc=ldap,dc=mifirmacr,dc=org";
$ldapbn      = "cn=admin,dc=ldap,dc=mifirmacr,dc=org";
$ldapbnpass  = "AX7coRbA8dP6UoujeRFLv99Wf3N";

// connect 
$ldapconn = ldap_connect($ldapserver) or die("Could not connect to LDAP server.");
if($ldapconn) {
    // binding to ldap server
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    $ldapbind = ldap_bind($ldapconn, $ldapbn, $ldapbnpass);
    // or die ("Error trying to bind: ".ldap_error($ldapconn))
    	
    // verify binding
    if ($ldapbind) {
        //echo "LDAP bind successful...<br /><br />";
        
        
        $result = ldap_search($ldapconn,$ldaptree,$ldapuser) or die ("Error in search query: ".ldap_error($ldapconn));
        
        $data = ldap_get_entries($ldapconn, $result);        
        
        // SHOW ALL DATA
        //echo '<h1>Dump all data</h1><pre>';
        // print_r($data);    
        //echo '</pre>';

	        if($data["count"] > 0){
	        	for ($i=0; $i<$data["count"]; $i++) {

		            $user_uid       = $data[$i]["uid"][0];
		            $user_ldap_pass = $data[$i]["userpassword"][0];
		            $user_role      = $data[$i]["employeetype"][0];		            

		        }
		        //print_r($user_role);
	        }else{
	        	header("Location: ../login_form.php?alert=usuario incorrecto");
        		die();
	        }	                            
        
        // var_dump($user_ced);
    } else {
        echo "LDAP bind failed...";
        header("Location: ../login_form.php?alert=credenciales con ldap incorrectos");
        die();
    }
}
// all done? clean up
ldap_close($ldapconn);

if($user_ldap_pass != $ldappass){
	header("Location: ../login_form.php?alert=contraseña incorrecta");
    die();
}else{
	create_local_user($user_uid, $user_password, $user_role);
}

////////////////////////////////////////////////////////////////////////////////

/**
 * Crea o verifica un usuario local
 * Si el usuario existe, trae la información y lo Redirecciona
 * Si el usuario no existe, lo crea y redirecciona
 * @param  [string] $name     [nombre de usuario ldap]
 * @param  [string] $password [contraseña del usuario ldap]
 * @return [type]           [description]
 */
function create_local_user($name, $password, $role) {
	// Selecciona tipo de usuario y asigna respectivo ROL
	if($role == "abonado"){
		$userdata_role = 'subscriber';
	}elseif($role == "funcionario"){
		$userdata_role = 'editor';
	}

  // Crea datos de usuario
  $website = "https://nubila.tech";
  $userdata = array(
    'user_login'  =>  $name,
    'user_url'    =>  $website,
    'user_pass'   =>  $password,
    'role'        =>  $userdata_role
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

function check_ldap_cedula($ced) {
	$ldapserver  = 'ldaps://ldap.mifirmacr.org';
	$ldapbn      = "cn=admin,dc=ldap,dc=mifirmacr,dc=org";
	$ldapbnpass  = "AX7coRbA8dP6UoujeRFLv99Wf3N";
	$ldaptree    = "dc=ldap,dc=mifirmacr,dc=org";

	$ldapconn = ldap_connect($ldapserver) or die("Could not connect to LDAP server.");

	if($ldapconn){

		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    	$ldapbind = ldap_bind($ldapconn, $ldapbn, $ldapbnpass);

    	if($ldapbind){
    		$result = ldap_search($ldapconn,$ldaptree,"(employeenumber=".$ced.")") or die ("Error in search query: ".ldap_error($ldapconn));
	        
	        $data = ldap_get_entries($ldapconn, $result);
	        
	        if($data["count"] > 0){	        		        	
	        	// ced aceptada
	        	return true;
	        }else{	        
	        	// ced NO aceptada
	        	return false;
	        }	        

    	}else{
    		//LDAP bind failed...
	        header("Location: ../login_form.php?alert=credenciales con ldap incorrectos");
	        die();
    	}

	}else{
		// LDAP conection failed...
	    header("Location: ../login_form.php?alert=conexión con ldap falló");
	    die();
	}
	ldap_close($ldapconn);
}

?>
