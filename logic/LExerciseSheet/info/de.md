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

Die LExerciseSheet bietet Befehle zum Behandeln von Übungsserien an.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Beschreibung| erstellt eine neue Übungsserie|
|Befehl| POST /exercisesheet|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ändert eine vorhandene Übungsserie|
|Befehl| PUT /exercisesheet/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittelt die Dateiadresse des Aufgabenblattes einer Übungsserie|
|Befehl| GET /exercisesheet/exercisesheet/:sheetid/url|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| liefert eine einzelne Übungsserie|
|Befehl| GET /exercisesheet/exercisesheet/:sheetid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| ermittet eine Übungsserie mit den dazu gehörigen Aufgaben|
|Befehl| GET /exercisesheet/exercisesheet/:sheetid/exercise|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| liefert alle Übungsserien einer Veranstaltung|
|Befehl| GET /exercisesheet/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Beschreibung| liefert alle Übungsserien einer Veranstaltung und die zugehörigen Aufgaben|
|Befehl| GET /exercisesheet/course/:courseid/exercise|
|Eingabetyp| -|
|Ausgabetyp| -|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| DELETE /FS/sheetFileAddress+|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| DELETE /FS/sampleFileAddress+|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| DELETE /DB/exercisesheet/exercisesheet/:sheetid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/exercisesheet/course/:courseid/exercise|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/exercisesheet/course/:courseid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/exercisesheet/exercisesheet/:sheetid/exercise|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /DB/exercisesheet/exercisesheet/:sheetid/url|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| PUT /DB/exercisesheet/exercisesheet/:sheetid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /DB/exercisesheet|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| GET /hash/:hash|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /DB/file|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:----- |
|Ziel| LController|
|Befehl| POST /FS/file|
|Beschreibung| für den Befehl controller|

||deleteFile|
| :----------- |:----- |
|Ziel| LFile|
|Befehl| DELETE /file/:fileId|
|Beschreibung| für den Befehl deleteFile|

||getExerciseSheet|
| :----------- |:----- |
|Ziel| DBExerciseSheet|
|Befehl| GET /exercisesheet/exercisesheet/:sheetId|
|Beschreibung| für den Befehl getExerciseSheet|

||deleteExerciseSheet|
| :----------- |:----- |
|Ziel| DBExerciseSheet|
|Befehl| DELETE /exercisesheet/exercisesheet/:sheetId|
|Beschreibung| für den Befehl deleteExerciseSheet|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LExerciseSheet als lokales Objekt aufgerufen werden kann|


Stand 25.07.2017
