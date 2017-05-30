<?php
/** 
Obtiene los métodos de wordpress

@category Firma_Digital
@package  Mifirma
@author   Michael Vinicio Rodríguez Delgado <mvrd_17@hotmail.com>
@license  MIT <https://opensource.org/licenses/MIT>
@link     nubila.tech
*/

require_once "../../../../wp-load.php";

/**
 * Revisa si existe la cédula con la que se quiere firmar en el directorio de 
 * usuarios
 *
 * @param String $ced cédula del usuario
 * 
 * @return Array or Boolean
 */
function check_ldap_cedula($ced)
{
    $ldapserver  = 'ldaps://ldap.mifirmacr.org';
    $ldapbn      = "cn=admin,dc=ldap,dc=mifirmacr,dc=org";
    $ldapbnpass  = "AX7coRbA8dP6UoujeRFLv99Wf3N";
    $ldaptree    = "dc=ldap,dc=mifirmacr,dc=org";

    $ldapconn = ldap_connect($ldapserver) or die("Could not connect to LDAP server.");

    if ($ldapconn) {
        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        $ldapbind = ldap_bind($ldapconn, $ldapbn, $ldapbnpass);

        if ($ldapbind) {
            $result = ldap_search($ldapconn, $ldaptree, "(employeenumber=".$ced.")") 
            or die("Error in search query: ".ldap_error($ldapconn));
            $data = ldap_get_entries($ldapconn, $result);

            if ($data["count"] > 0) {
                // ced aceptada
                $employee_role = $data[0]["employeetype"][0];
                return ["exist" => true, "role" => $employee_role];
            } else {
                // ced NO aceptada
                return false;
            }

        } else {
            //LDAP bind failed...
            header("Location: ../login_form.php?alert=credenciales con ldap incorrectos");
            die();
        }

    } else {
        // LDAP conection failed...
        header("Location: ../login_form.php?alert=conexión con ldap falló");
        die();
    }
        ldap_close($ldapconn);
}

?>
