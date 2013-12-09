<?php 

require 'Slim/Slim.php';
include 'include/Helpers.php';

\Slim\Slim::registerAutoloader();

$_app = new \Slim\Slim();

$links = array();
setLinks('config.ini');
$DBController = $links["DBControl"];	


$_app->get ('/:string+', function($string) use($_app)
{
	if ($string[0] == "DB")
	{
		unset($string[0]);
		$URI = "";
		foreach ($string as $str)
		{
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(LRequest($URI, GET, NULL));		//Rückgabe http_get??	
	}
	else 
	{
		foreach ($string as $str)
		{
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(UIRequest($URI, GET, NULL));		//Rückgabe http_get??	
	}
});

$_app->post ('/:string+', function($string) use($_app)
{
	$body = \Slim\Slim::getInstance()->request()->getBody();
	if ($string[0] == "DB")
	{
		unset($string[0]);
		$URI = "";
		foreach ($string as $str)
		{
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(LRequest($URI, POST, $body));		//Rückgabe http_get??	
	}
	else 
	{
		foreach ($string as $str)
		{
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(UIRequest($URI, POST, $body));		//Rückgabe http_get??	
	}
});

$_app->put ('/:string+', function($string) use($_app)
{
	$body = \Slim\Slim::getInstance()->request()->getBody();
	if ($string[0] == "DB")
	{
		unset($string[0]);
		$URI = "";
		foreach ($string as $str)
		{
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(LRequest($URI, PUT, $body));		//Rückgabe http_get??	
	}
	else 
	{
		foreach ($string as $str)
		{
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(UIRequest($URI, PUT, $body));		//Rückgabe http_get??	
	}
});

$_app->delete ('/:string+', function($string) use($_app)
{
	if ($string[0] == "DB")
	{
		unset($string[0]);
		$URI = "";
		foreach ($string as $str)
		{
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(LRequest($URI, DELETE, NULL));		//Rückgabe http_get??	
	}
	else 
	{
		foreach ($string as $str)
		{
			$URI = $URI.'/'.$str;
		}
		$_app->response->SetBody(UIRequest($URI, DELETE, NULL));		//Rückgabe http_get??	
	}
});

private function UIRequest($URI, $method, $data)
{
	$component = explode( '/', $URI); 
	$componentURL = $links[component[1]];				//oder 0?
	$URL = $componentURL.$URI;
	
	switch ($method)
	{
		case 'POST' : 
			return http_post_data($URL, $data);
		case 'PUT' : 
			return http_put_data($URL, $data);
		case 'GET' :
			return http_get($URL);
		case 'DELETE' :
			return http_delete($URL);
		default :
			return "du hast Scheiße gebaut"				//Fehlermeldung
	}
}

private function LRequest($URI, $method, $data)
{
	$URL = $DBController.$URI;
	
	switch ($method)
	{
		case 'POST' : 
			return http_post_data($URL, $data);
		case 'PUT' : 
			return http_put_data($URL, $data);
		case 'GET' :
			return http_get($URL);
		case 'DELETE' :
			return http_delete($URL);
		default :
			return "du hast Scheiße gebaut"				//Fehlermeldung
	}
}

private function setLinks($dataName)
{
	$datei = file($dataName);
	$explodedRow = array();

	foreach($datei AS $row)
	{
		$explodedRow = explode(' = ' , $row);			// Trenner in Config.ini definieren
		$links["$explodedRow[0]"] = $explodedRow[1];
	}

}
?>