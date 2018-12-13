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

Die DBProcess ermöglicht den Zugriff auf die `Process_X` Tabellen der Datenbank, dabei stellen diese Einträge ein Vorgang dar. Es werden aufzurufende Komponenten mit Aufrufparametern versehen. Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| fragt alle Einträge zur gegeben Übungsserie ab|
|Befehl| get (/:pre)/process/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt einen Eintrag anhand seiner ID|
|Befehl| delete (/:pre)/process(/process)/:processid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittet alle Einträge zu einer Veranstaltung|
|Befehl| get (/:pre)/process/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| editiert einen Eintrag anhand seiner ID|
|Befehl| put (/:pre)/process(/process)/:processid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| installiert die Komponente in die gegebene Veranstaltung|
|Befehl| post (/:pre)/course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| gibt einen einzelnen Eintrag anhand seiner ID zurück|
|Befehl| get (/:pre)/process(/process)/:processid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| fügt einen neuen Eintrag ein|
|Befehl| post (/:pre)/process|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| prüft, ob die Komponente korrekt in diese Veranstaltung installiert wurde/ist|
|Befehl| get (/:pre)/link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt die Komponente aus der Veranstaltung|
|Befehl| delete (/:pre)/course(/course)/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittelt alle Einträge anhand der ID einer Übungsaufgabe|
|Befehl| get (/:pre)/process/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||out|
| :----------- |:----- |
|Ziel| DBQuery2|
|Befehl| POST /query|
|Beschreibung| über diesen Ausgang werden alle Anfragen an die Datenbank gestellt|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBProcess als lokales Objekt aufgerufen werden kann|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 25.07.2017
