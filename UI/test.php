<?php

require_once(dirname(__FILE__).'/../install/segments/Anfragegraph/Anfragegraph.php');
require_once(dirname(__FILE__).'/include/Helpers.php');

echo '<html><head><meta charset="utf-8">';
echo '<link rel="stylesheet" type="text/css" href="../install/css/format.css">';
echo '<title></title></head><body>';

echo "Diese Daten kamen über den Aufruf:<br>";
echo "<div>GET: </br>";
echo Anfragegraph::prettyPrint(json_encode($_GET))."</div><br>";

echo "<div>POST: </br>";
echo Anfragegraph::prettyPrint(json_encode($_POST))."</div><br>";

$tid = $_GET['tid'];

$URI = "http://localhost/uebungsplattform/DB/DBTransaction/transaction/authentication/redirect/transaction/".$tid;
echo "<div>Wenn man nun <b>GET ".$URI."</b> aufruft</div><br>";

$transaction = http_get($URI, false, $message);

echo "<div>";
if ($message == 200){
    echo 'CONTENT: <div>'.Anfragegraph::prettyPrint($transaction).'</div><br>';
}
echo 'STATUS: '.$message."<br>";
echo "</div><br>";

if ($message == 200){
    echo "Jetzt wollen wir die Daten betrachten<br>";
    $transaction = json_decode($transaction,true);
    $content = json_decode($transaction['content'],true);
    unset($content['user']['courses'][0]['course']['settings']);

    echo "NutzerID: ".$content['user']['id']."<br>";
    echo "Übungsserie-ID: ".$content['esid']."<br>";
    echo "Vernstaltung-ID: ".$content['user']['courses'][0]['course']['id']."<br>";
    echo "Nutzername: ".$content['user']['userName']."<br>";
    echo "Vorname: ".$content['user']['firstName']."<br>";
    echo "Nachname: ".$content['user']['lastName']."<br>";
    echo "E-Mail: ".$content['user']['email']."<br>";
    echo "Veranstaltung-Name: ".$content['user']['courses'][0]['course']['name']."<br>";
    echo "Veranstaltung-Semester: ".$content['user']['courses'][0]['course']['semester']."<br>";
    echo "Nutzerstatus (Student, Admin, etc): ".$content['user']['courses'][0]['status']."<br>";
}
echo '</body></html>';
