Diese Komponente gehört zum Korrekturassistenten 2.0 und ermöglicht die Bearbeitung von Korrekturen direkt in der Oberfläche der Plattform.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||getButton|
| :----------- |:----- |
|Beschreibung| gibt den HTML-Code für die Schaltfläche zum Korrekturassistenten aus|
|Befehl| GET /view/button/entry/course/:cid/sheet/:sid|
|Eingabetyp| -|
|Ausgabetyp| binary|

||getMarkingTool2Page|
| :----------- |:----- |
|Beschreibung| zeichnet die Seite des Korrekturassistenten|
|Befehl| GET /page/markingtool2/course/:cid/exercisesheet/:sid|
|Eingabetyp| -|
|Ausgabetyp| binary|
|||
||Patzhalter|
|Name|cid|
|Regex|%^([0-9_]+)$%|
|Name|sid|
|Regex|%^([0-9_]+)$%|

||getPing|
| :----------- |:----- |
|Beschreibung| liefert eine einfache Antwort (mit Loginprüfung)|
|Befehl| GET /api/ping|
|Eingabetyp| -|
|Ausgabetyp| binary|

||getTest|
| :----------- |:----- |
|Beschreibung| liefert eine einfache Antwort (mit Loginprüfung)|
|Befehl| GET /api/test|
|Eingabetyp| -|
|Ausgabetyp| binary|

||postUpload|
| :----------- |:----- |
|Beschreibung| speichert die Änderungen an den Korrekturen|
|Befehl| POST /api/upload/course/:cid/exercisesheet/:sid|
|Eingabetyp| -|
|Ausgabetyp| binary|
|||
||Patzhalter|
|Name|cid|
|Regex|%^([0-9_]+)$%|
|Name|sid|
|Regex|%^([0-9_]+)$%|

||getUnknownMode|
| :----------- |:----- |
|Beschreibung| ein unbekannter Api-Aufruf wird verwendet|
|Befehl| GET /api/:mode|
|Eingabetyp| -|
|Ausgabetyp| binary|

||language|
| :----------- |:----- |
|Beschreibung| lädt die Sprachdatei (korrekt???)|
|Befehl| GET /lang|
|Eingabetyp| -|
|Ausgabetyp| binary|

||getUnknownApiCall|
| :----------- |:----- |
|Beschreibung| wenn kein anderer Aufruf passt, dann wird dieser Befehl aufgerufen|
|Befehl| GET,POST,DELETE,PUT /:path+|
|Eingabetyp| -|
|Ausgabetyp| binary|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||getMarkingToolData|
| :----------- |:----- |
|Ziel| LGetSite|
|Befehl| GET /markingtool/course/:cid/sheet/:sid|
|Beschreibung| zum Abrufen der benötigten Daten|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit UIMarkingTool2 als lokales Objekt aufgerufen werden kann|

|Ausgang|getSheetNavigation|
| :----------- |:----- |
|Ziel| UIExerciseSheetLecturer|
|| GET /view/button/entry/course/:cid/sheet/:sid (150)|

|Ausgang|getSheetNavigation|
| :----------- |:----- |
|Ziel| UIExerciseSheetTutor|
|| GET /view/button/entry/course/:cid/sheet/:sid (150)|

|Ausgang|getContent|
| :----------- |:----- |
|Ziel| CContent|
|| GET /content/markingtool2/css/MarkingTool2.css|
|| GET /content/markingtool2/img/computer-icon.png|
|| GET /content/markingtool2/img/ok-icon.png|
|| GET /content/markingtool2/img/server-icon.png|
|| GET /content/markingtool2/js/marking_tool2_editor.js|
|| GET /content/markingtool2/js/helper_events.js|
|| GET /content/markingtool2/js/helper_queue.js|
|| GET /content/markingtool2/js/helper_html.js|
|| GET /content/markingtool2/js/helper_updateindicator.js|
|| GET /content/markingtool2/js/helper_updateproperty.js|
|| GET /content/markingtool2/js/helper_updateobject.js|
|| GET /content/markingtool2/js/helper_updatefactory.js|
|| GET /content/markingtool2/js/helper_cookie.js|


Stand 25.07.2017
