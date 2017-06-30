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
 -->

Die DBOOP ermöglicht den Zugriff auf die `Testcase_X` Tabellen der Datenbank, diese verwalten die Testfälle für die automatische Vorkorrektur der LOOP. Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt.

## Eingänge
---------------

|||
| :----------- |:-----: |
|Beschreibung| installiert die Komponente in die Veranstaltung|
|Befehl| post<br>(/:pre)/course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente aus der Veranstaltung|
|Befehl| delete<br>(/:pre)/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| fügt einen neuen Testfall ein|
|Befehl| post<br>(/:pre)/insert|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| sperrt den nächsten unbearbeiteten Testfall und liefert ihn zurück|
|Befehl| get<br>(/:pre)/pop|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| editiert einen Testfall|
|Befehl| put<br>(/:pre)/testcase(/testcase)/:testcaseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| ermittelt einen Testfall zu einer Einsendung|
|Befehl| get<br>(/:pre)/testcase/submission/:sid/course/:cid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||out|
| :----------- |:-----: |
|Ziel| DBQuery2|
|Befehl| POST<br>/query/:abc|
|Beschreibung| über diesen Ausgang werden alle Datenbankanfragen ausgeführt|

||getCourse|
| :----------- |:-----: |
|Ziel| DBQuery2|
|Befehl| GET<br>/query/procedure/DBCourseGetCourse/:courseid|
|Beschreibung| für den Befehl getCourse|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBOOP als lokales Objekt aufgerufen werden kann|

|Ausgang|getDescFiles|
| :----------- |:-----: |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 30.06.2017
