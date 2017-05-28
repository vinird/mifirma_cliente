<?php
include_once('../auth/auth.php');
if (isset($_GET)) {
  create_local_user('usuario_'.$_GET['data'], $_GET['data'], $_GET['role']);
}
 ?>
