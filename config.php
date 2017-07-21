<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

define('APPLICATION_NAME', 'JANE');
define('CREDENTIALS_PATH', '~/.credentials/drive-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');

define('DRIVE_FOLDER_ID', getenv('DRIVE_FOLDER_ID'));

/* If modifying these scopes, 
 * delete your previously saved credentials
 * at ~/.credentials/drive-php-quickstart.json 
 * */
define('SCOPES', implode(' ', array(
  Google_Service_Drive::DRIVE)
));

if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}
date_default_timezone_set("America/Bogota");

define ("DAYS", serialize (array (
	"Domingo",
	"Lunes",
	"Martes",
	"Miercoles",
	"Jueves",
	"Viernes",
	"Sabado"
)));
define ("MONTHS", serialize (array (
	null,
	"Enero",
	"Febrero",
	"Marzo",
	"Abril",
	"Mayo",
	"Junio",
	"Julio",
	"Agosto",
	"Septiembre",
	"Octubre",
	"Noviembre",
	"Diciembre"
)));
