<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Navegación -->
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">
        Bienvenido a Mifirma Cliente
      </a>
    </div>
  </div>
</nav>
<!-- Fin de navegación -->

<!-- Contenedor principal -->
<div class="container">
  <!-- Inicia fila -->
  <div class="row">
    <!-- Columna de autentificación con Ldap -->
    <div class="col-xs-12 col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">Autentificación con protocolo Ldap</div>
        <div class="panel-body">
          <!-- formulario -->
          <form>
            <div class="form-group">
              <label for="email">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su correo electrónico...">
            </div>
            <div class="form-group">
              <label for="password">Contraseña</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña...">
            </div>
            <button type="submit" class="btn btn-primary">Ingresar</button>
          </form>
          <!-- Fin de formulario -->
        </div>
      </div>
    </div> <!-- Fin de columna de autentificación con Ldap -->


    <!-- Columna autentificación con firma digital -->
    <div class="col-xs-12 col-md-6">
      <div class="panel panel-primary">
        <div class="panel-heading">Autentificación con firma digital</div>
        <div class="panel-body">
          <!-- formulario -->
          <form>
            <div class="form-group">
              <label for="cedula">Cédula</label>
              <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Digite su número de cédula...">
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
          </form>
          <!-- Fin de formulario -->
        </div>
      </div>
    </div> <!-- Fin de columna autentificación con firma digital -->
  </div> <!-- Fin de fila -->
</div> <!-- Fin de contenedor principal -->
