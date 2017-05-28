<?php

// Obtiene los métodos de wordpress
require_once("../../../../wp-load.php");
// Certificados y valores de la institución
require_once('values/institution.php');
require_once('../auth/auth.php');
require_once('../ldap/ldap_check_ced.php');

// Obtiene el número de cédula
$cedula = $_POST['cedula'];

if ( check_ldap_cedula($cedula)['exist'] ){ // Verifica si el usuario existe (../ldap/ladap_check_cced.php)\
  if ( check_ldap_cedula($cedula)['role'] ) {
    $user_exist = check_user_by_identification('usuario_'.$cedula); // Verifica si el usuario existe (../auth/auth.php)

    if($user_exist) {
      echo "Error al direccionar";
    } else {
      $date = date('Y-m-t H:i:s');
      $data = json_encode(array(
        'institution' => MIFIRMACR_INSTITUTION,
        'notification_url' => MIFIRMACR_LISTEN_URL,
        'identification' => $cedula,
        'request_datetime' => $date
      ));

      $data = utf8_encode($data);
      openssl_public_encrypt($data, $encrypted, MIFIRMACR_SERVER_PUBLIC_KEY, OPENSSL_PKCS1_OAEP_PADDING);
      $data = base64_encode($encrypted);

      $hashsum = hash(MIFIRMACR_ALGORITHM, $data);

      $params = array(
        "data_hash" => $hashsum,
        "algorithm" => MIFIRMACR_ALGORITHM,
        "public_certificate" => MIFIRMACR_PUBLIC_CERTIFICATE,
        'institution' => MIFIRMACR_INSTITUTION,
        "data" => $data
      );


      $response = http_post('https://mifirmacr.org/autentica/authenticate/', $params);

      if (isset($_SERVER["HTTP_REFERER"])) {
        $response = json_decode($response);
        header("Location: " .
        $_SERVER["HTTP_REFERER"] .
        '?' . http_build_query($response) .
        '&role=' . check_ldap_cedula($cedula)['role']);
      }
    }
  } else {
    header("Location: ../login_form.php?alert=Este usuario no tiene un rol asignado en nuestro directorio. Por favor comuníquese con el administrador para que le asigne un rol.");
    die();
  }

} else{
  //LDAP bind failed...
    header("Location: ../login_form.php?alert=La cédula no existe en nuestro directorio.");
    die();
}


/**
 * [http_post description]
 * @param  [type] $url  [description]
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
function http_post($url, $data)
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
