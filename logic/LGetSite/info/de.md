<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since -
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2017
  -
 -->

Die LGetSite sammelt und modeliert die Daten für die Benutzerschicht.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Zuweisungsseite (Einsendungszuweisung)|
|Befehl| GET /tutorassign/user/:userid/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Studentenseite|
|Befehl| GET /student/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Accountseite|
|Befehl| GET /accountsettings/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Seite zum Erstellen von Übungsserien|
|Befehl| GET /createsheet/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Übersichtsseite|
|Befehl| GET /index/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Kursverwaltung|
|Befehl| GET /coursemanagement/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Plattformverwaltung|
|Befehl| GET /mainsettings/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Uploadseite für Studenten|
|Befehl| GET /upload/user/:userid/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Uploadseite für die Korrekturarchive der Tutoren|
|Befehl| GET /tutorupload/user/:userid/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage des Korrekturasistenten|
|Befehl| GET /markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Korrekturassistenten (ein Tutor ist vorausgewählt)|
|Befehl| GET /markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid/tutor/:tutorid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Korrekturassistenten (nur Einträge mit einem bestimmten Korrekturstatus)|
|Befehl| GET /markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid/status/:statusid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Korrekturassistenten (ein Tutor ist ausgewählt + ein ausgewählter Status)|
|Befehl| GET /markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid/tutor/:tutorid/status/:statusid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Korrekturassistenten 2.0 (veränderte Struktur)|
|Befehl| GET /markingtool/course/:courseid/sheet/:sheetid(/)|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Einsendungsverlaufs|
|Befehl| GET /uploadhistory/user/:userid/course/:courseid/exercisesheet/:sheetid/uploaduser/:uploaduserid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage des Auswahlbereits des Einsendungsverlaufs|
|Befehl| GET /uploadhistoryoptions/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Tutorenseite|
|Befehl| GET /tutor/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Adminseite|
|Befehl| GET /admin/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Dozentenseite|
|Befehl| GET /lecturer/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Gruppenseite|
|Befehl| GET /group/user/:userid/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Zulassungsübersicht|
|Befehl| GET /condition/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Zulassungsübersicht (mit Angabe der letzten anzuzeigenden Serie)|
|Befehl| GET /condition/user/:userid/course/:courseid/lastsheet/:maxsid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| die Datengrundlage der Zulassungsübersicht (mit der Angabe der anzuzeigenden Übungsserien)|
|Befehl| GET /condition/user/:userid/course/:courseid/firstsheet/:minsid/lastsheet/:maxsid|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LGetSite als lokales Objekt aufgerufen werden kann|


Stand 25.07.2017
