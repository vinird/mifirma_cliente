<?php
require_once("../../../../wp-load.php");
global $wpdb;

if (isset($_POST)) {
  if ( check_not_empty($_POST) ) {
    $check_pattern_ = check_pattern($_POST);
    if ( $check_pattern_['valid'] ){
      $table_name = $wpdb->prefix . 'mifirma_institutions';
      $data = $wpdb->get_results( 'SELECT * FROM '.$table_name.'', OBJECT_K );
      if (count($data) > 0) {
        $data = get_values($data);
        $wpdb->update( $table_name, $_POST, array('id'=> $data[0]->id), array('%s','%s', '%s', '%s', '%s', '%s') ); // Actualiza
        echo json_encode(['alert' => "Datos actualizados", "class" => "alert-success"]); // Mensaje
      } else {
        $wpdb->insert( $table_name, $_POST, array('%s','%s', '%s', '%s', '%s', '%s') ); // Inserta
        echo json_encode(['alert' => "Nuevos datos actualizados", "class" => "alert-success"]); // Mensaje
      }
    } else { // Pattern check
      echo json_encode(['alert' => $check_pattern_['output'], "class" => "alert-warning"]); // Mensaje
    }
  } else {
    echo json_encode(['alert' => "Es necesario ingresar todos los datos", "class" => "alert-warning"]); // Mensaje
  }
} else {
  echo json_encode(['alert' => "Error, no ha ingresado ningún dato", "class" => "alert-danger"]); // Mensaje
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

function check_not_empty($post)
{
  if ($post['mifirmacr_listen_url'] != ""
    && $post['mifirmacr_algorithm'] != ""
    && $post['mifirmacr_institution'] != ""
    && $post['mifirmacr_private_key'] != ""
    && $post['mifirmacr_public_certificate'] != ""
    && $post['mifirmacr_server_public_key'] != "") {
      return TRUE;
    } else {
      return FALSE;
    }
}

/**
 * [check_pattern description]
 * @param  [type] $post [description]
 * @return [type]       [description]
 */
function check_pattern($post)
{
  $url_exist = url_exist($post['mifirmacr_listen_url']);
  $algorithm_valid = algorithm_valid($post['mifirmacr_algorithm']);

  if ( $url_exist == false ) {
    $pattern_check_output = $pattern_check_output."<li>La Url no existe: ".$post['mifirmacr_listen_url']."</li>";
  }
  if ($algorithm_valid == false){
    $pattern_check_output = $pattern_check_output."<li>".$post['mifirmacr_algorithm']." no es un algoritmo válido, utilice sha256</li>";
  }

  if ( $url_exist && $algorithm_valid) {
    return  ["valid" => ture];
  } else {
    return  ["valid" => false, "output" => $pattern_check_output];
  }

}

/**
 * [url_exist description]
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function url_exist($url){//se passar a URL existe
    $c=curl_init();
    curl_setopt($c,CURLOPT_URL,$url);
    curl_setopt($c,CURLOPT_HEADER,1);//get the header
    curl_setopt($c,CURLOPT_NOBODY,1);//and *only* get the header
    curl_setopt($c,CURLOPT_RETURNTRANSFER,1);//get the response as a string from curl_exec(), rather than echoing it
    curl_setopt($c,CURLOPT_FRESH_CONNECT,1);//don't use a cached version of the url
    if(!curl_exec($c)){
        //echo $url.' inexists';
        return false;
    }else{
        //echo $url.' exists';
        return true;
    }
    //$httpcode=curl_getinfo($c,CURLINFO_HTTP_CODE);
    //return ($httpcode<400);
}

function algorithm_valid($algorithm)
{
  if ( $algorithm == "sha256" ) {
    return true;
  } else {
    return false;
  }
}
 ?>
