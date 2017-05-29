<?php
/**
Define los valores de la intitución:
url, algoritmo, código, llave privada, llave pública 
y el certificado.

@category Firma_Digital
@package  Mifirma
@author   Michael Vinicio Rodríguez Delgado <mvrd_17@hotmail.com>
@license  MIT <https://opensource.org/licenses/MIT>
@link     nubila.tech
*/

require_once "../../../../wp-load.php";

$data = Get_values();

define("MIFIRMACR_LISTEN_URL", $data[0]->mifirmacr_listen_url);
define("MIFIRMACR_ALGORITHM", $data[0]->mifirmacr_algorithm);
define("MIFIRMACR_INSTITUTION", $data[0]->mifirmacr_institution);
define("MIFIRMACR_PRIVATE_KEY", $data[0]->mifirmacr_private_key);
define("MIFIRMACR_PUBLIC_CERTIFICATE", $data[0]->mifirmacr_public_certificate);
define("MIFIRMACR_SERVER_PUBLIC_KEY", $data[0]->mifirmacr_server_public_key);

/**
 * Obtiene los valores de la intitución de la base de datos
 *
 * @return array $values [datos de la institución]
 */
function Get_values()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'mifirma_institutions';
    $data = $wpdb->get_results('SELECT * FROM '.$table_name.'', OBJECT_K);
    $values = [];
    foreach ($data as $key => $value) {
        array_push($values, $value);
    }
    return $values;
}
?>
