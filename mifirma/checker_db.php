<?php
/**
Verifica si el código que retornó de mifirmacr
se encuentra de en la base de datos, si se encuentra
significa que la petición fue firmada. 

Si la petición fue firmada se ingresa la identificación del usuario 
en la base de datos pero los valores se pasan por un hash. 

@category Firma_Digital
@package  Mifirma
@author   Michael Vinicio Rodríguez Delgado <mvrd_17@hotmail.com>
@license  MIT <https://opensource.org/licenses/MIT>
@link     nubila.tech
*/

$data = null;
if (isset($_POST)) {
    include_once 'values/institution.php';
    include_once "../../../../wp-load.php";
    include_once "../auth/auth.php";
    global $wpdb;

    $table = $wpdb->prefix . 'mifirma_server_response';

    // Consulta si el código existe en la base de datos
    $data = $wpdb->get_results(
        'SELECT * FROM '.$table.
        ' WHERE code = "'.$_POST['code'].
        '" LIMIT 1', 
        OBJECT_K
    );

    // Si el código existe se ejecuta el bloque
    if (count($data) > 0) {
        $values = null;
        $decrypted = null;

        // Extrae los valores
        foreach ($data as $key ) {
            $values = $key;
        }

        // Realiza la suma hash
        $hashsum = hash($values->algorithm, $values->data);
        // Verifica si el hash coincide
        $equals = hash_equals($hashsum, $values->hashsum);
        // Si coincide se ejecuta el bloque
        if ($equals) {

            $decoded_data = base64_decode($values->data);
            // Desencripta los datos
            openssl_private_decrypt(
                $decoded_data, $decrypted, 
                MIFIRMACR_PRIVATE_KEY, 
                OPENSSL_PKCS1_OAEP_PADDING
            );

            $decrypted = json_decode($decrypted);

            // Hash identificación
            $hash_identification = hash(MIFIRMACR_ALGORITHM, $decrypted->identification);
            // Crea array con la identificación del usuario
            $array = array('user_hashsum' => $hash_identification );

            $table_user = $wpdb->prefix . 'mifirma_users';
            // Inserta el hash en la tabla de usuarios del plugin
            $success = $wpdb->insert($table_user, $array, array('%s'));

            // Retorna la identificación
            echo $decrypted->identification;
        }
    } else {
        echo false;
    }
    // echo json_encode($data);

}
?>
