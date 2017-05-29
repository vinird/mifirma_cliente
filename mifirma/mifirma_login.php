<?php
/**
Se encarga de tomar la identificación del usuario y verificar 
si existe en el directorio del ldap, si existe hace la petición 
al servidor de mifirmacr enviando los datos de la organización
encriptados.

@category Firma_Digital
@package  Mifirma
@author   Michael Vinicio Rodríguez Delgado <mvrd_17@hotmail.com>
@license  MIT <https://opensource.org/licenses/MIT>
@link     nubila.tech
*/

// Obtiene los métodos de wordpress
require_once "../../../../wp-load.php";
// Certificados y valores de la institución
require_once 'values/institution.php';
// Autentifica usuarios en wordpress
require_once '../auth/auth.php';
// Revisa si un usu existe en el directorio de ldap
require_once '../ldap/ldap_check_ced.php';

// Obtiene el número de cédula
$cedula = $_POST['cedula'];

if (check_ldap_cedula($cedula)['exist'] ) { // Verifica si el usuario existe (../ldap/ladap_check_cced.php)\
    if (check_ldap_cedula($cedula)['role'] ) { // Verifica si tiene un rol asignado
        $user_exist = check_user_by_identification('usuario_'.$cedula); // Verifica si el usuario existe en wordpress (../auth/auth.php)

        if ($user_exist) {
            echo "Error al direccionar";
        } else {
            // Crea el array de datos para enviarlos al servidor
            $data = json_encode(
                array(
                  'institution' => MIFIRMACR_INSTITUTION,
                  'notification_url' => MIFIRMACR_LISTEN_URL,
                  'identification' => $cedula,
                  'request_datetime' => date('Y-m-t H:i:s')
                )
            );

            // Encriptación y hash
            $data = utf8_encode($data);
            openssl_public_encrypt($data, $encrypted, MIFIRMACR_SERVER_PUBLIC_KEY, OPENSSL_PKCS1_OAEP_PADDING);
            $data = base64_encode($encrypted);
            $hashsum = hash(MIFIRMACR_ALGORITHM, $data);

            // Parametros del post que se envian el servidor
            $params = array(
              "data_hash" => $hashsum,
              "algorithm" => MIFIRMACR_ALGORITHM,
              "public_certificate" => MIFIRMACR_PUBLIC_CERTIFICATE,
              'institution' => MIFIRMACR_INSTITUTION,
              "data" => $data
            );

            // Realiza la petición al servidor
            $response = Http_post('https://mifirmacr.org/autentica/authenticate/', $params);

            if (isset($_SERVER["HTTP_REFERER"])) {
                $response = json_decode($response);
                header(
                    "Location: " .
                    $_SERVER["HTTP_REFERER"] .
                    '?' . http_build_query($response) .
                    '&role=' . check_ldap_cedula($cedula)['role']
                );
            }
        }
    } else {
        header(
            "Location: ../login_form.php?
            alert=Este usuario no tiene un rol asignado en nuestro directorio. 
            Por favor comuníquese con el administrador para que le asigne un rol."
        );
        die();
    }
} else {
    header("Location: ../login_form.php?alert=La cédula no existe en nuestro directorio.");
    die();
}

/**
 * Realiza una petición de tipo post a la url indicada
 * y envía un array con parametros
 * 
 * @param string $url  [url del servidor mifirmacr]
 * @param array  $data [datos encriptados]
 * 
 * @return array        [respuesta del servidor]
 */
function Http_post($url, $data)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $_response = curl_exec($curl);
    curl_close($curl);
    return $_response;
}
?>
