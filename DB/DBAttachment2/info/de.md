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

Die DBAttachment2 ermöglicht den Zugriff auf die `Attachment_X` Tabellen der Datenbank. Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| entfernt die Komponente aus der Veranstaltung|
|Befehl| delete (/:pre)/course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| prüft, ob die Komponente korrekt in die Veranstaltung installiert wurde|
|Befehl| get (/:pre)/link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| gibt die Anhänge der Veranstaltung zurück|
|Befehl| get (/:pre)/attachment/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| fügt einen neuen Eintrag hinzu|
|Befehl| post (/:pre)/attachment|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| editiert einen einzelnen Anhang|
|Befehl| put (/:pre)/attachment(/attachment)/:aid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittelt alle Anhänge einer Veranstaltung|
|Befehl| get (/:pre)/attachment/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| gibt eine einzelne Veranstaltung aus|
|Befehl| get (/:pre)/attachment(/attachment)/:aid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittelt alle Anhänge einer Übungsserie|
|Befehl| get (/:pre)/attachment/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt einen einzelnen Anhang|
|Befehl| delete (/:pre)/attachment(/attachment)/:aid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| installiert die Komponente in die Veranstaltung|
|Befehl| post (/:pre)/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||out|
| :----------- |:----- |
|Ziel| DBQuery2|
|Befehl| POST /query|
|Beschreibung| über diesen Ausgang werden die Anfragen an die Datenbank gestellt|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBAttachment2 als lokales Objekt aufgerufen werden kann|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 25.07.2017
