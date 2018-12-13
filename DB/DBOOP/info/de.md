<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since 0.4.4
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2017
  -
 -->

Die DBOOP ermöglicht den Zugriff auf die `Testcase_X` Tabellen der Datenbank, diese verwalten die Testfälle für die automatische Vorkorrektur der LOOP. Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| installiert die Komponente in die Veranstaltung|
|Befehl| post (/:pre)/course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| entfernt die Komponente aus der Veranstaltung|
|Befehl| delete (/:pre)/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| fügt einen neuen Testfall ein|
|Befehl| post (/:pre)/insert|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| sperrt den nächsten unbearbeiteten Testfall und liefert ihn zurück|
|Befehl| get (/:pre)/pop|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| editiert einen Testfall|
|Befehl| put (/:pre)/testcase(/testcase)/:testcaseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittelt einen Testfall zu einer Einsendung|
|Befehl| get (/:pre)/testcase/submission/:sid/course/:cid|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||out|
| :----------- |:----- |
|Ziel| DBQuery2|
|Befehl| POST /query/:abc|
|Beschreibung| über diesen Ausgang werden alle Datenbankanfragen ausgeführt|

||getCourse|
| :----------- |:----- |
|Ziel| DBQuery2|
|Befehl| GET /query/procedure/DBCourseGetCourse/:courseid|
|Beschreibung| für den Befehl getCourse|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBOOP als lokales Objekt aufgerufen werden kann|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 25.07.2017
