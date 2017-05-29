<?php

// Obtiene los métodos de wordpress
require_once("../../../../wp-load.php");
require_once("../auth/auth.php");

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


?>
