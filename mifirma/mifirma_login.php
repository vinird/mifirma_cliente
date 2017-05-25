<?php

// Obtiene los métodos de wordpress
require_once("../../../../wp-load.php");
// Certificados y valores de la institución
require_once('values/mifirma_certs.php');
require_once('values/institution.php');
require_once('../auth/auth.php');

// Obtiene el número de cédula
$cedula = $_POST['cedula'];


$user_exist = check_user_by_identification('usuario_'.$cedula); // Verifica si el usuario existe

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
    // session_start();
    // $_SESSION['response'] = $response;
    header("Location: " . $_SERVER["HTTP_REFERER"] . '?' . http_build_query($response));
  }
}

// Create a POST request
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