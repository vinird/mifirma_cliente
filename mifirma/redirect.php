<?php
/**
Este archivo se ejecuta cuando mifirmacr a acceptado 
la solicitud y a notificado al cliente que puede acceder.
Recibe el número de cédula que ingresó el usuario y
el rol que contiene en el directorio del ldap.

@category Firma_Digital
@package  Mifirma
@author   Michael Vinicio Rodríguez Delgado <mvrd_17@hotmail.com>
@license  MIT <https://opensource.org/licenses/MIT>
@link     nubila.tech
*/

require_once '../auth/auth.php';
if (isset($_GET)) {
    create_local_user('usuario_'.$_GET['data'], $_GET['data'], $_GET['role']);
}
?>
