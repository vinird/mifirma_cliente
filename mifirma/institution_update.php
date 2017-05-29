<?php
/**
Actualiza los datos de la institución:
url, código, llave privada, llave pública
y certificado.

Además verifica que los valores sean 
correctos (validación a nivel de servidor)

@category Firma_Digital
@package  Mifirma
@author   Michael Vinicio Rodríguez Delgado <mvrd_17@hotmail.com>
@license  MIT <https://opensource.org/licenses/MIT>
@link     nubila.tech
*/

require_once "../../../../wp-load.php";
global $wpdb;

if (isset($_POST)) {
    if (Check_Not_empty($_POST)) {
        $check_pattern_ = Check_pattern($_POST);
        if ($check_pattern_['valid']) {
            $table_name = $wpdb->prefix . 'mifirma_institutions';
            $data = $wpdb->get_results('SELECT * FROM '.$table_name.'', OBJECT_K);
            if (count($data) > 0) {
                $data = Get_values($data);
                $wpdb->update($table_name, $_POST, array('id'=> $data[0]->id), array('%s','%s', '%s', '%s', '%s', '%s')); // Actualiza
                echo json_encode(['alert' => "Datos actualizados", "class" => "alert-success"]); // Mensaje
            } else {
                $wpdb->insert($table_name, $_POST, array('%s','%s', '%s', '%s', '%s', '%s')); // Inserta
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
 * 
 * @param array $data [datos de la consulta a la base de datos]
 * 
 * @return array       [valores ordenados]
 */
function Get_values($data)
{
    $values = [];
    foreach ($data as $key => $value) {
        array_push($values, $value);
    }
    return $values;
}

/**
 * [Revisa que los valores del post no esten vacidos]
 *
 * @param array $post [los datos del post]
 * 
 * @return boolean
 */
function Check_Not_empty($post)
{
    if ($post['mifirmacr_listen_url'] != ""
        && $post['mifirmacr_algorithm'] != ""
        && $post['mifirmacr_institution'] != ""
        && $post['mifirmacr_private_key'] != ""
        && $post['mifirmacr_public_certificate'] != ""
        && $post['mifirmacr_server_public_key'] != ""
    ) {
        return true;
    } else {
        return false;
    }
}

/**
 * [check_pattern description]
 * 
 * @param array $post [valores del post]
 * 
 * @return array       [contiene booleanos y html para mensaje]
 */
function Check_pattern($post)
{
    $url_exist = Url_exist($post['mifirmacr_listen_url']);
    $algorithm_valid = Algorithm_valid($post['mifirmacr_algorithm']);

    if ($url_exist == false) {
        $pattern_check_output = $pattern_check_output."<li>La Url no existe: ".$post['mifirmacr_listen_url']."</li>";
    }
    if ($algorithm_valid == false) {
        $pattern_check_output = $pattern_check_output."<li>".$post['mifirmacr_algorithm']." no es un algoritmo válido, utilice sha256</li>";
    }

    if ($url_exist && $algorithm_valid) {
        return  ["valid" => ture];
    } else {
        return  ["valid" => false, "output" => $pattern_check_output];
    }
}

/**
 * [Revisa si una url existe]
 * 
 * @param string $url [url para callback]
 * 
 * @return boolean     [si existe la url]
 */
function Url_exist($url)
{
    //se passar a URL existe
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_HEADER, 1);//get the header
    curl_setopt($c, CURLOPT_NOBODY, 1);//and *only* get the header
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);//get the response as a string from curl_exec(), rather than echoing it
    curl_setopt($c, CURLOPT_FRESH_CONNECT, 1);//don't use a cached version of the url
    if (!curl_exec($c)) {
        //echo $url.' inexists';
        return false;
    } else {
        //echo $url.' exists';
        return true;
    }
    //$httpcode=curl_getinfo($c,CURLINFO_HTTP_CODE);
    //return ($httpcode<400);
}

/**
 * [Revisa si el algitmo es válido]
 *
 * @param string $algorithm algoritmo
 * 
 * @return void
 */
function Algorithm_valid($algorithm)
{
    if ($algorithm == "sha256") {
        return true;
    } else {
        return false;
    }
}
?>
