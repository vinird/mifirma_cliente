<?php
$data = NULL;
if ( isset($_POST)) {
  require_once('values/institution.php');
  require_once('values/mifirma_certs.php');
  require_once("../../../../wp-load.php");
  require_once("../auth/auth.php");
  global $wpdb;

  $table = $wpdb->prefix . 'mifirma_server_response';
  $table_user = $wpdb->prefix . 'mifirma_users';

  $data = $wpdb->get_results( 'SELECT * FROM '.$table.' WHERE code = "'.$_POST['code'].'" LIMIT 1', OBJECT_K );

  if (count($data) > 0) {
    $values = NULL;
    $decrypted = NULL;
    foreach ($data as $key ) {
        $values = $key;
    }
    /////////////////////////
    $hashsum = hash($values->algorithm, $values->data);
    $equals = hash_equals($hashsum, $values->hashsum);
    if ( $equals ) {
      $decoded_data = base64_decode($values->data);
      openssl_private_decrypt( $decoded_data , $decrypted , MIFIRMACR_PRIVATE_KEY , OPENSSL_PKCS1_OAEP_PADDING );
      $decrypted = json_decode($decrypted);

      // Hash identificación
      $hash_identification = hash(MIFIRMACR_ALGORITHM, $decrypted->identification);
      $array = array('user_hashsum' => $hash_identification );
      $success = $wpdb->insert( $table_user, $array, array('%s') );
    
      echo $decrypted->identification;
    }
    /////////////////////////
  } else {
    echo false;
  }
  // echo json_encode($data);

}
?>
