# Repositorio para el plugin del proyecto mi firma / Wordpress

Este plugin permite, mediante un formulario personalizado, autentificar en wordpress utilizando un servidor ldap o un servicio de firma digital.

**Este plugin esta adaptado para funcionar únicamente con los servicios de https://mifirmacr.org/, no intente utilizarlo para conectar con otro servidor.**

****

## Bitácora de instalación

La instación del plugin debe realizarce en un proyecto de wordpress, en la carpeta de plugins:

`Proyecto/wp-admin/plugins/`

### Dependencias

* PHP ldap
* PHP curl

Además de estas dependencias es necesario tener el certificado perm del servidor [mifirmacr](https://mifirmacr.org/) instalado en el servidor cliente en donde se encuentra el proyecto de wordpress.

### Instalación

* Se debe descargar el archivo en la carpeta /plugins de wordpress y descomprimirlo.
* Se debe activar el plugin en el panel administrativo de wordpress.

Al instalarlo, el plugin va a crear las tablas necesarias en la base de datos para administrar la información.

### Configuración inicial

Ingresar los parámetros de la institución en la sección de mi Mi firma en el panel administrativo. Los datos que debe ingresar son; la url para las notificaciones que provienen del servidor de mifirmacr, el código de la institución, la llave privada de la institución, la llave pública de la institución y el certificado de la institución.

### Login con ldap o firma digital

Para ingresar al formulario de autentificación con ldap o firma digital se debe accesar mediante la siguiente dirección:

`Proyect/wp-admin`

### Login con wordpress

Al instalar el plugin la dirección de autentificación de wordpress se sobrescribe para dar paso a la autentificación con ldap y firma digital, para ingresar al formulario de autentificación de wordpress se debe accesar a la siguiente dirección:

`Proyecto/wp-login.php`
