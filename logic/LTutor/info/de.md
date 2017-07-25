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

Die LTutor bietet Aufrufe zum Verteilen der Einsendungen auf Kontrolleure, dem Abrufen von Korrekturarchiven und dem Behandeln der hochgeladenen Korrekturarchive an.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| dieser Befehl verteilt die Einsendungen der Studenten gleichmäßig auf die übergebenen Tutoren (Aufgabenweise)|
|Befehl| POST /tutor/auto/exercise/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| dieser Befehl verteilt die Einsendungen der Studenten gleichmäßig auf die übergebenen Tutoren (Gruppenweise)|
|Befehl| POST /tutor/auto/group/course/:courseid/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| erzeugt ein Korrekturarchiv mit den zugewiesenen Korrekturaufträgen|
|Befehl| GET /tutor/user/:userid/exercisesheet/:sheetid(/status/:status)(/)|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| erzeugt ein Korrekturarchiv mit den zugewiesenen Korrekturaufträgen und fügt die Namen der Studenten ein|
|Befehl| GET /tutor/user/:userid/exercisesheet/:sheetid(/status/:status)(/withnames)(/)|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| bearbeitet ein Korrekturarchiv und übernimmt die Änderungen aus diesem ins System|
|Befehl| POST /tutor/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| erzeugt ein Korrekturarchiv anhand der übermittelten Marking-Daten|
|Befehl| POST /tutor/archive/user/:userid/exercisesheet/:sheetid(/)|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| erzeugt ein Korrekturarchiv anhand der übermittelten Marking-Daten und fügt die Namen der Studenten ein|
|Befehl| POST /tutor/archive/user/:userid/exercisesheet/:sheetid/withnames(/)|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| PUT /DB/marking/:marking|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /DB/file|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/file/hash/:hash|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /FS/file|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/user:userid|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /FS/path+|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /FS/zip|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/submission/submission/:submissionid|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/user/user/:userid|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/exercise/exercisesheet/:sheetid|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/marking/exercisesheet/:sheetid/tutor/:userid|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /DB/marking|
|Beschreibung| über diesen Ausgang werden diverse Aufrufe bearbeitet|

||postTransaction|
| :----------- |:----- |
|Ziel| DBTransaction|
|Befehl| POST /transaction/exercisesheet/:sheetid|
|Beschreibung| zum Erzeugen einer neuen Transaktionsnummer|

||out2|
| :----------- |:----- |
|Ziel| DBQuery2|
|Befehl| POST /query|
|Beschreibung| über diesen Ausgang werden alle übrigen Anfragen behandelt (DEPRECATED)|

||getCourse|
| :----------- |:----- |
|Ziel| DBCourse|
|Befehl| GET /course/exercisesheet/:esid|
|Beschreibung| zum Abrufen einer Veranstaltung anhand seiner Übungsnummer|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LTutor als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|postCourse|
| :----------- |:----- |
|Ziel| LCourse|
|Beschreibung| wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden|


Stand 25.07.2017
