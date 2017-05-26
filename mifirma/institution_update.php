<?php
require_once("../../../../wp-load.php");
global $wpdb;

if (isset($_POST)) {
  if ($_POST['mifirmacr_listen_url'] != ""
    && $_POST['mifirmacr_algorithm'] != ""
    && $_POST['mifirmacr_institution'] != ""
    && $_POST['mifirmacr_private_key'] != ""
    && $_POST['mifirmacr_public_certificate'] != ""
    && $_POST['mifirmacr_server_public_key'] != ""
    ) {
    $table_name = $wpdb->prefix . 'mifirma_institutions';
    $data = $wpdb->get_results( 'SELECT * FROM '.$table_name.'', OBJECT_K );
    if (count($data) > 0) {
      $data = get_values($data);
      $wpdb->update( $table_name, $_POST, array('id'=> $data[0]->id), array('%s','%s', '%s', '%s', '%s', '%s') ); // Actualiza
      echo json_encode(['alert' => "Datos actualizados", "class" => "success"]); // Mensaje
    } else {
      $wpdb->insert( $table_name, $_POST, array('%s','%s', '%s', '%s', '%s', '%s') ); // Inserta 
      echo json_encode(['alert' => "Nuevos datos ingresados", "class" => "success"]); // Mensaje
    }
  } else {
    echo json_encode(['alert' => "Es necesario ingresar todos los datos", "class" => "warning"]); // Mensaje
  }
} else {
  echo json_encode(['alert' => "Error, no ha ingresado ningún dato", "class" => "danger"]); // Mensaje
}

/**
 * Obtiene los valores del array
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
function get_values($data)
{
  $values = [];
  foreach ($data as $key => $value) {
    array_push($values, $value);
  }
  return $values;
}
 ?>
