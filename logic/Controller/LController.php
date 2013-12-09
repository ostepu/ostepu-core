<?php 

require 'Slim/Slim.php';
include 'include/Helpers.php';

\Slim\Slim::registerAutoloader();

$_app = new \Slim\Slim();

$links = array();
setLinks('config.ini');
$DBController = $links["DBControl"];	
$FSController = $links["FSControl"];

$_app->get ('/:string+', function($string) use($_app) 
{
	if ($string[0] == "DB") {
		unset($string[0]);
		$URI = "";
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(LRequest($URI, GET, NULL));		//Rückgabe http_get??	
	}
	elseif ($string[0] == "FS") {
		unset($string[0]);
		$URI = "";
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(FSRequest($URI, GET, $body));		//Rückgabe http_get??
	}
	else {
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(UIRequest($URI, GET, NULL));		//Rückgabe http_get??	
	}
});

$_app->post ('/:string+', function($string) use($_app) 
{
	$body = \Slim\Slim::getInstance()->request()->getBody();
	if ($string[0] == "DB") {
		unset($string[0]);
		$URI = "";
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(LRequest($URI, POST, $body));			
	}
	elseif ($string[0] == "FS") {
		unset($string[0]);
		$URI = "";
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(FSRequest($URI, POST, $body));		
	}
	else {
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(UIRequest($URI, POST, $body));		
	}
});

$_app->put ('/:string+', function($string) use($_app) 
{
	$body = \Slim\Slim::getInstance()->request()->getBody();
	if ($string[0] == "DB") {
		unset($string[0]);
		$URI = "";
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(LRequest($URI, PUT, $body));		
	}
	elseif ($string[0] == "FS") {
		unset($string[0]);
		$URI = "";
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(FSRequest($URI, PUT, $body));		
	}
	else {
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(UIRequest($URI, PUT, $body));		
	}
});

$_app->delete ('/:string+', function($string) use($_app) 
{
	if ($string[0] == "DB") {
		unset($string[0]);
		$URI = "";
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(LRequest($URI, DELETE, NULL));		
	}
	elseif ($string[0] == "FS") {
		unset($string[0]);
		$URI = "";
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(FSRequest($URI, DELETE, NULL));	
	}
	else {
		foreach ($string as $str) {
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(UIRequest($URI, DELETE, NULL));	
	}
});

/**
 * handle requests from userinterface to logic
 *
 * @param (param)
 */
private function UIRequest($URI, $method, $data) 
{
	$component = explode( '/', $URI); 
	$componentURL = $links[component[1]];				//oder 0?
	$URL = $componentURL.$URI;
	
	return chooseCaseOfRequest($URL, $method, $data);
}

/**
 * handle requests from logic to database
 *
 * @param (param)
 */
private function LRequest($URI, $method, $data) 
{
	$URL = $DBController.$URI;
	return chooseCaseOfRequest($URL, $method, $data);
}

/**
 * handle requests from logic to filesystem
 *
 * @param (param)
 */
private function FSRequest($URI, $method, $data) 
{
	$URL = $FSController.$URI;
	return chooseCaseOfRequest($URL, $method, $data);
}

/**
 * pick out the config.ini file and store all addresses in $links
 *
 * @param (param)
 */
private function setLinks($dataName) 
{
	$datei = file($dataName);
	$explodedRow = array();

	foreach ($datei AS $row) {
		$explodedRow = explode(' = ' , $row);			// Trenner in Config.ini definieren
		$links["$explodedRow[0]"] = $explodedRow[1];
	}
}

/**
 * select the case of the request based on the available method
 * and send the selected on
 * 
 * @param (param)
 */
private function chooseCaseOfRequest($URL, $method, $data) 
{
	switch ($method) {
		case 'POST' :
			return http_post_data($URL, $data);
			break;
			
		case 'PUT' :			
			return http_put_data($URL, $data);
			break;
			
		case 'GET' :
			return http_get($URL);
			break;
			
		case 'DELETE' :
			return http_delete($URL);
			break;
			
		default :
			return "Fehler";			//Fehlermeldung
	}
}

?>