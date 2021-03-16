# mysql
Clase que facilita el uso de mysql desde php

uso:

la clase se puede extender

extends Query

$this->consulta("*","clientes","Basededatos","Condiciones como CliID = 23 AND CliCodigo = '2340'");

o se puede usar directamente

$query = new Query;

$query->consulta("*","clientes","Basededatos","Condiciones como CliID = 23 AND CliCodigo = '2340'");

y de esa manera tener acceso a las funciones de consulta, insertar, reemplazar, eliminar, modificar etc.

para insertar y modificar se le envia un objeto o arrelgo con el nombre del cambo y el valor del campo


$datos = new stdClass();


$datos->CliNombre = "Carlos E Inglish";

$datos = array("CliNombre"=>"Carlos E English");

$query->insertar("clientes",$datos, "basededatos");


$query->modificar("clientes", $datos, "clave primaria", "nombre de clave primaria", "Base de datos", "Quien modifica")
